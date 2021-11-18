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
 * Log_admin_action
 */
class Log_admin_action extends Action_admin
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
				case 'delete':
					$this->delete();
					break;
			}
		}
	}

	/**
	 * Очищает лог ошибок
	 *
	 * @return void
	 */
	private function delete()
	{
		if(is_writable(ABSOLUTE_PATH.Dev::LOG_ERRORS_PATH))
		{
			File::rm(Dev::LOG_ERRORS_PATH);
		}
		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}
}
