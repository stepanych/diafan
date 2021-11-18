<?php
/**
 * Подключение модуля «Фоновые процессы» для работы с отложенными задачами
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */
if ( ! defined('DIAFAN'))
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
 * Executable_inc
 */
class Executable_inc extends Diafan
{
	/**
   * @var string url
   */
	const URL = "exec/";

	/**
   * @var array метка кэша
   */
	const CACHE_META = array("name" => "exec", "prefix" => "execute");

	/**
   * @var array метка кэша
   */
	const CACHE_META_TICK = array("name" => "exec", "prefix" => "tick");

	/**
   * @var array метка кэша
   */
	const CACHE_META_INIT = array("name" => "exec", "prefix" => "init");

	/**
	 * @var integer отсрочка времени в секундах, после которой данные будут рассматриваться, как "мусор", и потенциально будут удалены
	 */
	const MIN_EXECUTION_TIME = 30; // MAX_EXECUTION_TIME не может быть равен нулю (бесконечным)

	/**
	 * @var integer отсрочка времени в секундах, после которой данные будут рассматриваться, как "мусор", и потенциально будут удалены
	 */
	const GS_MAXLIFETIME = 86400; // значение в секундах - 1 день

	/**
	 * @var integer отсрочка времени в секундах, после которой любые данные будут рассматриваться, как "мусор", и потенциально будут удалены
	 */
	const MAXLIFETIME = 1209600; // значение в секундах - 14 дней

	/**
	 * @var integer максимальное число записей. При привышении лимита любые записи рассматриваются, как "мусор", и потенциально будут удалены
	 */
	const MAXLIMITROWS = 1024;

	/**
	 * @var integer максимальное количество фоновых процессов
	 */
	const MAX_GLOBAL_EXECUTE = 1;

	/**
	 * @var integer лимит количества фоновых процессов
	 */
	const LIMIT_GLOBAL_EXECUTE = 10;

	/**
	 * @var integer максимальное количество фоновых процессов одного модуля
	 */
	const MAX_MODULE_EXECUTE = 1;

	/**
	 * @var integer интервал тика в микросекундах
	 */
	const TICK_MSEC = 2000000; // 2 секунды

