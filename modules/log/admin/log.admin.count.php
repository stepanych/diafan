<?php
/**
 * Количество ошибок в логе для меню административной панели
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
 * Log_admin_count
 */
class Log_admin_count extends Diafan
{
	/**
	 * @var object вспомогательный объект модуля
	 */
	private $log = null;

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		Custom::inc('modules/log/admin/log.admin.inc.php');
		$this->log = new Log_admin_inc($this->diafan);
	}

	/**
	 * Возвращает количество ошибок в логе для меню административной панели
	 *
	 * @param integer $site_id страница сайта, к которой прикреплен модуль
	 * @return integer
	 */
	public function count($site_id)
	{
		$rows = $this->log->errors();
		$count = count($rows);
		return $count;
	}
}
