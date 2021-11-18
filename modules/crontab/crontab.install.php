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

class Crontab_install extends Install
{
	/**
	 * @var boolean модуль является частью ядра
	 */
	public $is_core = true;

	/**
	 * @var string название
	 */
	public $title = "Расписание задач";

	/**
	 * @var array таблицы в базе данных
	 */
	public $tables = array(
		array(
			"name" => "crontab",
			"comment" => "Расписание задач",
			"fields" => array(
				array(
					"name" => "id",
					"type" => "INT(11) UNSIGNED NOT NULL AUTO_INCREMENT",
					"comment" => "идентификатор",
				),
				array(
					"name" => "name",
					"type" => "VARCHAR(100) NOT NULL DEFAULT ''",
					"comment" => "название задачи",
				),
				array(
					"name" => "text",
					"type" => "TEXT",
					"comment" => "описание задачи",
				),
				array(
					"name" => "datetime",
					"type" => "VARCHAR(21) NOT NULL DEFAULT ''",
					"comment" => "время выполнения в формате CRONTAB",
				),
				array(
					"name" => "module_name",
					"type" => "VARCHAR(50) NOT NULL DEFAULT ''",
					"comment" => "название модуля",
				),
				array(
					"name" => "method",
					"type" => "VARCHAR(50) NOT NULL DEFAULT ''",
					"comment" => "название метода",
				),
				array(
					"name" => "params",
					"type" => "VARCHAR(250) NOT NULL DEFAULT ''",
					"comment" => "серилизованные параметры инициализации",
				),
				array(
					"name" => "act",
					"type" => "ENUM('0', '1') NOT NULL DEFAULT '0'",
					"comment" => "активное задание: 0 - нет, 1 - да",
				),
				array(
					"name" => "sort",
					"type" => "INT(11) UNSIGNED NOT NULL DEFAULT '0'",
					"comment" => "подрядковый номер для сортировки",
				),
				array(
					"name" => "timeedit",
					"type" => "INT(10) UNSIGNED NOT NULL DEFAULT '0'",
					"comment" => "время последнего изменения в формате UNIXTIME",
				),
				array(
					"name" => "timeinit",
					"type" => "INT(10) UNSIGNED NOT NULL DEFAULT '0'",
					"comment" => "время последней инициализации задачи в формате UNIXTIME",
				),
				array(
					"name" => "result",
					"type" => "TEXT",
					"comment" => "результат исполнения процесса",
				),
				array(
					"name" => "errors",
					"type" => "TEXT",
					"comment" => "ошибки при исполнении процесса",
				),
				array(
					"name" => "content",
					"type" => "TEXT",
					"comment" => "контент по результатам исполнения процесса",
				),
				array(
					"name" => "trash",
					"type" => "ENUM('0', '1') NOT NULL DEFAULT '0'",
					"comment" => "запись удалена в корзину: 0 - нет, 1 - да",
				),
			),
			"keys" => array(
				"PRIMARY KEY (id)",
			),
		),
	);

	/**
	 * @var array записи в таблице {modules}
	 */
	public $modules = array(
		array(
			"name" => "crontab",
			"admin" => true,
			"site" => false,
			"site_page" => false,
		),
	);

	/**
	 * @var array меню административной части
	 */
	public $admin = array(
		array(
			"name" => "Расписание задач",
			"rewrite" => "crontab",
			"icon_name" => "calendar-o",
			"group_id" => 5,
			"sort" => 36,
			"act" => true,
			"children" => array(
				array(
					"name" => "Список задач",
					"rewrite" => "crontab",
					"act" => true,
				),
				array(
					"name" => "Настройки",
					"rewrite" => "crontab/config",
				),
			),
		),
	);
}
