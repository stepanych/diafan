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

class Executable_exec extends Exec
{
	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		Custom::inc('modules/executable/executable.inc.php');
	}

	/**
	 * Инициализация API модуля
	 *
	 * @return void
	 */
	public function init()
	{
		switch($this->method)
		{
			case 'tick':
				$this->tick();
				break;

			case 'tick_check':
				$this->tick_check();
				break;

			default:
				$this->set_error();
				break;
		}
	}

	/**
	 * Определение свойств класса
	 *
	 * @return void
	 */
	public function variables()
	{
		switch($this->method)
		{
			// case 'tick':
			// case 'tick_check':
			// 	$this->verify = false;
			// 	break;

			default:
				break;
		}
	}

	/**
	 * Инициализация тик
	 *
	 * @return void
	 */
	private function tick()
	{
		if(! $this->max_execution_time || ! $this->timestart)
		{
			return;
		}
		$max_execution_time = $this->max_execution_time - self::TICK_DELAY;
		$tick_sec = Executable_inc::TICK_MSEC;
		$tick_sec = ceil($tick_sec / 1000000); // TO_DO: max execution time в микросекундах
		if($tick_sec > $max_execution_time)
		{
			return;
		}

		$data = $this->tick_data();
		$first_start = (! $data || empty($data["timestart"])); // первый запуск
		$datestamp = time();
		$datestamp_end = $datestamp + $max_execution_time;
		if($first_start) $data["timestart"] = $datestamp;
		if(empty($data["cron_timestart"]))
		{
			$data["cron_timestart"] = mktime(
				date("H", $datestamp), date("i", $datestamp), 0,
				date("n", $datestamp), date("j", $datestamp), date("Y", $datestamp)
			) + self::TICK_STEP;
		}
		$data["cron_timestart"] = $this->diafan->_crontab->time_sec_reset($data["cron_timestart"]);
		$data["cron_timeend"] = mktime(
			date("H", $datestamp), date("i", $datestamp), 0,
			date("n", $datestamp), date("j", $datestamp), date("Y", $datestamp)
		) + self::TICK_STEP;
		if($data["cron_timestart"] >= $data["cron_timeend"])
		{
			$data["cron_timeend"] = mktime(
				date("H", $data["cron_timestart"]), date("i", $data["cron_timestart"]), 0,
				date("n", $data["cron_timestart"]), date("j", $data["cron_timestart"]), date("Y", $data["cron_timestart"])
			) + self::TICK_STEP;
		}
		$time = $datestamp;
		do
		{
			if($data["cron_timestart"] <= $datestamp && $data["cron_timestart"] < $data["cron_timeend"])
			{
				$this->diafan->_executable->execute(array(
					"module" => "crontab",
					"method" => "cron",
					"params" => array("timestart" => $data["cron_timestart"], "timeend" => ($data["cron_timeend"] - 1)),
					"text"   => $this->diafan->_('CRONTAB'),
					"trash" => true,
					"forced" => true,
				));

				$data["cron_timestart"] = $data["cron_timeend"];
				$data["cron_timeend"] += self::TICK_STEP;
			}
			if(($datestamp - $time) >= $tick_sec)
			{
				$time = $datestamp;
				if(! $this->tick_configmodules_enable() || ! $this->verify_tick()) break;
				if($this->exec && $this->exec->id) $exec = DB::query_fetch_object("SELECT * FROM {executable} WHERE id='%h' LIMIT 1", $this->exec->id);
				else $exec = false;
				if(! $exec || $exec->break) break;
				$this->tick_data($data);
			}
			usleep(Executable_inc::TICK_MSEC); // ждать 1 интервал тик
			$datestamp = time();
		}
		while($datestamp <= $datestamp_end);
		$this->tick_data($data);
		$this->repeat = true;
	}

	/**
	 * Проверка тика (рестарт по необходимости)
	 *
	 * @return void
	 */
	private function tick_check()
	{
		if(! $this->diafan->configmodules("enable", "crontab") || ! $this->diafan->configmodules("check", "crontab"))
			return;

		if($this->diafan->_executable->tick_status())
			return;

		$this->diafan->_executable->tick();
	}
}
