<?php
/**
 * Настройки капчи «reCAPTCHA v3»
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

class Captcha_recaptcha3_admin extends Diafan
{
	public $config = array(
		'name' => 'reCAPTCHA v3',
		'params' => array(
			'public_key' => array(
				'type' => 'text',
				'name' => 'Public Key для сервиса <a href="http://www.google.com/recaptcha">reCAPTCHA</a>',
				'help' => 'Параметр выводится, если в поле «Тип» выбрано «reCAPTCHA».',
			),
			'private_key' => array(
				'type' => 'text',
				'name' => 'Private Key для сервиса <a href="http://www.google.com/recaptcha">reCAPTCHA</a>',
				'help' => 'Параметр выводится, если в поле «Тип» выбрано «reCAPTCHA».',
			),
		),
	);
}
