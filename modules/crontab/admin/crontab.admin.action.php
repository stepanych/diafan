<?php
/**
 * Обработка POST-запросов в административной части модуля
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
 * Crontab_admin_action
 */
class Crontab_admin_action extends Action_admin
{

	/**
	 * Вызывает обработку Ajax-запросов
	 *
	 * @return void
	 */
	public function init()
	{
		if (! empty($_POST["action"]))
		{
			switch($_POST["action"])
			{
				case 'cron':
					$this->cron();
					break;
			}
		}
	}

	/**
	 * Удаление завершённых фоновых процессов
	 *
	 * @return void
	 */
	public function cron()
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->result["redirect"] = URL;
			return;
		}

		//проверка прав пользователя на активацию/блокирование
		if (! $this->diafan->_users->roles('edit', $this->diafan->_admin->rewrite))
		{
			$this->result["redirect"] = URL;
			return;
		}

		$status_tick = !! $this->diafan->_executable->tick_status();
		if(! $status_tick)
		{
			// запуск тика
			$this->diafan->configmodules("enable", "crontab", 0, 0, 1);
			$this->diafan->_executable->tick();
		}
		else
		{
			// остановка тика
			$this->diafan->configmodules("enable", "crontab", 0, 0, 0);
			$this->diafan->_executable->tick_delete(sprintf('Process is stopped in %d', date("H:i:s d.m.Y")));
			// TO_DO: альтернативная остановка тика
			// $this->diafan->_memory->delete(Executable_inc::CACHE_META_TICK, "executable");
		}
		$status_tick = !! $this->diafan->_executable->tick_status(true);
		$this->diafan->configmodules("enable", "crontab", 0, 0, ($status_tick ? 1 : 0));
		$this->result["switch"] = $status_tick ? 'on' : 'off';
		$this->result["title"] = $status_tick ? $this->diafan->_('Выключить') : $this->diafan->_('Включить');

		// $this->result["redirect"] = URL.$this->diafan->get_nav;
	}
}
