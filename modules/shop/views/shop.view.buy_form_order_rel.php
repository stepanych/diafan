<?php
/**
 * Шаблон кнопки «Купить» для блока товаров
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



if (! empty($result["result"]["access_buy"]))
	return false;

if($result["row"]["empty_price"])
	return false;

$action = '';
if(! $result["result"]["cart_link"] || $result["row"]["no_buy"] || empty($result["row"]["count"]))
{
	$action = 'buy';
}

echo
'<form class="offer-d js_shop_form ajax" method="post" action="">
	<input type="hidden" name="good_id" value="'. $result["row"]["id"].'">
	<input type="hidden" name="module" value="shop">
	<input type="hidden" name="action" value="'.$action.'">';

	if ($result["row"]["no_buy"] || empty($result["row"]["count"]))
	{
		echo '<div class="_unavailable js_shop_no_buy js_shop_no_buy_good">'.$this->diafan->_('Товар временно отсутствует').'</div>';
		$hide_submit = true;
		$waitlist = true;
	}

	if(! $result["result"]["cart_link"])
	{
		$hide_submit = true;
	}

	// у товара несколько цен
	if ($result["row"]["price_arr"])
	{
		echo '<div class="offer-d__pricelist pricelist-d">';

		$price = $result["row"]["price_arr"][0];

		echo
		'<div class="pricelist-d__row offer-d__pricerow>
			<strong class="offer-d__price price-d price-d_basic">
				<span class="price-d__num">'.$price["price"].'</span>
				<span class="price-d__curr">'.$result["result"]["currency"].'</span>
			</strong>
		</div>';

		echo '</div>';
	}

	echo '<div class="error">';
	if(! empty($result["row"]["count_in_cart"]))
	{
		$measure_unit = ! empty($result["row"]["measure_unit"]) ? $result["row"]["measure_unit"] : $this->diafan->_('шт.');
		echo $this->diafan->_('В <a href="%s">корзине</a> %s %s', true, BASE_PATH_HREF.$result["result"]["cart_link"], $result["row"]["count_in_cart"], $measure_unit);
	}
	echo '</div>';

	echo
'</form>';

//форма быстрого заказа
if(! empty($result["result"]["one_click"]))
{
	$result["result"]["one_click"]["good_id"] = $result["row"]["id"];
	echo $this->get('one_click', 'cart', $result["result"]["one_click"]);
}
