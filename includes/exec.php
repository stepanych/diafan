<?php
/**
 * Каркас для обработки фоновых запросов модуля
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
 * Exec
 *
 * Абстрактный класс для фоновых запросов
 */
abstract class Exec extends Diafan
{
	/**
   * @var string result
   */
	const RESULT = "result";

	/**
   * @var string data
   */
	const DATA = "data";

	/**
   * @var string content
   */
	const CONTENT = "content";

	/**
   * @var string query
   */
	const QUERY = "query";

	/**
   * @var string success
   */
	const SUCCESS = "success";

	/**
   * @var string error
   */
	const ERROR = "error";

	/**
	 * @var integer максимальное количество служебных фоновых процессов
	 */
	const MAX_SYS_EXECUTE = 5; // TO_DO: 5 = (резерв::1)  + (executable::tick + executable::tick_check + crontab::cron + crontab::execute);

	/**
	 * @var integer шаг тика в секундах
	 */
	const TICK_STEP = 60; // 1 минута
	// TO_DO: TICK_STEP должне быть равен 1 минуте (шаг CRON)

	/**
	 * @var integer шаг тика в секундах
	 */
	const TICK_DELAY = 5; // отсрочка в секундах: оставляем 5 секунд на завершение кода
	// TO_DO: TICK_DELAY не может быть больше или равной разницы между
	// (Executable_inc::MIN_GLOBAL_EXECUTE - (Executable_inc::TICK_MSEC / 1000000))

	/**
	 * @var integer время начала работы фонового запроса
	 */
	protected $timestart;

	/**
	 * @var string полученный электронный ключ при обращении
	 */
	private $token;

	/**
	 * @var object характеристики текущего фонового процесса
	 */
	protected $exec;

	/**
	 * @var string имя модуля
	 */
	protected $module;

	/**
	 * @var string имя метода
	 */
	protected $method;

	/**
	 * @var array массив значений POST-запроса
	 */
	protected $post;

	/**
	 * @var string идентификатор фонового процесса
	 */
	protected $id;

	/**
	 * @var integer номер итерации
	 */
	protected $iteration;

	/**
	 * @var integer максимальный номер итерации
	 */
	protected $max_iteration;

	/**
	 * @var boolean принудительное исполнение
	 */
	protected $forced;

	/**
	 * @var boolean приоритет исполнения
	 */
	protected $prior;

	/**
	 * @var boolean считать запись мусором по завершению процесса
	 */
	protected $trash;

	/**
	 * @var boolean отклонять процесс пока работает аналогичный процесс
	 */
	protected $once;

	/**
	 * @var integer описание фонового процесса
	 */
	protected $text;

	/**
	 * @var string стек вызовов функций инициализации
	 */
	private $init_backtrace;

	/**
	 * @var boolean административная часть сайта
	 */
	protected $is_admin;

	/**
	 * @var string псевдоссылка
	 */
	protected $rewrite;

	/**
	 * @var boolean повторить фоновый процесс
	 */
	protected $repeat;

	/**
	 * @var boolean статус выполнения фонового процесса
	 */
	private $execute;

	/**
	 * @var array полученный после обработки данных результат
	 */
	protected $result = array();

	/**
	 * @var array ответ
	 */
	protected $answer;

	/**
	 * @var boolean отдавать ответ только запросам AJAX
	 */
	private $ajax = true;

	/**
	 * @var boolean требуется верификация
	 */
	protected $verify = true;

	/**
	 * @var boolean при недопустимых запросах отдавать 404
	 */
	private $page_404 = true;

	/**
	 * @var boolean ответ API только по протоколу HTTPS
	 */
	private $only_https = false;

	/**
	 * @var integer максимальное время выполнение скрипта, указанное в секундах
	 */
	protected $max_execution_time;

	/**
	 * @var integer отсрочка времени в секундах, после которой данные будут рассматриваться, как "мусор", и потенциально будут удалены
	 */
	private $gc_maxlifetime; // значение в секундах: 86400 = 1 день

