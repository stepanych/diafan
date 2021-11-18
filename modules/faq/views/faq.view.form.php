<?php
/**
 * Шаблон формы добавления вопроса
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



echo '<section class="block-d block-d_form block-d_faq block-d_faq_add">';

//заголовок блока
echo '<header class="block-d__name">'.$this->diafan->_('Задайте Ваш вопрос').'</header>';

echo
'<form method="POST" action="" enctype="multipart/form-data" class="ajax">
	<input type="hidden" name="module" value="faq">
	<input type="hidden" name="form_tag" value="'.$result["form_tag"].'">
	<input type="hidden" name="site_id" value="'.$result["site_id"].'">
	<input type="hidden" name="cat_id" value="'.$result["cat_id"].'">';

	//имя
	echo
	'<div class="field-d">
		<div class="field-d__name">'.$this->diafan->_('Ваше имя').'<span class="_asterisk"></span>:</div>
		<input type="text" name="name" value="'.$result["name"].'">
		<div class="errors error_name"'.($result["error_name"] ? '>'.$result["error_name"] : ' style="display:none">').'</div>
	</div>';

	//вопрос
	echo
	'<div class="field-d">
		<div class="field-d__name">'.$this->diafan->_('Ваш вопрос').'<span class="_asterisk"></span>:</div>
		<textarea name="question" cols="66" rows="10"></textarea>
		<div class="errors error_question"'.($result["error_question"] ? '>'.$result["error_question"] : ' style="display:none">').'</div>
	</div>';

	//e-mail
	echo
	'<div class="field-d">
		<div class="field-d__name">'.$this->diafan->_('Ваш e-mail для ответа').':</div>
		<input type="email" name="email" value="">
		<div class="errors error_email"'.($result["error_email"] ? '>'.$result["error_email"] : ' style="display:none">').'</div>
	</div>';

	//прикрепляемые файлы
	if ($result["attachments"])
	{
		echo
		'<div class="field-d">
			<label class="inpattachment">
				<input type="file" name="attachments[]" class="inpfiles" max="'.$result["max_count_attachments"].'">
				<label>'.$this->diafan->_('Прикрепить файл').'</label>
			</label>
			<label class="inpattachment" style="display:none">
				<input type="file" name="hide_attachments[]" class="inpfiles" max="'.$result["max_count_attachments"].'">
				<label>'.$this->diafan->_('Прикрепить файл').'</label>
			</label>';
			if ($result["attachment_extensions"])
			{
				echo '<div class="field-d__text attachment_extensions">('.$this->diafan->_('Доступные типы файлов').': '.$result["attachment_extensions"].')</div>';
			}
			echo '<div class="errors error_attachments"'.(! empty($result["error_attachments"]) ? '>'.$result["error_attachments"] : ' style="display:none">').'</div>
		</div>';
	}

	//защитный код
	echo $result["captcha"];

	//кнопка "Отправить"
	echo
	'<button class="button-d">
		<span class="button-d__name">'.$this->diafan->_('Отправить').'</span>
	</button>

	<div class="privacy_field">'.$this->diafan->_('Отправляя форму, я даю согласие на <a href="%s">обработку персональных данных</a>.', true, BASE_PATH_HREF.'privacy'.ROUTE_END).'</div>

	<div class="required_field"><span class="_asterisk"></span> — '.$this->diafan->_('Поля, обязательные для заполнения').'</div>

</form>';

echo '<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>';

echo '</section>';
