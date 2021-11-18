<?php
/**
 * Подключение модуля «Онлайн касса» для работы с отложенной отправкой чеков
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
 * Cashregister_inc_defer
 */
class Cashregister_inc_defer extends Diafan
{
	const URL = 'cashregister/send/';

	/**
	 * Инициирует отложенную отправку чеков
	 *
	 * @return void
	 */
	public function init()
	{
		if($this->diafan->configmodules('auto_send', 'cashregister') && $this->diafan->_cashregister->db_count_sent() > 0)
		{
			$this->diafan->fast_request(BASE_PATH.self::URL);
		}
	}
}

/**
 * Cashregister_defer_exception
 *
 * Исключение
 */
class Cashregister_defer_exception extends Exception{}