	/**
	 * @var integer максимальное число записей. При привышении лимита любые записи рассматриваются, как "мусор", и потенциально будут удалены
	 */
	private $maxlimitrows;

	/**
	 * @var integer отсрочка времени в секундах, после которой любые данные будут рассматриваться, как "мусор", и потенциально будут удалены
	 */
	private $maxlifetime; // значение в секундах: 1209600 = 14 дней

	/**
	 * @var integer максимальное количество фоновых процессов
	 */
	private $max_global_execute;

	/**
	 * @var integer максимальное количество фоновых процессов одного модуля
	 */
	private $max_module_execute;

	/**
	 * @var boolean включение буферизации вывода
	 */
	private $ob_start;

	/**
	 * @var string тик
	 */
	private $tick;

	/**
	 * Подключает модель
	 *
	 * @return object|null
	 */
	public function __get($name)
	{
		if($name == 'model' || $name == 'inc')
		{
			$module = $this->diafan->current_module;
			if(! isset($this->cache[$name.'_'.$module]))
			{
				if(Custom::exists('modules/'.$module.'/'.$module.'.'.$name.'.php'))
				{
					Custom::inc('modules/'.$module.'/'.$module.'.'.$name.'.php');
					$class = ucfirst($module).'_'.$name;
					$this->cache[$name.'_'.$module] = new $class($this->diafan, $module);
				}
			}
			return $this->cache[$name.'_'.$module];
		}
		return NULL;
	}

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		$this->timestart = microtime(true);
		Custom::inc('modules/executable/executable.inc.php');

		Custom::inc('includes/header.php');
		if(! $value = Header::value('Authorization', 'OAuth ')) $this->token = null;
		else $this->token = $this->diafan->filter($value, "string"); // получаем электронный ключ
		if(! $value = Header::value('id')) $this->id = '';
		else $this->id = $this->diafan->filter($value, "string");
		if(! $value = Header::value('iteration')) $this->iteration = 0;
		else $this->iteration = $this->diafan->filter($value, "integer");
		if(! $value = Header::value('maxiteration')) $this->max_iteration = 0;
		else $this->max_iteration = $this->diafan->filter($value, "integer");
		if(! $value = Header::value('forced')) $this->forced = false;
		else $this->forced = $value ? true : false;
		if(! $value = Header::value('prior')) $this->prior = false;
		else $this->prior = $value ? true : false;
		if(! $value = Header::value('trash')) $this->trash = false;
		else $this->trash = $value ? true : false;
		if(! $value = Header::value('site')) $this->is_admin = false;
		else $this->is_admin = ($value == 'admin');
		if(! $value = Header::value('rewrite')) $this->rewrite = '';
		else $this->rewrite = $this->diafan->filter(urldecode($value), "string");
		if(! $value = Header::value('once')) $this->once = false;
		else $this->once = $value ? true : false;
		if(! $value = Header::value('tick')) $this->tick = null;
		else $this->tick = $this->diafan->filter(urldecode($value), "string");

		$this->text = '';
		$this->init_backtrace = '';
		if(! $value = Header::value('memoryuid')) $memoryuid = '';
		else $memoryuid = $this->diafan->filter($value, "string");
		$cache_meta = Executable_inc::CACHE_META_INIT;
		$cache_meta["uid"] = $memoryuid;
		if($memoryuid && ($cache = $this->diafan->_memory->get($cache_meta, 'executable')))
		{
			if(! empty($cache["text"])) $this->text = $cache["text"];
			if(! empty($cache["initbacktrace"])) $this->init_backtrace = $cache["initbacktrace"];
			$this->diafan->_memory->delete($cache_meta, "executable");
		}

		$this->post = $_POST;
		$this->repeat = false;

