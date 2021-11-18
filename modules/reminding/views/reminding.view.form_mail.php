<?php
/**
 * Шаблон формы восстановления доступа
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

echo '
<form method="POST" action="" class="reminding_form ajax">
	<input type="hidden" name="action" value="mail">
	<input type="hidden" name="module" value="reminding">
	'.$result["action"];

	echo
	'<div class="field-d">
		<div class="field-d__name">'.$this->diafan->_('Введите ваш e-mail').'<span class="_asterisk"></span>:</div>
		<input type="email" name="mail" value="">
		<div class="errors error_mail"'.($result["error_mail"] ? '>'.$result["error_mail"] : ' style="display:none">').'</div>
	</div>';

	echo $result["captcha"];

	echo
	'<button class="button-d" type="submit">
		<span class="button-d__name">'.$this->diafan->_('Отправить').'</span>
	</button>

	<div class="required_field"><span class="_asterisk"></span> — '.$this->diafan->_('Поля, обязательные для заполнения').'</div>

</form>

<div class="errors error reminding_result"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>';
