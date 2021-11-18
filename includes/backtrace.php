<?php
/**
 * Набор функций для работы с ошибками
 *
 * @package    DIAFAN.CMS
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
 * Backtrace
 *
 * Класс для работы с ошибками
 */
class Backtrace
{
	/**
	 * @var string перечень ошибок
	 */
	static private $is_init;

	/**
	 * @var array массив ошибок
	 */
	static public $errors = array();

	/**
	 * @var mixed обработчик ошибок
	 */
	static private $error_handler;

	/**
	 * @var callable обработчик исключений
	 */
	static private $exception_handler;

	/**
	 * @var callable callback-функция, вызываемая после обработки исключения
	 */
	static public $exception_callback;

	/**
	 * @var string вывод ошибок
	 */
	static private $display_errors;

	/**
	 * @var string перечень ошибок
	 */
	static private $error_level;

	/**
	 * @var array функции, выполняющиеся по завершению скрипта
	 */
	static private $shutdown_functions = array();

	/**
	 * @var string ключ функции, выполняющиеся по завершению скрипта
	 */
	static private $key_shutdown_function;

	/**
	 * Инициализация
	 *
	 * @return void
	 */
	static public function init()
	{
		if(! is_null(self::$is_init))
		{
			return;
		}
		self::$is_init = true;

		self::$errors = array();

		// регистрация ошибок
		self::$error_handler = set_error_handler(array('Backtrace', 'other_error_catcher'));
		self::$exception_handler = set_exception_handler(array('Backtrace', 'other_exception_catcher'));
		self::$display_errors = ini_get('display_errors'); ini_set('display_errors', 'on');
		self::$error_level = error_reporting(); error_reporting(E_ALL | E_STRICT);
		if (function_exists("xdebug_disable"))
		{
			xdebug_disable();
		}

		self::$key_shutdown_function = Dev::register_shutdown_function(array('Backtrace', 'shutdown'));
	}

	/**
	 * Деинициализация
	 *
	 * @return void
	 */
	static public function deinit()
	{
		if(! self::$is_init)
		{
			return;
		}
		self::$is_init = null;

		// регистрация ошибок
		restore_error_handler();
		if(self::$error_handler)
		{
			set_error_handler(self::$error_handler);
			self::$error_handler = null;
		}
		restore_exception_handler();
		if(self::$exception_handler)
		{
			set_exception_handler(self::$exception_handler);
			self::$exception_handler = null;
		}
		if(self::$display_errors)
		{
			ini_set('display_errors', self::$display_errors);
			self::$display_errors = null;
		}
		if(! is_null(self::$error_level))
		{
			error_reporting(self::$error_level);
			self::$error_level = null;
		}
		self::$exception_callback = null;

		self::$shutdown_functions = array();
		Dev::unregister_shutdown_function(self::$key_shutdown_function);
		self::$key_shutdown_function = null;
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
		{ // TO_DO: Fatal ERROR: if(isset($error) && in_array($error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR))) { }
			self::$errors[] = array('line' => $error['file'].':'.$error['line'], 'message' => $error['message'], 'trace' => '');
			Dev::log_error($error['message'], $error['file'], $error['line'], '');
		}
	}

	static public function exception($exception)
	{
		self::other_exception_catcher($exception);
	}

	static public function other_exception_catcher($exception)
	{
		$backtrace = $exception->getTrace();
		$file = '';
		$line = '';
		$message = $exception->getMessage();

		if(isset($backtrace[0]['file']) && isset( $backtrace[0]['line']))
		{
			$file = $backtrace[0]['file'];
			$line = $backtrace[0]['line'];
		}
		if(strpos($message, 'unable to connect to') !== false || strpos($message, 'php_network_getaddresses') !== false)
		{
			// return true;
		}
		array_unshift($backtrace, array("file" => "", "line" => 0, "function" => "", "class" => "", "type" => "", "args" => array()));
		if($trace = self::backtrace_to_string($backtrace))
		{
			self::warning($message, $file, $line, $trace);
		}
		if(self::$exception_callback)
		{
			if (is_callable(self::$exception_callback))
			{
				return call_user_func_array(self::$exception_callback, array($exception));
			}
		}
		else print self::print_errors();
	}

	static public function other_error_catcher($errno, $errstr, $errfile, $errline)
	{
		// молчим, если ошибку подавили оператором @
    if (0 === error_reporting()) {
        return true;
    }

		$backtrace = debug_backtrace();
		$file = '';
		$line = '';
		$message = $errstr;

		if(isset($backtrace[0]['file']) && isset( $backtrace[0]['line']))
		{
			$file = $backtrace[0]['file'];
			$line = $backtrace[0]['line'];
		}
		if(strpos($message, 'unable to connect to') !== false || strpos($message, 'php_network_getaddresses') !== false)
		{
			// return true;
		}
		if($trace = self::backtrace_to_string($backtrace))
		{
			self::warning($message, $file, $line, $trace);
		}
		return true;
	}

	/**
	 * Форматирует стек вызова функций в строку
	 *
	 * @param array $backtrace стек вызова функций
	 * @return array
	 */
	static public function backtrace_to_string($backtrace)
	{
		// Iterate backtrace
		$calls = array ();
		foreach($backtrace as $i => $call)
		{
			if ($i == 0)
			{
				continue;
			}

			if (! isset( $call['file'] ))
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
			$calls[$i] = sprintf('#%d  %s(%s) called at [%s]', $i, $function, $params, $location);
		}

		return $calls;
	}

	static public function warning($message, $file, $line, $trace)
	{
			$errno = ($file ? $file.':' : '').$line;
			self::$errors[] = array('line' => $errno, 'message' => $message, 'trace' => $trace);
			Dev::log_error($message, $file, $line, $trace);
	}

	static public function print_errors()
	{
		if (! count(self::$errors))
			return '';

		$return = array();

		foreach(self::$errors as $key => $error)
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

			if(is_array($e['trace'])) $e['trace'] = implode(PHP_EOL, $e['trace']);
			else $e['trace'] = $e['trace'];

			if(strpos($e['trace'], 'mysqli_connect'))
			{
				$e['trace'] = preg_replace('/mysqli_connect\((.*)\)/', 'mysqli_connect(...)', $e['trace']);
				$url = parse_url(DB_URL);
				unset($url["scheme"]);
				$url["path"] = substr($url["path"], 1);
				$e['message'] = str_replace($url, '...', $e['message']);
			}
			$return[] = sprintf('Error #%d: %s  called at [%s] %s', ($key+1), $e['message'], $e['line'], PHP_EOL.$e['trace']);
		}

		return implode(PHP_EOL.PHP_EOL, $return);
	}
}

/**
 * Backtrace_exception
 *
 * Исключение для работы с ошибками
 */
class Backtrace_exception extends Exception{}
