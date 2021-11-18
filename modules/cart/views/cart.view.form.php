<?php
/**
 * Шаблон формы редактирования корзины товаров, оформления заказа
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



if (empty($result["rows"]))
{
	echo '<p class="_note">'.$this->diafan->_('Корзина пуста.').' <a href="'.BASE_PATH_HREF.$result["shop_link"].'">'.$this->diafan->_('Перейти к покупкам.').'</a></p>';
	return;
}

echo '<a name="top"></a>';

/*
echo '<form action="" method="POST" class="ajax">
<input type="hidden" name="module" value="cart">
<input type="hidden" name="action" value="clear">
<input type="submit" value="'.$this->diafan->_('Очистить корзину', false).'">
</form>';
*/

echo '<section class="section-d section-d_home section-d_cart section-d_cart_home">';

echo
'<form class="cart__invoice js_cart_table_form ajax" method="POST" action="">
	<input type="hidden" name="module" value="cart">
	<input type="hidden" name="action" value="recalc">
	<input type="hidden" name="form_tag" value="'.$result["form_tag"].'">
	<input type="hidden" name="delivery_summ" value="">
	<input type="hidden" name="delivery_info" value="">
	<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>';
	//вывод таблицы с товарами
	echo '<div class="cart_table">';
	echo $this->get('table', 'cart', $result);
	echo '</div>';
	echo '<div class="cart_recalc"><input type="submit" value="'.$this->diafan->_('Пересчитать', false).'"></div>
</form>';

echo
'<form class="cart__form cart_form js_form_order ajax" method="POST" action="" enctype="multipart/form-data">
	<input type="hidden" name="module" value="cart">
	<input type="hidden" name="action" value="order">
	<input type="hidden" name="tmpcode" value="'.md5(mt_rand(0, 9999)).'">';

	if(! empty($result["yandex_fast_order"]))
	{
		echo '<p><a href="'.$result["yandex_fast_order_link"].'"><img src="http'.(IS_HTTPS ? "s" : '').'://cards2.yandex.net/hlp-get/5814/png/3.png" border="0" /></a></p>';
	}

	$required = false;
	if (! empty($result["rows_param"]))
	{
		foreach ($result["rows_param"] as $row)
		{
			if($row["required"])
			{
				$required = true;
			}
			$value = ! empty($result["user"]['p'.$row["id"]]) ? $result["user"]['p'.$row["id"]] : '';

			echo '<div class="field-d order_form_param'.$row["id"].'">';

			switch ($row["type"])
			{
				case 'title':
					echo '<div class="field-d__title">'.$row["name"].':</div>';
					break;

				case 'text':
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<input type="text" name="p'.$row["id"].'" value="'.str_replace('"', '&quot;', $value).'"'.($row["info"] ? ' data-info="'.$row["info"].'"' : '').'>';
					break;

				case "email":
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<input type="email" name="p'.$row["id"].'" value="'.str_replace('"', '&quot;', $value).'"'.($row["info"] ? ' data-info="'.$row["info"].'"' : '').'>';
					break;

				case "phone":
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<input type="tel" name="p'.$row["id"].'" value="'.$value.'"'.($row["info"] ? ' data-info="'.$row["info"].'"' : '').' placeholder="+70000000000">';
					break;

				case 'textarea':
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<textarea name="p'.$row["id"].'"'.($row["info"] ? ' data-info="'.$row["info"].'"' : '').'>'.str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $value).'</textarea>';
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
					<input type="number" name="p'.$row["id"].'" size="5" value="'.$value.'"'.($row["info"] ? ' data-info="'.$row["info"].'"' : '').'>';
					break;

				case 'checkbox':
					echo '<input name="p'.$row["id"].'" id="cart_p'.$row["id"].'" value="1" type="checkbox" '.($value ? ' checked' : '').'><label for="cart_p'.$row["id"].'">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').'</label>';
					break;

				case 'select':
					echo '<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<select name="p'.$row["id"].'">
						<option value="">-</option>';
					foreach ($row["select_array"] as $select)
					{
						echo '<option value="'.$select["id"].'"'.($value == $select["id"] ? ' selected' : '').'>'.$select["name"].'</option>';
					}
					echo '</select>';
					break;

				case 'multiple':
					echo
					'<div class="field-d__name">'.$row["name"].($row["required"] ? '<span class="_asterisk"></span>' : '').':</div>
					<div class="field-d__list">';
					foreach ($row["select_array"] as $select)
					{
						echo
						'<div class="field-d__item">
							<input name="p'.$row["id"].'[]" id="cart_p'.$select["id"].'[]" value="'.$select["id"].'" type="checkbox" '.(is_array($value) && in_array($select["id"], $value) ? ' checked' : '').'>
							<label for="cart_p'.$select["id"].'[]">'.$select["name"].'</label>
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
			echo
			'<div class="field-d">
				<input type="checkbox" checked name="subscribe_in_order" id="subscribe_in_order">
				<label for="subscribe_in_order">'.$this->diafan->_('Подписаться на новости').'</label>
			</div>';
		}
	}

	if(! empty($result["payments"]))
	{
		echo
		'<section class="block-d block-d_payment block-d_payment_item">
			<header class="block-d__name">'.$this->diafan->_('Выберите способ оплаты').':</header>
			<div class="block-d__list _list payments">';
			echo $this->get('list', 'payment', $result["payments"]);
			echo '</div>
		</section>';
	}

	echo
	'<button class="button-d" type="submit">
		<span class="button-d__name">'.$this->diafan->_('Продолжить').'</span>
	</button>';

	echo '<div class="privacy_field">'.$this->diafan->_('Отправляя форму, я даю согласие на <a href="%s">обработку персональных данных</a>.', true, BASE_PATH_HREF.'privacy'.ROUTE_END).'</div>';

	echo '<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>';

	if($required)
	{
		echo '<div class="required_field"><span class="_asterisk"></span> — '.$this->diafan->_('Поля, обязательные для заполнения').'</div>';
	}

	echo
'</form>';

if($result["show_auth"])
{
	echo '<section class="cart__autorization">';
	echo '<div class="_note">'.$this->diafan->_('Если Вы оформляли заказ на сайте ранее, просто введите логин и пароль:').'</div>';
	echo $this->get('show_login', 'registration', $result["show_login"]);
	echo '</section>';

	/* echo '<section class="cart__registration">';
	echo '<div class="_note">'.$this->diafan->_('Если Вы заполните форму регистрации, то при заказе в следующий раз Вам не придется повторно заполнять Ваши данные:').'</div>';
	echo $this->get('form', 'registration', $result["registration"]);
	echo '</section>'; */
}

echo '</section>';
