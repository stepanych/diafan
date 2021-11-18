<?php
/**
 * On-line консультант, система JivoSite
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

class Consultant_admin extends Frame_admin
{
	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'config' => array (
			'backend' => array(
				'type' => 'function',
				'name' => 'Тип',
				'help' => 'Выбор on-line консультанта. Добавить новый метод можно в папке modules/consultant/backend.',
				'addons_tag' => array(
					'tag' => 'consultant',
					'title' => 'Добавить сервис',
				),
			),
		),
	);

	/**
	 * @var array настройки модуля
	 */
	public $config = array (
		'config', // файл настроек модуля
	);
}
