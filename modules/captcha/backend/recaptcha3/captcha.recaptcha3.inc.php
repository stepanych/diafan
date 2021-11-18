<?php
/**
 * Подключение для работы с капчей «reCAPTCHA v3»
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

/**
 * Captcha_recaptcha3_inc
 */
class Captcha_recaptcha3_inc extends Diafan
{
	/**
	 * Выводит капчу
	 * 
	 * @param string $modules метка капчи
	 * @param string $error ошибка ввода кода, если запрос передан не через Ajax
	 * @param boolean $is_update капча генерируется для обновления
	 * @return string
	 */
	public function get($modules, $error, $is_update)
	{
		if($is_update)
		{
			return "";
		}
		$this->diafan->_site->js_view[] = 'https://www.google.com/recaptcha/api.js?render='.$this->diafan->configmodules('recaptcha3_public_key', 'captcha');
		
		$result["public_key"] = $this->diafan->configmodules('recaptcha3_public_key', 'captcha');
		$result["error"] = $error;
		$result["modules"] = $modules;
		ob_start();
		include(Custom::path('modules/captcha/backend/recaptcha3/captcha.recaptcha3.view.php'));
		$data = ob_get_contents();
		ob_end_clean();
		return $data;
	}

	/**
	 * Проверяет правильность ввода капчи
	 * 
	 * @return string|boolean false
	 */
	public function check($modules)
	{
		if(empty($_POST['recaptcha3']))
		{
			return $this->diafan->_('Вы не прошли проверку.', false);
		}
	    $data = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$this->diafan->configmodules('recaptcha3_private_key', 'captcha').'&response='.$_POST['recaptcha3'].'&remoteip='.$_SERVER['REMOTE_ADDR']));
	    if ($data->success)
		{
			return false;
	    }
		else
		{
			return $this->diafan->_('Вы не прошли проверку.', false);
		}
	}
}
