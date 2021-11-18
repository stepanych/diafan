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

class Executable_install extends Install
{
	/**
	 * @var boolean модуль является частью ядра
	 */
	public $is_core = true;

	/**
	 * @var string название
	 */
	public $title = "Фоновые процессы";

	/**
	 * @var array таблицы в базе данных
	 */
	public $tables = array(
		array(
			"name" => "executable",
			"comment" => "Реестр фоновых процессов",
			"fields" => array(
				array(
					"name" => "master_id",
					"type" => "INT(10) UNSIGNED NOT NULL DEFAULT '0'",
					"comment" => "главный идентификатор в формате UNIXTIME",
				),
				array(
					"name" => "slave_id",
					"type" => "INT(11) UNSIGNED NOT NULL DEFAULT '0'",
					"comment" => "вторичный идентификатор в интервале master_id",
				),
				array(
					"name" => "id",
					"type" => "VARCHAR(21) NOT NULL DEFAULT ''",
					"comment" => "объединенный идентификатор",
				),
				array(
					"name" => "created",
					"type" => "INT(10) UNSIGNED NOT NULL DEFAULT '0'",
					"comment" => "дата создания",
				),
				array(
					"name" => "timeedit",
					"type" => "INT(10) UNSIGNED NOT NULL DEFAULT '0'",
					"comment" => "время последнего изменения в формате UNIXTIME",
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
					"type" => "TEXT",
					"comment" => "серилизованные параметры инициализации",
				),
				array(
					"name" => "text",
					"type" => "TEXT",
					"comment" => "описание процесса",
				),
				array(
					"name" => "is_admin",
					"type" => "ENUM('0', '1') NOT NULL DEFAULT '0'",
					"comment" => "статус: 0 - общая часть сайта, 1 - административная часть сайта",
				),
				array(
					"name" => "rewrite",
					"type" => "VARCHAR(250) NOT NULL DEFAULT ''",
					"comment" => "псевдоссылка",
				),
				array(
					"name" => "iteration",
					"type" => "INT(11) UNSIGNED NOT NULL DEFAULT '0'",
					"comment" => "порядковый номер итерации",
				),
				array(
					"name" => "max_iteration",
					"type" => "INT(11) UNSIGNED NOT NULL DEFAULT '0'",
					"comment" => "максимальный номер итерации",
				),
				array(
					"name" => "init_params",
					"type" => "TEXT",
					"comment" => "серилизованные параметры первичной инициализации",
				),
				array(
					"name" => "init_backtrace",
					"type" => "TEXT",
					"comment" => "стек вызовов функций инициализации",
				),
				array(
					"name" => "result",
					"type" => "TEXT",
					"comment" => "результат исполнения процесса",
				),
				array(
					"name" => "break",
					"type" => "ENUM('0', '1') NOT NULL DEFAULT '0'",
					"comment" => "статус: 0 - процесс разрешён, 1 - процесс прерван",
				),
				array(
					"name" => "status",
					"type" => "ENUM('0', '1', '2', '3') NOT NULL DEFAULT '0'",
					"comment" => "статус: 0 - ожидает инициализации, 1 - процесс выполнения, 2 - процесс завершен, 3 - ошибка во время выполнения",
				),
				array(
					"name" => "forced",
					"type" => "ENUM('0', '1') NOT NULL DEFAULT '0'",
					"comment" => "принудительное исполнение: 0 - нет, 1 - да",
				),
				array(
					"name" => "prior",
					"type" => "ENUM('0', '1') NOT NULL DEFAULT '0'",
					"comment" => "приоритет исполнения: 0 - без приоритета, 1 - приоритетно",
				),
				array(
					"name" => "trash",
					"type" => "ENUM('0', '1') NOT NULL DEFAULT '0'",
					"comment" => "считать запись мусором по завершению процесса: 0 - нет, 1 - да",
				),
			),
			"keys" => array(
				"PRIMARY KEY (`master_id`, `slave_id`)",
			),
		),
	);

	/**
	 * @var array записи в таблице {modules}
	 */
	public $modules = array(
		array(
			"name" => "executable",
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
			"name" => "Фоновые процессы",
			"rewrite" => "executable",
			"icon_name" => "tasks",
			"group_id" => 5,
			"sort" => 35,
			"act" => true,
			"children" => array(
				array(
					"name" => "Реестр фоновых процессов",
					"rewrite" => "executable",
					"act" => true,
				),
				array(
					"name" => "Настройки",
					"rewrite" => "executable/config",
				),
			),
		),
	);
}