		if($value = (int) $this->diafan->configmodules("gc_maxlifetime", "executable")) $this->gc_maxlifetime = $value;
		else $this->gc_maxlifetime = Executable_inc::GS_MAXLIFETIME;
		if($value = (int) $this->diafan->configmodules("maxlifetime", "executable")) $this->maxlifetime = $value;
		else $this->maxlifetime = Executable_inc::MAXLIFETIME;
		if($value = (int) $this->diafan->configmodules("max_global_execute", "executable")) $this->max_global_execute = $value;
		else $this->max_global_execute = Executable_inc::MAX_GLOBAL_EXECUTE;
		if($value = (int) $this->diafan->configmodules("max_module_execute", "executable")) $this->max_module_execute = $value;
		else $this->max_module_execute = Executable_inc::MAX_MODULE_EXECUTE;
		if($value = (int) $this->diafan->configmodules("maxlimitrows", "executable")) $this->maxlimitrows = $value;
		else $this->maxlimitrows = Executable_inc::MAXLIMITROWS;

		$this->max_execution_time = (MAX_EXECUTION_TIME < Executable_inc::MIN_EXECUTION_TIME ? Executable_inc::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
		$this->diafan->set_time_limit($this->max_execution_time);

		$this->gc_maxlifetime = $this->gc_maxlifetime < $this->max_execution_time ? $this->max_execution_time : $this->gc_maxlifetime;
		$this->maxlifetime = $this->maxlifetime < $this->gc_maxlifetime ? $this->gc_maxlifetime : $this->maxlifetime;

		$this->max_global_execute = ($this->max_global_execute ?: Executable_inc::MAX_GLOBAL_EXECUTE);
		$this->max_global_execute = ($this->max_global_execute < Executable_inc::MAX_GLOBAL_EXECUTE
			? Executable_inc::MAX_GLOBAL_EXECUTE
			: $this->max_global_execute);
		$this->max_global_execute = ($this->max_global_execute > Executable_inc::LIMIT_GLOBAL_EXECUTE
			? Executable_inc::LIMIT_GLOBAL_EXECUTE
			: $this->max_global_execute);
		$this->max_module_execute = ($this->max_module_execute ?: Executable_inc::MAX_MODULE_EXECUTE);
		$this->max_module_execute = ($this->max_module_execute > $this->max_global_execute ? $this->max_global_execute : $this->max_module_execute);
		$this->maxlimitrows = ($this->maxlimitrows ?: 1);

		$this->max_global_execute = $this->max_global_execute < self::MAX_SYS_EXECUTE ? self::MAX_SYS_EXECUTE : $this->max_global_execute;

		$this->maxlimitrows = $this->maxlimitrows < $this->max_global_execute
			? $this->max_global_execute
			: $this->maxlimitrows;
		$this->maxlimitrows = $this->maxlimitrows > Executable_inc::MAXLIMITROWS
			? Executable_inc::MAXLIMITROWS
			: $this->maxlimitrows;

		$this->execute = true;

		Custom::inc('includes/json.php');
	}

	/**
	 * Деструктор класса
	 *
	 * @return void
	 */
	public function __destruct()
	{

	}

	/**
	 * Гнерирует исключение с выводом времени работы скрипта
	 *
	 * @return void
	 */
	protected function debug_time()
	{
		throw new Exec_exception("Время выполнения скрипта: " . (microtime(true) - $this->timestart));
	}

