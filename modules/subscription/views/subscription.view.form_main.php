<?php
/**
 * Шаблон формы подписки на рассылки
 * 
 * Шаблонный тег <insert name="show_form" module="subscription" [template="шаблон"]>:
 * блок вывода формы подписки на рассылки
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



echo
'<form class="subscript-d subscript-d_main _box ajax" method="POST" action="" enctype="multipart/form-data">
	<input type="hidden" name="module" value="subscription">
	<input type="hidden" name="action" value="add">
	<input type="hidden" name="form_tag" value="'.$result["form_tag"].'">';
	echo
	'<div class="subscript-d__inside">
		<div class="subscript-d__title">'.$this->diafan->_('Подписаться на рассылку').'</div>
		<div class="subscript-d__field field-d">
			<input type="email" name="mail" placeholder="'.$this->diafan->_('Ваш e-mail', false).'">
		</div>
		<button class="subscript-d__button button-d button-d_narrow" type="submit">
			<span class="button-d__name">'.$this->diafan->_('Подписаться').'</span>
		</button>
	</div>
	<div class="errors error_mail"'.($result["error_mail"] ? '>'.$result["error_mail"] : ' style="display:none">').'</div>
	<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>';
	if(! empty($result['captcha']))
	{
		echo
		'<button class="subscript-d__robot button-d button-d_short" type="button" title="Captcha">
			<span class="button-d__icon icon-d fas fa-robot"></span>
		</button>';

		echo $result['captcha'];
	}
	echo
'</form>';