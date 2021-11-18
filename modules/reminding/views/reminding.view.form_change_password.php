<?php
/**
 * Шаблон формы смены пароля
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

switch($result["result"])
{
    case "incorrect":
	echo '<p class="_note">'.$this->diafan->_('Извините, вы не можете воспользоваться этой ссылкой.').'</p>';
	break;

    case "block":
	echo '<p class="_note">'.$this->diafan->_('Пользователь заблокирован.').'</p>';
	break;

    case "old":
	echo '<p class="_note">'.$this->diafan->_('Извините, время действия ссылки закончилось.').'</p>';
	break;

    case "success":
	echo '
	<form method="POST" action="" class="reminding_form ajax">
		<input type="hidden" name="action" value="change_password">
		<input type="hidden" name="module" value="reminding">
		<input type="hidden" name="code" value="'.$result["code"].'">
		<input type="hidden" name="user_id" value="'.$result["user_id"].'">
		
		<div class="field-d">
			<div class="field-d__name">'.$this->diafan->_('Введите новый пароль').'<span class="_asterisk"></span>:</div>
			<input type="password" name="password" value="">
			<div class="errors error_password"'.($result["error_password"] ? '>'.$result["error_password"] : ' style="display:none">').'</div>
		</div>
		
		<div class="field-d">
			<div class="field-d__name">'.$this->diafan->_('Повторите пароль').'<span class="_asterisk"></span>:</div>
			<input type="password" name="password2" value="">
		</div>

		<button class="button-d" type="submit">
			<span class="button-d__name">'.$this->diafan->_('Отправить').'</span>
		</button>

		<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>

		<div class="required_field"><span class="_asterisk"></span> — '.$this->diafan->_('Поля, обязательные для заполнения').'</div>
	</form>';
	break;
}