	/**
	 * Определяет свойства класса
	 *
	 * @param string $module имя модуля
	 * @param string $method имя метода
	 * @return void
	 */
	public function prepare($module, $method)
	{
		Custom::inc('includes/backtrace.php');
		Backtrace::$exception_callback = array($this, 'end');
		Backtrace::register_shutdown_function(array($this, 'shutdown'));
		Backtrace::init();

		if(! $this->ob_start)
		{
			$this->ob_start = true;
			ob_start();
		}

		$this->module = preg_replace('/[^a-z0-9_]+/', '', $module);
		$this->method = preg_replace('/[^a-z0-9_]+/', '', $method);

		if($this->module == 'executable' && $this->method == 'tick')
		{
			$this->tick = empty($this->tick) ? $this->diafan->uid(true) : $this->tick;
			$this->forced = true;
		}
		else $this->tick = null;
		if($this->module == 'crontab' && $this->method == 'cron')
		{
			$this->forced = true;
		}
		if(in_array($this->module, array('executable', 'crontab')))
		{
			$this->max_module_execute = $this->max_module_execute < self::MAX_SYS_EXECUTE ? self::MAX_SYS_EXECUTE : $this->max_module_execute;
		}

		// чистим реестр фоновых процессов
		$this->gc();

		// определение свойств класса
		$this->variables();

		// получаем или создаем запись о фоновом процессе в таблице {executable}
		$this->set_exec();

		// лимит фоновых процессов превышен, текущий процесс поставлен в очередь
		if(! $this->execute) $this->end();
	}

	/**
	 * Определяет текущий фоновый процесс
	 *
	 * @return void
	 */
	private function set_exec()
	{
		if($this->exec)
		{
			return;
		}

		if(! $this->is_verify())
		{
			$this->execute = false;
			return;
		}

		// лимит аналогичных процессов
		if($this->once)
		{
			if($this->count($this->module, $this->method) > 0)
			{
				$this->execute = false;
				return;
			}
		}

		if($this->tick)
		{
			if(! $this->tick_configmodules_enable())
			{
				$this->execute = false;
				return;
			}
			if(! $this->set_tick())
			{
				$this->execute = false;
				return;
			}
			usleep(Executable_inc::TICK_MSEC); // ждать 1 интервал тик
			if(! $this->verify_tick())
			{
				$this->execute = false;
				return;
			}
		}

		// лимит одновременно запущенных процессов
		if(! $this->forced)
		{
			$this->execute = ($this->count() < $this->max_global_execute);
			if($this->execute) $this->execute = ($this->count($this->module) < $this->max_module_execute);
		}

		if($this->id)
		{
			if(! $this->exec = DB::query_fetch_object("SELECT * FROM {executable} WHERE id='%h' LIMIT 1", $this->id))
			{
				return;
			}
		}
		if(! $this->exec)
		{
			$def = array();
			$masks = array();

			$def["created"] = time();
			$masks[] = "%d";
			$def["timeedit"] = time();
			$masks[] = "%d";
			$def["module_name"] = $this->module;
			$masks[] = "'%s'";
			$def["method"] = $this->method;
			$masks[] = "'%s'";
			$def["params"] = ! empty($this->post) ? serialize($this->post) : '';
			$masks[] = "'%s'";
			$def["text"] = $this->text;
			$masks[] = "'%s'";
			if($this->rewrite)
			{
				if($this->is_admin) $def["is_admin"] = 1;
				else $def["is_admin"] = 0;
				$masks[] = "'%d'";
				$def["rewrite"] = $this->rewrite;
				$masks[] = "'%s'";
			}
			$def["iteration"] = $this->iteration;
			$masks[] = "%d";
			$def["max_iteration"] = $this->max_iteration;
			$masks[] = "%d";
			$def["forced"] = $this->forced ? 1 : 0;
			$masks[] = "'%d'";
			$def["prior"] = $this->prior ? 1 : 0;
			$masks[] = "'%d'";
			$def["trash"] = $this->trash ? 1 : 0;
			$masks[] = "'%d'";
			$def["result"] = '';
			$masks[] = "'%s'";
			$def["status"] = $this->execute ? 1 : 0;
			$masks[] = "'%d'";
			$def["break"] = 0;
			$masks[] = "'%d'";

			$init_params = $def;
			$def["init_params"] = ! empty($init_params) ? serialize($init_params) : '';
			$masks[] = "'%s'";
			$def["init_backtrace"] = ! empty($this->init_backtrace) ? $this->init_backtrace : '';
			$masks[] = "'%s'";

			$this->id = $this->diafan->_db_ex->add_new('{executable}', array_keys($def), $masks, $def);
			$this->exec = DB::query_fetch_object("SELECT * FROM {executable} WHERE id='%h' LIMIT 1", $this->id);
		}
		else
		{
			if($this->status != 1)
			{
				$def = array();
				$def["timeedit=%d"] = time();
				$def["module_name='%s'"] = $this->module;
				$def["method='%s'"] = $this->method;
				$def["params='%s'"] = ! empty($this->post) ? serialize($this->post) : '';
				$def["text='%s'"] = $this->text;
				if($this->rewrite)
				{
					if($this->is_admin) $def["is_admin='%d'"] = 1;
					else $def["is_admin='%d'"] = 0;
					$def["rewrite='%s'"] = $this->rewrite;
				}
				$def["iteration=%d"] = $this->iteration;
				$def["max_iteration=%d"] = $this->max_iteration;
				$def["forced='%d'"] = $this->forced ? 1 : 0;
				$def["prior='%d'"] = $this->prior ? 1 : 0;
				$def["trash='%d'"] = $this->trash ? 1 : 0;
				$def["result='%s'"] = '';
				if(!! $this->exec->break)
				{
					$def["status='%d'"] = $this->exec->status != 3 ? 2 : 3;
					$this->execute = false;
				}
				else $def["status='%d'"] = $this->execute ? 1 : 0;

				$this->id = $this->exec->id;
				$this->diafan->_db_ex->update('{executable}', $this->id, array_keys($def), $def);
				$this->exec = DB::query_fetch_object("SELECT * FROM {executable} WHERE id='%h' LIMIT 1", $this->id);
			}
			else $this->execute = false;
		}

		// лимит одновременно запущенных процессов
		if($this->execute) $this->push();
	}