	/**
	 * Инициирует фоновый процесс
	 *
	 * @param array $options параметры инициализации фонового процесса в виде ассоциативного массива:
	 * array(
	 *  "id"            => (string)  идентификатор фонового процесса,
	 *  "module"        => (string)  имя модуля,
	 *  "method"        => (string)  имя метода,
	 *  "params"        => (array)   параметры,
	 *  "text"          => (string)  описание процесса,
	 *  "iteration"     => (integer) номер итерации,
	 *  "max_iteration" => (integer) максимальный номер итерации,
	 *  "forced"        => (boolean) принудительное исполнение,
	 *  "prior"         => (boolean) приоритет исполнения,
	 *  "trash"         => (boolean) удалить запись по завершению фонового процесса,
	 *  "once"          => (boolean) отклонять процесс пока работает аналогичный процесс,
	 *  "tick"          => (string)  идентификатор тик,
	 * )
	 * @param integer $flag флаг или комбинация флагов запроса для fast_request
	 * @return mixed
	 */
	public function execute($options, $flag = 0)
	{
		if(! $options || ! is_array($options))
		{
			return false;
		}
		if(! MOD_DEVELOPER)
		{
			$init_backtrace = '';
		}
		else
		{
			ob_start();
			debug_print_backtrace();
			$init_backtrace = ob_get_contents();
			ob_end_clean();
		}
		$keys = array("id", "module", "method", "params", "text", "iteration", "max_iteration", "forced", "prior", "trash", "once", "tick");
		foreach($keys as $key) ${$key} = isset($options[$key]) ? $options[$key] : null;

		if(! $id || (! $exec = DB::query_fetch_object("SELECT * FROM {executable} WHERE id='%h' LIMIT 1", $id)))
		{
			$id = null;
			$exec = null;
		}

		if(! $id && (! $module || ! $method))
		{
			return false;
		}

		$is_admin = null;
		$rewrite = null;

		if($exec)
		{
			if(is_null($iteration) && ! empty($exec->iteration)
			&& is_null($max_iteration) && ! empty($exec->max_iteration)
			&& ! empty($exec->init_params)
			&& $exec->iteration == $exec->max_iteration)
			{
				$init_params = unserialize($exec->init_params);
				if(! empty($init_params) && is_array($init_params))
				{
					if(is_null($module) && isset($init_params["module_name"])) $module = $init_params["module_name"];
					if(is_null($method) && isset($init_params["method"])) $method = $init_params["method"];
					if(is_null($params) && isset($init_params["params"])) $params = unserialize($init_params["params"]);
					if(is_null($text) && isset($init_params["text"])) $text = $init_params["text"];
					if(is_null($iteration) && isset($init_params["iteration"])) $iteration = $init_params["iteration"];
					if(is_null($max_iteration) && isset($init_params["max_iteration"])) $max_iteration = $init_params["max_iteration"];
					if(is_null($forced) && isset($init_params["forced"])) $forced = $init_params["forced"];
					if(is_null($prior) && isset($init_params["prior"])) $prior = $init_params["prior"];
					if(is_null($trash) && isset($init_params["trash"])) $trash = $init_params["trash"];
					if(is_null($is_admin) && isset($init_params["is_admin"])) $is_admin = $init_params["is_admin"];
					if(is_null($rewrite) && isset($init_params["rewrite"])) $rewrite = $init_params["rewrite"];
				}
			}

			if(is_null($module) && ! empty($exec->module_name)) $module = $exec->module_name;
			if(is_null($method) && ! empty($exec->method)) $method = $exec->method;
			if(is_null($params) && ! empty($exec->params)) $params = unserialize($exec->params);
			if(is_null($text) && ! empty($exec->text)) $text = $exec->text;
			if(is_null($iteration) && ! empty($exec->iteration)) $iteration = $exec->iteration;
			if(is_null($max_iteration) && ! empty($exec->max_iteration)) $max_iteration = $exec->max_iteration;
			if(is_null($forced) && ! empty($exec->forced)) $forced = $exec->forced;
			if(is_null($prior) && ! empty($exec->prior)) $prior = $exec->prior;
			if(is_null($trash) && ! empty($exec->trash)) $trash = $exec->trash;
			if(is_null($is_admin)) $is_admin = !! $exec->is_admin;
			if(is_null($rewrite)) $rewrite = $exec->rewrite;
		}
		else
		{
			if(! empty($_GET["rewrite"]))
			{
				$is_admin = (defined('IS_ADMIN') && IS_ADMIN);
				$rewrite = $_GET["rewrite"]; // getenv('REQUEST_URI')
			}
		}

		if(! $module || ! $method)
		{
			return false;
		}

		if(! $token = $this->diafan->configmodules("token", "executable"))
		{
			// TO_DO: генерация псевдослучайной соли для CRYPT_BLOWFISH hash type
			$salt = substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand()))), 0, 22);
			$token = md5(base64_encode(crypt($this->diafan->domain().$this->diafan->uid(true), $salt)));
			$this->diafan->configmodules("token", "executable", 0, 0, $token);
		}
		$header = array('Authorization: OAuth '.$token);

		if($id) $id = $this->diafan->filter($id, 'string');
		if($text) $text = $this->diafan->filter($text, 'string');
		if($rewrite) $rewrite = $this->diafan->filter($rewrite, 'string');
		if($iteration) $iteration = $this->diafan->filter($iteration, 'integer');
		if($max_iteration) $max_iteration = $this->diafan->filter($max_iteration, 'integer');
		if($forced) $forced = 1;
		if($prior) $prior = 1;
		if($trash) $trash = 1;
		if($once) $once = 1;
		if(! empty($tick) && is_string($tick)) $tick = $this->diafan->filter($tick, 'string');
		else $tick = false;

		$cache = array();

		if($id) $header[] = 'id: '.$id;
		if($text) $cache["text"] = $text;
		if(! empty($rewrite))
		{
			if($is_admin) { $header[] = 'site: admin'; } else { $header[] = 'site: site'; }
			$header[] = 'rewrite: '.urlencode($rewrite);
		}
		if($iteration) $header[] = 'iteration: '.$iteration;
		if($max_iteration) $header[] = 'maxiteration: '.$max_iteration;
		if($forced) $header[] = 'forced: '.$forced;
		if($prior) $header[] = 'prior: '.$prior;
		if($trash) $header[] = 'trash: '.$trash;
		if($once) $header[] = 'once: '.$once;
		if($init_backtrace) $cache["initbacktrace"] = $init_backtrace;
		if($tick) $header[] = 'tick: '.urlencode($tick);
		$flag = ($flag | REQUEST_POST | REQUEST_AJAX);
		if($cache)
		{
			$cache_meta = Executable_inc::CACHE_META_INIT;
			$cache_meta["uid"] = $this->diafan->uid();
			$header[] = 'memoryuid: '.$cache_meta["uid"];
			$this->diafan->_memory->save($cache, $cache_meta, 'executable');
		}
		$request = $this->diafan->fast_request(BASE_PATH.self::URL.$module.'/'.$method.'/', ($params ?: false), ($header ?: false), false, $flag);
		if($flag & (REQUEST_DEBUG | REQUEST_ANSWER_ARRAY))
		{
			return $request;
		}
		elseif($flag & REQUEST_ANSWER)
		{
			Custom::inc('includes/json.php');
			$request = Json::from_json($request);
			return isset($request["data"]) ? $request["data"] : (isset($request["result"]) && $request["result"] == 'success');
		}
		return true;
	}

	/**
	 * Прерывает фоновый процесс или процессы
	 *
	 * @param string $id идентификатор фонового процесса, если не задан, прерываются все процессы
	 * @param string $module имя модуля
	 * @param string $method имя метода
	 * @return void
	 */
	public function break_down($id = '', $module = '', $method = '')
	{
		$def = array("1=1" => 1);
		if($id) $def["id='%h'"] = $id;
		if($module) $def["module_name='%s'"] = $module;
		if($method) $def["method='%s'"] = $method;
		DB::query("UPDATE {executable} SET break='%d' WHERE ".implode(" AND ", array_keys($def))." AND (`status` IN ('0', '1') OR (`max_iteration`>0 AND `iteration`<`max_iteration` AND `break`='0'))", $def);
		// $def["1=1"] = 2;
		// DB::query("UPDATE {executable} SET status='%d' WHERE ".implode(" AND ", array_keys($def))." AND `status`='0' AND `break`='1'", $def);
	}

	/**
	 * Статус фонового процесса
	 *
	 * @param string $id идентификатор фонового процесса
	 * @return mixed(integer|boolean)
	 *  FALSE - процесс не найден,
	 *     -3 - процесс прерван (выявлена ошибка при выполнении процесса),
	 *     -2 - процесс прерван,
	 *     -1 - процесс прерывается,
	 *      0 - процесс ожидает инициализации,
	 *      1 - процесс выполняется,
	 *      2 - процесс завершен,
	 *      3 - процесс завершен с ошибкой
	 */
	public function status($id)
	{
		if(! $exec = DB::query_fetch_object("SELECT * FROM {executable} WHERE id='%h' LIMIT 1", $id))
		{
			return false;
		}
		$exec->status = (int) $exec->status;
		if($exec->iteration < $exec->max_iteration && $exec->status == 2 && ! $exec->break)
		{
			$exec->status = 1;
		}
		if($exec->break)
		{
			$exec->status = ! in_array($exec->status, array(2, 3)) ? -1 : $exec->status * (-1);
		}
		$status = $exec->status; unset($exec);
		return $status;
	}

	/**
	 * Выполняется ли фоновый процесс
	 *
	 * @param string $id идентификатор фонового процесса
	 * @return boolean
	 */
	public function is_execute($id)
	{
		$result = $this->status($id);
		return (abs($result) == 1);
	}

	/**
	 * Инициализация тик
	 *
	 * @return void
	 */
	public function tick()
	{
		$this->execute(array(
			"module" => "executable",
			"method" => "tick",
			"text"   => $this->diafan->_('CRON'),
			"forced" => true,
		));
	}

	/**
	 * Возвращает статус тик
	 *
	 * @param boolean $refresh обновить статус
	 * @return mixed(boolean|integer)
	 * false - отключен
	 * 0     - завис/отключен
	 * true  - включен
	 */
	public function tick_status($refresh = false)
	{
		if($refresh && isset($this->cache["status_tick"]))
		{
			unset($this->cache["status_tick"]);
		}
		if(! isset($this->cache["status_tick"]))
		{
			usleep(self::TICK_MSEC); // TO_DO: ждать 1 интервал тик (один интервал требуется при регистрации тика)
			if($cache = $this->diafan->_memory->get(self::CACHE_META_TICK, 'executable', CACHE_REFRESH))
			{
				$timeedite = $this->tick_maxtimeedit();
				if(empty($cache["timeedit"]) || $cache["timeedit"] < (time() - $timeedite))
				{ // Процесс тик завис. Например, был перезапущен веб-сервер.
					// Деинициализация тик
					$this->tick_delete(sprintf('Maximum execution time edit of %d seconds exceeded', $timeedite));
					$cache = 0;
				}
			}
			$this->cache["status_tick"] = $cache === 0 ?: !! $cache;
		}
		return $this->cache["status_tick"];
	}

	/**
	 * Возвращает максимальную задержку в секундах между записями тика
	 *
	 * @return integer
	 */
	public function tick_maxtimeedit()
	{
		$tick_sec = self::TICK_MSEC;
		$tick_sec = ceil($tick_sec / 1000000); // TO_DO: max execution time в микросекундах
		$timeedite = $tick_sec * 2; // TO_DO: регистрация тика требует 1 интервал (self::TICK_MSEC) + еще интервал на запись
		return $timeedite; // TO_DO: минимально время = 4 сек.
	}

	/**
	 * Деинициализация тик
	 *
	 * @param string $msg сообщение об ошибке
	 * @return boolean
	 */
	public function tick_delete($msg = 'Process is aborted')
	{
		if($cache = $this->diafan->_memory->get(self::CACHE_META_TICK, 'executable', CACHE_REFRESH))
		{
			$cache = false;
			$this->diafan->_memory->save($cache, self::CACHE_META_TICK, 'executable');
		}

		$module_name = 'executable';
		$method_name = 'tick';

		$exec = DB::query_fetch_object(
			"SELECT * FROM {executable} WHERE module_name='%h' AND method='%h' AND status IN ('0', '1') LIMIT 1",
			$module_name, $method_name
		);
		if($exec && $exec->id)
		{
			if(! $exec->trash)
			{
				$answer = array("result" => "error");
				$answer["error"] = ($msg ?: "Aborted");
				Custom::inc('includes/json.php');
				$json = Json::encode($answer);
				DB::query(
					"UPDATE {executable} SET status='%d', break='%d', result='%h' WHERE id='%h' LIMIT 1",
					3, 1, $json, $exec->id
				);
			}
			else $this->diafan->_db_ex->delete('{executable}', $exec->id);
		}

		$cache = $this->diafan->_memory->get(self::CACHE_META, 'executable', CACHE_REFRESH);
		$cache = ! empty($cache) && is_array($cache) ? $cache : array();
		if(! empty($cache["execute"][$module_name][$method_name])
		&& is_numeric($cache["execute"][$module_name][$method_name])
		&& $cache["execute"][$module_name][$method_name] > 0)
		{
			$cache["execute"][$module_name][$method_name] -= 1;
		}
		if(empty($cache["execute"][$module_name][$method_name]) || $cache["execute"][$module_name][$method_name] < 0) unset($cache["execute"][$module_name][$method_name]);
		if(empty($cache["execute"][$module_name])) unset($cache["execute"][$module_name]);
		if(empty($cache["execute"])) unset($cache["execute"]);
		if(empty($cache)) $cache = array();
		$this->diafan->_memory->save($cache, self::CACHE_META, 'executable');
	}

	/**
	 * Проверка тика (рестарт по необходимости)
	 *
	 * @return void
	 */
	public function tick_check()
	{
		// TO_DO: Рестарт возможен не ранее чем через два интервала тика ($this->tick_maxtimeedit() = self::TICK_MSEC * 2),
		// после его падения (например, перезагрузки веб-сервера)
		if(! $this->diafan->configmodules("enable", "crontab") || ! $this->diafan->configmodules("check", "crontab"))
			return;

		$module = "executable";
		$method = "tick_check";
		if($rows = $this->get($module, $method))
		{
			$max_execution_time = time() - $this->tick_maxtimeedit();
			foreach($rows as $id => $time)
			{
				if($time < $max_execution_time)
				{
					$this->delete($id);
					unset($rows[$id]);
					continue;
				}
			}
			if(! empty($rows)) return;
		}
		$this->execute(array(
			"module" => $module,
			"method" => $method,
			"text"   => $this->diafan->_('CRON'),
			"once"   => true,
			"forced" => true,
			"trash" => true,
		));
	}

	/**
	 * Возвращает количество активных фоновых процессов
	 *
	 * @param string $module_name имя модуля
	 * @param string $method_name имя метода
	 * @return integer
	 */
	public function count($module_name = false, $method_name = false)
	{
		$this->cache_gc();
		$cache = $this->diafan->_memory->get(self::CACHE_META, 'executable', CACHE_REFRESH);
		$cache = ! empty($cache) && is_array($cache) ? $cache : array();
		$count = 0;
		if($module_name && $method_name)
		{
			if(! empty($cache["execute"][$module_name][$method_name])
			&& is_array($cache["execute"][$module_name][$method_name]))
			{
				$count = count($cache["execute"][$module_name][$method_name]);
			}
		}
		elseif(! $module_name && ! $method_name)
		{
			if(! empty($cache["execute"]) && is_array($cache["execute"]))
			{
				foreach($cache["execute"] as $module => $array)
				{
					if(empty($array) || ! is_array($array)) continue;
					foreach($array as $method => $values)
					{
						if(! is_array($values)) continue;
						$count += count($values);
					}
				}
			}
		}
		elseif(! $module_name)
		{
			if(! empty($cache["execute"])
			&& is_array($cache["execute"]))
			{
				foreach($cache["execute"] as $module => $array)
				{
					if(empty($array) || ! is_array($array)) continue;
					foreach($array as $method => $values)
					{
						if(! is_array($values)) continue;
						if($method_name != $method) continue;
						$count += count($values);
					}
				}
			}
		}
		elseif(! $method_name)
		{
			if(! empty($cache["execute"][$module_name])
			&& is_array($cache["execute"][$module_name]))
			{
				foreach($cache["execute"][$module_name] as $method => $values)
				{
					if(! is_array($values)) continue;
					$count += count($values);
				}
			}
		}
		return $count;
	}

	/**
	 * Возвращает содержание кеша активных фоновых процессов
	 *
	 * @param string $module_name имя модуля
	 * @param string $method_name имя метода
	 * @return array
	 */
	private function get($module_name, $method_name)
	{
		$this->cache_gc();
		$cache = $this->diafan->_memory->get(self::CACHE_META, 'executable', CACHE_REFRESH);
		$cache = ! empty($cache) && is_array($cache) ? $cache : array();
		$result = array();
		if($module_name && $method_name)
		{
			if(! empty($cache["execute"][$module_name][$method_name])
			&& is_array($cache["execute"][$module_name][$method_name]))
			{
				$result = $cache["execute"][$module_name][$method_name];
			}
		}
		return $result;
	}

	/**
	 * Очистка кеша активных фоновых процессов от "мусора"
	 *
	 * @return void
	 */
	private function cache_gc()
	{
		$cache = $this->diafan->_memory->get(self::CACHE_META, 'executable', CACHE_REFRESH);
		$cache = ! empty($cache) && is_array($cache) ? $cache : array();
		if(! isset($this->cache["cache_cheack"]))
		{
			if(! empty($cache["execute"]))
			{
				$max_execution_time = (MAX_EXECUTION_TIME < self::MIN_EXECUTION_TIME ? self::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
				$max_execution_time = (time() - $max_execution_time);
				foreach($cache["execute"] as $module => $array)
				{
					if(empty($array) || ! is_array($array))
					{
						unset($cache["execute"][$module]);
						continue;
					}
					foreach($array as $method => $values)
					{
						if(empty($values) || ! is_array($values))
						{
							unset($cache["execute"][$module][$method]);
							continue;
						}
						foreach($values as $id => $time)
						{
							$row = $this->diafan->_db_ex->get('{executable}', $id);
							if(! $row || $row["status"] != 1 || $row["timeedit"] < $max_execution_time)
							{
								unset($cache["execute"][$module][$method][$id]);
								continue;
							}
						}
						if(empty($cache["execute"][$module][$method]))
						{
							unset($cache["execute"][$module][$method]);
							continue;
						}
					}
					if(empty($cache["execute"][$module]))
					{
						unset($cache["execute"][$module]);
						continue;
					}
				}
				if(empty($cache["execute"]))
				{
					unset($cache["execute"]);
				}
				if(empty($cache))
				{
					$cache = array();
				}
				$this->diafan->_memory->save($cache, self::CACHE_META, 'executable');
			}
			$this->cache["cache_cheack"] = true;
		}
		return;
	}

	/**
	 * Удаляет идентификатор из кеша активных фоновых процессов и прерывает сам процесс
	 *
	 * @param string $id идентификатор фонового процесса
	 * @return boolean
	 */
	private function delete($id)
	{
		if(! $id) return;
		$this->cache_gc();
		$cache = $this->diafan->_memory->get(self::CACHE_META, 'executable', CACHE_REFRESH);
		$cache = ! empty($cache) && is_array($cache) ? $cache : array();

		if(! empty($cache["execute"]) && is_array($cache["execute"]))
		{
			foreach($cache["execute"] as $module => $array)
			{
				if(empty($array) || ! is_array($array)) continue;
				foreach($array as $method => $values)
				{
					if(! is_array($values)) continue;
					if(! isset($values[$id])) continue;

					unset($cache["execute"][$module][$method][$id]);
					$this->break_down($id);

					if(empty($cache["execute"][$module][$method]))
					{
						unset($cache["execute"][$module][$method]);
					}
					if(empty($cache["execute"][$module]))
					{
						unset($cache["execute"][$module]);
					}
					if(empty($cache["execute"]))
					{
						unset($cache["execute"]);
					}
					if(empty($cache))
					{
						$cache = array();
					}
					$this->diafan->_memory->save($cache, self::CACHE_META, 'executable');
					return true;
				}
			}
		}
		return false;
	}
}
