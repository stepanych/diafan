<?php
/**
 * Шаблон форма оформления заказа в один клик
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
'<div class="oneclick-d _scroll js_cart_one_click cart_one_click" style="display:none">

	<form method="POST" action="" class="js_cart_one_click_form cart_one_click_form ajax" enctype="multipart/form-data">
		<input type="hidden" name="module" value="cart">
		<input type="hidden" name="action" value="one_click">
		<input type="hidden" name="form_tag" value="'.$result["form_tag"].'">
		<input type="hidden" name="good_id" value="'.$result["good_id"].'">
		<input type="hidden" name="tmpcode" value="'.md5(mt_rand(0, 9999)).'">';

		if (! empty($result["rows_param"]))
		{
			foreach ($result["rows_param"] as $row)
			{
				$value = ! empty($result["user"]['p'.$row["id"]]) ? $result["user"]['p'.$row["id"]] : '';

				echo
				'<div class="field-d order_form_param'.$row["id"].'">';

					switch ($row["type"])
					{
						case 'title':
							echo '<div class="field-d__title">'.$row["name"].':</div>';
							break;

						case 'text':
							echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
							<input type="text" name="p'.$row["id"].'" value="'.$value.'">';
							break;

						case "phone":
							echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
							<input type="tel" name="p'.$row["id"].'" value="'.$value.'" placeholder="+70000000000">';
							break;

						case "email":
							echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
							<input type="email" name="p'.$row["id"].'" value="'.$value.'">';
							break;

						case 'textarea':
							echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
							<textarea name="p'.$row["id"].'" rows="10" cols="30">'.$value.'</textarea>';
							break;

						case 'date':
						case 'datetime':
							$timecalendar  = true;
							echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
								<input type="text" name="p'.$row["id"].'" value="'.$value.'" class="timecalendar" showTime="'
								.($row["type"] == 'datetime'? 'true' : 'false').'">';
							break;

						case 'numtext':
							echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
							<input type="number" name="p'.$row["id"].'" value="'.$value.'">';
							break;

						case 'checkbox':
							echo '<input name="p'.$row["id"].'" id="cart_'.$result["good_id"].'_p'.$row["id"].'" value="1" type="checkbox" '.($value ? ' checked' : '').'><label for="cart_'.$result["good_id"].'_p'.$row["id"].'">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').'</label>';
							break;

						case 'select':
							echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
							<select name="p'.$row["id"].'" class="inpselect">
								<option value="">-</option>';
							foreach ($row["select_array"] as $select)
							{
								echo '<option value="'.$select["id"].'"'.($value == $select["id"] ? ' selected' : '').'>'.$select["name"].'</option>';
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
									<input name="p'.$row["id"].'[]" id="cart_'.$result["good_id"].'_p'.$select["id"].'[]" value="'.$select["id"].'" type="checkbox" '.(is_array($value) && in_array($select["id"], $value) ? ' checked' : '').'>
									<label for="cart_'.$result["good_id"].'_p'.$select["id"].'[]">'.$select["name"].'</label>
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

					if(! empty($row["text"]))
					{
						echo '<div class="field-d__text order_form_param_text">'.$row["text"].'</div>';
					}

					echo '<div class="errors error_p'.$row["id"].'"'.($result["error_p".$row["id"]] ? '>'.$result["error_p".$row["id"]] : ' style="display:none">').'</div>
				</div>';
			}
			if(! empty($result["subscribe_in_order"]))
			{
				echo '<input type="hidden" name="subscribe_in_order" value="1">';
			}
		}
		echo
		'<button class="button-d button-d_narrow" type="button">
			<span class="button-d__name">'.$this->diafan->_('Заказать').'</span>
		</button>

		<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>

		<div class="required_field"><span class="_asterisk"></span> — '.$this->diafan->_('Поля, обязательные для заполнения').'</div>';

		echo '<div class="privacy_field">'.$this->diafan->_('Отправляя форму, я даю согласие на <a href="%s">обработку персональных данных</a>.', true, BASE_PATH_HREF.'privacy'.ROUTE_END).'</div>';

		echo
	'</form>
	<button class="oneclick-d__close close-d" title="'.$this->diafan->_('Закрыть', false).'"></button>
</div>';