	/**
	 * Очистка реестра фоновых процессов
	 *
	 * @return void
	 */
	private function gc()
	{
		// всем просроченным фоновым процессам присваиваем статус ошибки: Maximum execution time
		if($this->max_execution_time != 0)
		{
			$answer = array(self::RESULT => self::ERROR);
			$answer[self::ERROR] = sprintf('Maximum execution time of %d seconds exceeded', $this->max_execution_time);
			$json = Json::encode($answer);
			DB::query(
				"UPDATE {executable} SET status='%d', result='%h' WHERE status='%d' AND timeedit<%d",
				3, $json, 1, (time() - $this->max_execution_time)
			);
		}
		// чистим мусор
		if($this->gc_maxlifetime > 0 && $this->maxlifetime > 0)
		{
			DB::query(
				"DELETE FROM {executable} WHERE"
				." (timeedit<%d AND status IN (%h) AND (iteration >=max_iteration))"
				." OR (timeedit<%d AND status IN (%h))"
				." OR (timeedit<%d)",
				(time() - $this->gc_maxlifetime), implode(",", array(2)),
				(time() - $this->gc_maxlifetime), implode(",", array(3)),
				(time() - $this->maxlifetime)
			);
		}
		$count = (int) DB::query_result("SELECT COUNT(*) FROM {executable}");
		if($count > $this->maxlimitrows)
		{
			$count = $count - $this->maxlimitrows;
			DB::query(
				"DELETE FROM {executable} WHERE 1=1 ORDER BY FIELD(status, '2', '3', '0', '1') ASC, forced ASC, prior ASC, timeedit ASC LIMIT %d",
				$count
			);
		}
	}

	/**
	 * Определение свойств класса
	 *
	 * @return void
	 */
	public function variables(){}

	/**
	 * Верификация
	 *
	 * @return boolean
	 */
	public function is_verify()
	{
		$token = $this->diafan->configmodules("token", "executable");
		return (! $this->verify
			|| (! empty($this->token) && ! empty($token)
			&& $this->token == $token)
			&& (! $this->only_https || IS_HTTPS) && (! $this->ajax || $this->is_ajax())
		);
	}

