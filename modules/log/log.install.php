<?php
/**
 * Установка модуля
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

class Log_install extends Install
{
	/**
	 * @var string название
	 */
	public $title = "Лог";

	/**
	 * @var array записи в таблице {modules}
	 */
	public $modules = array(
		array(
			"name" => "log",
			"admin" => true,
		),
	);

	/**
	 * @var array меню административной части
	 */
	public $admin = array(
		array(
			"name" => "Лог",
			"rewrite" => "log",
			"icon_name" => "file-text-o",
			"group_id" => 5,
			"sort" => 46,
			"act" => true,
			"children" => array(
				array(
					"name" => "Лог ошибок",
					"rewrite" => "log",
					"act" => true,
				),
				array(
					"name" => "Настройки",
					"rewrite" => "log/config",
				),
			),
		),
	);
}
