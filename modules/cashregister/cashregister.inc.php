<?php
/**
 * Подключение модуля «Онлайн касса»
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
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

class Cashregister_inc extends Diafan
{
	/**
	 * @var object бэкэнд
	 */
	private $backend;

	/**
	 * Подключает расширения для подключения
	 *
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		list($prefix, $method) = explode('_', $name, 2);
		switch($prefix)
		{
			case 'db':
				if(! isset($this->backend['db']))
				{
					Custom::inc('modules/cashregister/inc/cashregister.inc.db.php');
					$this->backend['db'] = new Cashregister_inc_db($this->diafan);
				}
				$cashregister = &$this->backend['db'];
				break;

			case 'defer':
				if(! isset($this->backend['defer']))
				{
					Custom::inc('modules/cashregister/inc/cashregister.inc.defer.php');
					$this->backend['defer'] = new Cashregister_inc_defer($this->diafan);
				}
				$cashregister = &$this->backend['defer'];
				break;

			case 'receipt':
				if(! isset($this->backend['receipt']))
				{
					Custom::inc('modules/cashregister/inc/cashregister.inc.receipt.php');
					$this->backend['receipt'] = new Cashregister_inc_receipt($this->diafan);
				}
				$cashregister = &$this->backend['receipt'];
				break;
  			default:
				return false;
		}
		return call_user_func_array(array(&$cashregister, $method), $args);
	}
}