	/**
	 * Является ли запрос AJAX
	 *
	 * @return boolean
	 */
	private function is_ajax()
	{
		if(! empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == 'xmlhttprequest'
			// для IE
			|| ! empty($_POST["action"]))
		{
			return true;
		}
		return false;
	}

	/**
	 * Запоминает найденную ошибку
	 *
	 * @return void
	 */
	public function set_error()
	{
		$this->answer = array(self::RESULT => self::ERROR);
	}

	/**
	 * Проверяет сформирован ли результат
	 *
	 * @return boolean
	 */
	public function result()
	{
		return !! $this->result;
	}

	/**
	 * Проверяет сформирован ли ответ
	 *
	 * @return boolean
	 */
	public function answer()
	{
		return ! (! $this->answer || ! is_array($this->answer) || ! array_key_exists(self::RESULT, $this->answer));
	}

	/**
	 * Отправляет ответ
	 *
	 * @return void
	 */
	public function end()
	{
		$content = false;
		if($this->ob_start)
		{
			if(ob_get_level())
			{
				$content = ob_get_contents();
				ob_end_clean();
			}
			$this->ob_start = null;
		}
		Backtrace::deinit();

		if($this->is_verify())
		{
			// лимит одновременно запущенных процессов
			if($this->execute) $this->pop();

			$count_errors = count(Backtrace::$errors);
			if($count_errors)
			{
				$this->set_error();
				$this->answer[self::ERROR] = Backtrace::print_errors();
			}
			if(! $this->answer()) $this->answer = array(self::RESULT => self::SUCCESS);
			if($this->result()) $this->answer[self::DATA] = $this->result;
			if(! empty(DB::$dev_query))
			{
				$num = (count(DB::$dev_query) > 1);
				foreach(DB::$dev_query as $key => $query) DB::$dev_query[$key] = 'query'.($num ? ' #'.($key+1) : '').': '.$query;
				$this->answer[self::QUERY] = implode(PHP_EOL, DB::$dev_query);
			}
			if($content)
			{
				$json_content = explode(PHP_EOL, $content);
				$this->answer[self::CONTENT] = array();
				foreach($json_errors as $value) $this->answer[self::CONTENT][] = htmlspecialchars($value);
			}
			$json = $this->answer;
			if(! empty($json[self::ERROR]))
			{
				$json_errors = explode(PHP_EOL, $json[self::ERROR]);
				$json[self::ERROR] = array();
				foreach($json_errors as $value) $json[self::ERROR][] = trim(htmlspecialchars($value));
			}
			$json = Json::to_json($json);
			$break = false;
			//финализация фонового процесса
			if($this->execute && $this->exec) // процесс исполнялся и объект процесса не удален
			{
				// TO_DO: условие необходимо, чтобы проверить, не была ли принудительно удалина запись из реестра пока выполнялся фоновый процесс
				if($this->exec = DB::query_fetch_object("SELECT * FROM {executable} WHERE id='%h' LIMIT 1", $this->exec->id))
				{
					// запись об исполнении
					$def = array();
					$def["timeedit=%d"] = time();
					$def["status='%d'"] = ! $count_errors ? 2 : 3;
					$def["result='%s'"] = $json ?: '';
					$def["iteration=%d"] = ! empty($this->iteration) ? $this->iteration : 0;
					$def["max_iteration=%d"] = ! empty($this->max_iteration) ? $this->max_iteration : 0;
					$def["forced='%d'"] = ! empty($this->forced) ? 1 : 0;
					$def["prior='%d'"] = ! empty($this->prior) ? 1 : 0;
					$def["trash='%d'"] = ! empty($this->trash) ? 1 : 0;
					$def["text='%s'"] = ! empty($this->text) ? $this->text : '';
					$this->diafan->_db_ex->update('{executable}', $this->exec->id, array_keys($def), $def);
					$this->exec = DB::query_fetch_object("SELECT * FROM {executable} WHERE id='%h' LIMIT 1", $this->exec->id);
					$break = !! $this->exec->break;
				}
				// определяем необходимость повторения фонового процесса
				$this_repeat = (
					$this->exec                   // объект процесса не удален
					&& ! $break && $this->repeat  // процесс не отменен и требует повторения
					&& ! empty($this->module) && ! empty($this->method) && ! $count_errors
				);
				if($this->tick && $this_repeat)
				{
					if($this_repeat = $this->verify_tick())
					{
						$this_repeat = $this->tick_configmodules_enable();
					}
				}
				if($this->tick && ! $this_repeat) $this->delete_tick();
				// удаление записи по требованию после окончательного завершения процесса
				if($this->exec && $this->trash && ! $this_repeat && ! $count_errors)
				{
					$this->diafan->_db_ex->delete('{executable}', $this->exec->id);
					$this->exec = null;
				}
				// инициализация очередности
				if($this->exec && $this_repeat)
				{
					$this->diafan->_executable->execute(array(
						"id"        => $this->exec->id,
						"module"    => $this->module,
						"method"    => $this->method,
						"params"    => (! empty($this->post) ? $this->post : false),
						"text"      => (! empty($this->text) ? $this->text : false),
						"iteration" => ($this->iteration ?: false),
						"max_iteration" => ($this->max_iteration ?: false),
						"forced" => ($this->forced ? true : false),
						"prior" => ($this->prior ? true : false),
						"trash" => ($this->trash ? true : false),
						"tick" => ($this->tick ?: false),
					));
				}
				elseif($this->count() < $this->max_global_execute)
				{
					// получаем идентификатор следующего процесса
					if($this->count($this->module) < $this->max_module_execute)
					{
						$id = DB::query_result("SELECT id FROM {executable} WHERE status='%d' AND module_name<>'' AND method<>'' GROUP BY id ORDER BY forced DESC, prior DESC, timeedit DESC LIMIT 1", 0);
					}
					else
					{
						$id = DB::query_result("SELECT id FROM {executable} WHERE status='%d' AND module_name<>'' AND module_name<>'%h' AND method<>'' GROUP BY id ORDER BY forced DESC, prior DESC, timeedit DESC LIMIT 1", 0, $this->module);
					}
					if($id)
					{
						$this->diafan->_executable->execute(array(
							"id" => $id
						));
					}
				}
			}
			elseif(! $this->execute && $this->tick)
			{ // инициализированному тику, требующему удаление по завершению процесса, было отказано в исполнении
				$this->delete_tick();
				if($this->id && $this->trash
				&& ($exec = DB::query_fetch_object("SELECT * FROM {executable} WHERE id='%h' LIMIT 1", $this->id)))
				{
					$this->diafan->_db_ex->delete('{executable}', $exec->id);
				}
			}
			echo $json;
			exit;
		}
		if(! $this->page_404)
		{
			$this->set_error();
			echo Json::to_json($this->answer);
			exit;
		}
		include(ABSOLUTE_PATH.Custom::path('includes/404.php'));
		exit;
	}

