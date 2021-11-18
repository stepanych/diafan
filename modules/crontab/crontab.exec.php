<?php
/**
 * Обрабатывает полученные данные из формы
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

class Crontab_exec extends Exec
{
	/**
	 * Инициализация API модуля
	 *
	 * @return void
	 */
	public function init()
	{
		switch($this->method)
		{
			case 'cron':
				$this->cron();
				break;

			case 'execute':
				$this->execute();
				break;

			default:
				$this->set_error();
				break;
		}
	}

	/**
	 * Инициализация очереди задач CRONTAB
	 *
	 * @return void
	 */
	private function cron()
	{
		$timestart = $this->diafan->filter($_POST, "integer", "timestart");
		$timeend = $this->diafan->filter($_POST, "integer", "timeend");
		if($timestart <= 0 || $timeend <= 0 || $timestart > $timeend)
		{
			return;
		}
		$rows = DB::query_fetch_all(
			"SELECT id, datetime FROM {crontab}"
			." WHERE act='1' AND trash='0' AND datetime<>'' AND module_name<>'' AND method<>''"
			." GROUP BY id ORDER BY sort DESC, id DESC"
		);
		if(! $rows)
		{
			return;
		}
		foreach($rows as $row)
		{
			$datetime = $this->diafan->_crontab->parser($row["datetime"], $timestart);
			if($datetime === false || $datetime < $timestart || $datetime > $timeend) continue;
			$this->diafan->_executable->execute(array(
				"module" => "crontab",
				"method" => "execute",
				"params" => array("id" => $row["id"]),
				"text"   => $this->diafan->_('CRONEXEC'),
				"trash" => true,
				"forced" => true,
			));
		}
	}

	/**
	 * Инициализация задачи CRONTAB
	 *
	 * @return void
	 */
	private function execute()
	{
		if(! $id = $this->diafan->filter($_POST, "integer", "id"))
		{
			return;
		}
		if(! $row = DB::query_fetch_array(
			"SELECT id, datetime, module_name, method, params FROM {crontab}"
			." WHERE id=%d AND act='1' AND trash='0' AND module_name<>'' AND method<>''",
			$id)
		)
		{
			return;
		}
		DB::query(
			"UPDATE {crontab} SET timeinit=%d, errors='%h' WHERE id=%d LIMIT 1",
			time(), 'error', $id
		);
		Custom::inc('includes/cron.php');
		$module = $row["module_name"];
		$method = $row["method"];
		$params = $row["params"];
		$result = null; $error = false; $content = false;
		if(Custom::exists('modules/'.$module.'/'.$module.'.cron.php'))
		{
			Custom::inc('modules/'.$module.'/'.$module.'.cron.php');
			$class = ucfirst($module).'_cron';
			if(class_exists($class) && is_subclass_of($class, 'Cron'))
			{
				$object = new $class($this->diafan);
				if(method_exists($object, $method) || ! is_callable(array($object, $method)))
				{
					try
					{
						$arguments = array();
						if($params)
						{
							$params = unserialize($params);
							if(! empty($params) && is_array($params))
							{
								$arguments = $params;
							}
						}
						ob_start();
						$result = call_user_func_array(array($object, $method), $arguments);
						if(ob_get_level())
						{
							$content = ob_get_contents();
							ob_end_clean();
						}
					}
					catch(Exception $e)
					{
						if(! $error = Backtrace::print_errors())
						{
							if($error = $e->getMessage())
							{
								$error = sprintf('Error in class method: %s -> %s', $class, $method);
							}
						}
					}
				}
				else $error = sprintf('%s is fail_function', $method);
			}
			else $error = sprintf('%s is fail_class', $class);
		}
		else $error = sprintf('File %s is not exists', 'modules/'.$module.'/'.$module.'.cron.php');
		if(! is_null($result))
		{
			$result = serialize($result);
		}
		DB::query(
			"UPDATE {crontab} SET result='%s', errors='%h', content='%s' WHERE id=%d LIMIT 1",
			($result ?: ''), ($error ?: ''), ($content ?: ''), $id
		);
		return;
	}
}
