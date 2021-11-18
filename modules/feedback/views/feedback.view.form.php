<?php
/**
 * Шаблон формы добавления сообщения в обратной связи
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



if(! empty($result["text"]))
{
	echo '<section class="_text">'.$result["text"].'</section>';
	return;
}

echo '<section class="block-d block-d_form block-d_feedback block-d_feedback_add">';

echo
'<form method="POST" enctype="multipart/form-data" action="" class="ajax">
	<input type="hidden" name="module" value="feedback">
	<input type="hidden" name="action" value="add">
	<input type="hidden" name="form_tag" value="'.$result["form_tag"].'">
	<input type="hidden" name="site_id" value="'.$result["site_id"].'">
	<input type="hidden" name="tmpcode" value="'.md5(mt_rand(0, 9999)).'">';

	//заголовок блока
	if (! empty($result["name"]))
	{
		echo '<header class="block-d__name">'.$result["name"].'</header>';
	}

	$required = false;
	if(! empty($result["rows"]))
	{
		foreach ($result["rows"] as $row) //вывод полей из конструктора форм
		{
			if($row["required"])
			{
				$required = true;
			}
			echo '<div class="field-d feedback_form_param'.$row["id"].'">';

			switch ($row["type"])
			{
				case 'title':
					echo '<div class="field-d__title">'.$row["name"].':</div>';
					break;

				case 'text':
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<input type="text" name="p'.$row["id"].'" value="">';
					break;

				case "email":
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<input type="email" name="p'.$row["id"].'" value="">';
					break;

				case "phone":
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<input type="tel" name="p'.$row["id"].'" value="">';
					break;

				case 'textarea':
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<textarea name="p'.$row["id"].'" cols="66" rows="10"></textarea>';
					break;

				case 'date':
				case 'datetime':
					$timecalendar  = true;
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
						<input type="text" name="p'.$row["id"].'" value="" class="timecalendar" showTime="'
						.($row["type"] == 'datetime'? 'true' : 'false').'">';
					break;

				case 'numtext':
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<input type="number" name="p'.$row["id"].'" value="">';
					break;

				case 'checkbox':
					echo '<input name="p'.$row["id"].'" id="feedback_p'.$row["id"].'" value="1" type="checkbox" ><label for="feedback_p'.$row["id"].'">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').'</label>';
					break;

				case 'radio':
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>';
					echo '<div class="field-d__list">';
					foreach ($row["select_array"] as $select)
					{
						echo
						'<div class="field-d__item">
							<input name="p'.$row["id"].'" type="radio" value="'.$select["id"].'" id="feedback_form_p'.$row["id"].'_'.$select["id"].'">
							<label for="feedback_form_p'.$row["id"].'_'.$select["id"].'">'.$select["name"].'</label>
						</div>';
					}
					echo '</div>';
					break;

				case 'select':
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<select name="p'.$row["id"].'" class="inpselect">
						<option value="">-</option>';
					foreach ($row["select_array"] as $select)
					{
						echo '<option value="'.$select["id"].'">'.$select["name"].'</option>';
					}
					echo '</select>';
					break;

				case 'multiple':
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<div class="field-d__list">';
					foreach ($row["select_array"] as $select)
					{
						echo
						'<div class="field-d__item">
							<input name="p'.$row["id"].'[]" id="feedback_p'.$select["id"].'[]" value="'.$select["id"].'" type="checkbox">
							<label for="feedback_p'.$select["id"].'[]">'.$select["name"].'</label>
						</div>';
					}
					echo '</div>';
					break;

				case "attachments":
					echo
					'<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<label class="inpattachment">
						<input type="file" name="attachments'.$row["id"].'[]" class="inpfiles" max="'.$row["max_count_attachments"].'">
						<label>'.$this->diafan->_('Прикрепить файл').'</label>
					</label>
					<label class="inpattachment" style="display:none">
						<input type="file" name="hide_attachments'.$row["id"].'[]" class="inpfiles" max="'.$row["max_count_attachments"].'">
						<label>'.$this->diafan->_('Прикрепить файл').'</label>
					</label>';
					if ($row["attachment_extensions"])
					{
						echo '<div class="field-d__text attachment_extensions">('.$this->diafan->_('Доступные типы файлов').': '.$row["attachment_extensions"].')</div>';
					}
					break;

				case "images":
					echo
					'<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<div class="inpimage">
						<div class="images"></div>
						<input type="file" name="images'.$row["id"].'" param_id="'.$row["id"].'" class="inpimages">
						<label>'.$this->diafan->_('Прикрепить изображение').'</label>
					</div>';
					break;
			}

			if($row["text"])
			{
				echo '<div class="field-d__text feedback_form_param_text">'.$row["text"].'</div>';
			}

			if($row["type"] != 'title')
			{
				echo '<div class="errors error_p'.$row["id"].'"'.($result["error_p".$row["id"]] ? '>'.$result["error_p".$row["id"]] : ' style="display:none">').'</div>';
			}

			echo '</div>';
		}
	}

	//Защитный код
	echo $result["captcha"];

	//Кнопка Отправить
	echo
	'<button class="button-d">
		<span class="button-d__name">'.$this->diafan->_('Отправить').'</span>
	</button>';

	echo '<div class="privacy_field">'.$this->diafan->_('Отправляя форму, я даю согласие на <a href="%s">обработку персональных данных</a>.', true, BASE_PATH_HREF.'privacy'.ROUTE_END).'</div>';

	if($required)
	{
		echo '<div class="required_field"><span class="_asterisk"></span> — '.$this->diafan->_('Поля, обязательные для заполнения').'</div>';
	}

echo '</form>';

echo '<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>';

echo '</section>';