	/**
	 * Завершение работы скрипта
	 *
	 * @return void
	 */
	public function shutdown(){}

	/**
	 * Возвращает количество активных фоновых процессов
	 *
	 * @param string $module_name имя модуля
	 * @param string $method_name имя метода
	 * @return integer
	 */
	private function count($module_name = false, $method_name = false)
	{
		return $this->diafan->_executable->count($module_name, $method_name);
	}

	/**
	 * Увеличивает количество активных фоновых процессов
	 *
	 * @return void
	 */
	private function push()
	{
		$cache = $this->diafan->_memory->get(Executable_inc::CACHE_META, 'executable', CACHE_REFRESH);
		$cache = ! empty($cache) && is_array($cache) ? $cache : array();
		if(empty($cache["execute"][$this->module][$this->method][$this->id]))
		{
			$cache["execute"][$this->module][$this->method][$this->id] = time();
		}
		$this->diafan->_memory->save($cache, Executable_inc::CACHE_META, 'executable');
	}

	/**
	 * Уменьшает количество активных фоновых процессов
	 *
	 * @return void
	 */
	private function pop()
	{
		$cache = $this->diafan->_memory->get(Executable_inc::CACHE_META, 'executable', CACHE_REFRESH);
		$cache = ! empty($cache) && is_array($cache) ? $cache : array();
		if(isset($cache["execute"][$this->module][$this->method][$this->id]))
		{
			unset($cache["execute"][$this->module][$this->method][$this->id]);
		}
		if(empty($cache["execute"][$this->module][$this->method]))
		{
			unset($cache["execute"][$this->module][$this->method]);
		}
		if(empty($cache["execute"][$this->module]))
		{
			unset($cache["execute"][$this->module]);
		}
		if(empty($cache["execute"]))
		{
			unset($cache["execute"]);
		}
		if(empty($cache))
		{
			$cache = array();
		}
		$this->diafan->_memory->save($cache, Executable_inc::CACHE_META, 'executable');
	}

