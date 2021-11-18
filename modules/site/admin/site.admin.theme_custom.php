<?php
/**
 * Настройки шаблона сайта
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2020 OOO «Диафан» (http://www.diafan.ru/)
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
 * Site_admin_theme_custom
 */
class Site_admin_theme_custom extends Diafan
{
	/**
	 * @var array поля для редактирования
	 */
	public $variables = array (
		'logo' => array(
			'type' => 'image',
			'name' => 'Логотип',
			'multilang' => true
		),
		'logo_name' => array(
			'type' => 'textarea',
			'name' => 'Название сайта',
			'multilang' => true
		),
		'logo_text' => array(
			'type' => 'text',
			'name' => 'Слоган',
			'multilang' => true
		),
		'phone' => array(
			'type' => 'phone',
			'name' => 'Телефон',
			'help' => "Контактный телефон.",
			'multilang' => true
		),
		'email' => array(
			"type" => "email",
			"name" => 'E-mail',
			'multilang' => true
		),
		'contacts' => array(
			'type' => 'editor',
			'name' => 'Контакты',
			'help' => "Адрес организации.",
			'multilang' => true
		),
		'title_block' => array(
			'type' => 'title',
			'name' => 'Блоки',
		),
		'show_favorite' => array(
			'type' => 'checkbox',
			'name' => 'Показывать избранное',
		),
		'show_lk' => array(
			'type' => 'checkbox',
			'name' => 'Показывать личный кабинет',
		),
		'show_slider' => array(
			'type' => 'checkbox',
			'name' => 'Показывать слайдер',
		),
		'title_inside' => array(
			'type' => 'title',
			'name' => 'Внутренние страницы',
		),
		'delivery' => array(
			'type' => 'editor',
			'name' => 'Блок о доставке в карточке товара',
			'multilang' => true
		),
	);
}
