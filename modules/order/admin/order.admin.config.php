<?php
/**
 * Редактирование настроек заказов
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

class Order_admin_config extends Frame_admin
{
	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'config' => array (
			'method_count' => array(
				'type' => 'select',
				'name' => 'Количество',
				'help' => 'Задает метод расчета количества.',
				'select' => array(
					0 => 'товаров',
					1 => 'позиций',
				)
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