	/**
	 * Возвращает тик
	 *
	 * @return string
	 */
	private function get_tick()
	{
		$cache = $this->diafan->_memory->get(Executable_inc::CACHE_META_TICK, 'executable', CACHE_REFRESH);
		return (! empty($cache["tick"]) && $cache["tick"] == $this->tick ? $cache["tick"] : false);
	}

	/**
	 * Запоминает тик
	 *
	 * @return boolean
	 */
	private function set_tick()
	{
		if(! $cache = $this->diafan->_memory->get(Executable_inc::CACHE_META_TICK, 'executable', CACHE_REFRESH))
		{
			$cache = array("tick" => $this->tick, "timeedit" => time());
			$this->diafan->_memory->save($cache, Executable_inc::CACHE_META_TICK, 'executable');
			return true;
		}
		return (! empty($cache["tick"]) && $cache["tick"] == $this->tick);
	}

	/**
	 * Удаляет тик
	 *
	 * @return boolean
	 */
	private function delete_tick()
	{
		if($tick = $this->get_tick())
		{
			if($tick == $this->tick)
			{
				$cache = false;
				$this->diafan->_memory->save($cache, Executable_inc::CACHE_META_TICK, 'executable');
				return true;
			}
		}
		return false;
	}

	/**
	 * Верификация тика
	 *
	 * @return boolean
	 */
	protected function verify_tick()
	{
		if(! $tick = $this->get_tick())
		{
			return false;
		}
		return ($tick == $this->tick);
	}

	/**
	 * Данные тика
	 *
	 * @param array $data массив значений тика
	 * @return array
	 */
	protected function tick_data($data = null)
	{
		if(! $cache = $this->diafan->_memory->get(Executable_inc::CACHE_META_TICK, 'executable', CACHE_REFRESH))
		{
			$cache = array("tick" => $this->tick, "timeedit" => time());
			if(! is_null($data)) $cache["data"] = $data;
			$this->diafan->_memory->save($cache, Executable_inc::CACHE_META_TICK, 'executable');
			return (isset($cache["data"]) ? $cache["data"] : array());
		}
		elseif(! empty($cache["tick"]) && $cache["tick"] == $this->tick)
		{
			if(! is_null($data))
			{
				$cache["timeedit"] = time();
				$cache["data"] = $data;
				$this->diafan->_memory->save($cache, Executable_inc::CACHE_META_TICK, 'executable');
			}
			return (isset($cache["data"]) ? $cache["data"] : array());
		}
		return array();
	}

	protected function tick_configmodules_enable()
	{
		// return $this->diafan->configmodules("enable", "crontab");
		return !! DB::query_result("SELECT value FROM {config} WHERE module_name='crontab' AND name='enable' AND lang_id=0 AND site_id=0 LIMIT 1");
	}
}

/**
 * Exec_exception
 *
 * Исключение для фоновых запросов
 */
class Exec_exception extends Exception{}
