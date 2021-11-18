<?php
/**
 * Количество активных фонововых процессов
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
 * Executable_admin_count
 */
class Executable_admin_count extends Diafan
{
	/**
	 * Возвращает количество неотправленных уведомлений для меню административной панели
	 * @param integer $site_id страница сайта, к которой прикреплен модуль
	 * @return integer
	 */
	public function count($site_id)
	{
		// Всего ожидают процессов
		$defer = (int) DB::query_result("SELECT COUNT(*) FROM {executable} WHERE status='0'");
		// Всего выполняются процессов
		$execute = $this->diafan->_executable->count();
		// Всего процессов, завершённых с ошибками
		// $errors = (int) DB::query_result("SELECT COUNT(*) FROM {executable} WHERE `status`='3'");
		$count = $defer + $execute;
		return $count;
	}
}
