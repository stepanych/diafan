<?php
/**
 * Шаблон кнопки «Купить», в котором характеристики, влияющие на цену выводятся в виде выпадающего списка
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
'<div class="offer-d">

<form class="js_shop_form shop_form ajax" method="post" action="">
<input type="hidden" name="good_id" value="'. $result["row"]["id"].'">
<input type="hidden" name="module" value="shop">
<input type="hidden" name="action" value="'.$action.'">';

if ($result["row"]["price_arr"])
{
	$depends_param = array();
	foreach ($result["result"]["depends_param"] as $param)
	{
		$depends_param[$param["id"]]["name"] = $param["name"];
		foreach($param["values"] as $value)
		{
			$depends_param[$param["id"]]["values"][$value["id"]] = $value["name"];
		}
	}
	foreach($result["row"]["price_arr"] as $price)
	{
		echo
		'<form class="js_shop_form shop_form ajax" method="post" action="">
			<input type="hidden" name="good_id" value="' . $result["row"]["id"] . '">
			<input type="hidden" name="module" value="shop">
			<input type="hidden" name="action" value="'.$action.'">
			<input type="hidden" name="ajax" value="">';

			echo
			'<div class="offer-d__choices choices-d">
				<div class="offer-d__choice choice-d js_shop_form_param">';
					foreach($price["param"] as $param)
					{
						echo '<input type="hidden" name="param'.$param["id"].'" value="'.$param["value"].'">';
						echo $depends_param[$param["id"]]["name"].': '.$depends_param[$param["id"]]["values"][$param["value"]];
					}
					foreach ($result["result"]["depends_param"] as $param)
					{
						if(! empty($result["row"]["param_multiple"][$param["id"]]))
						{
							if(count($result["row"]["param_multiple"][$param["id"]]) == 1)
							{
								foreach($result["row"]["param_multiple"][$param["id"]] as $value => $depend)
								{
									if($depend == 'select')
									{
										echo '<input type="hidden" name="param'.$param["id"].'" value="'.$value.'">';
									}
								}
							}
							else
							{
								$select = '';
								foreach($param["values"] as $value)
								{
									if(! empty($result["row"]["param_multiple"][$param["id"]][$value["id"]])
									&& $result["row"]["param_multiple"][$param["id"]][$value["id"]] == 'select')
									{
										if(! $select)
										{
											$select = '
											<div class="field-d">
												<label class="field-d__name">'.$param["name"].'</label>
												<select name="param'.$param["id"].'" class="cs-select inpselect'.($result["row"]["param_multiple"][$param["id"]][$value["id"]] == 'depend' ? ' js_shop_depend_param' : '').'">';
										}

										$select .= '<option value="'.$value["id"].'"'
										.(! empty($_GET["p" . $param["id"]]) && $_GET["p" . $param["id"]] == $value["id"] ? ' selected' : '')
										.'>'.$value["name"].'</option>';
									}
								}
								if($select)
								{
									echo $select.'</select></div>';
								}
							}
						}
					}
					echo
				'</div>
			</div>
			<div class="offer-d__pricelist pricelist-d">
				<div class="pricelist-d__row offer-d__pricerow">';
					echo
					'<strong class="offer-d__price price-d price-d_basic">
						<span class="price-d__num">'.$price["price"].'</span>
						<span class="price-d__curr">'.$result["result"]["currency"].'</span>
					</strong>';
					if(! empty($price["old_price"]))
					{
						echo
						'<strong class="offer-d__price price-d price-d_old" title="'.$this->diafan->_('Старая цена', false).'">
							<span class="price-d__num">'.$price["old_price"].'</span>
							<span class="price-d__curr">'.$result["result"]["currency"].'</span>
						</strong>';
					}
					if(! empty($price["discount"]))
					{
						echo
						'<div class="offer-d__price price-d price-d_discount">
							<div class="price-d__name">'.($price["role_id"] || $price["person"] ? $this->diafan->_('Ваша скидка') : $this->diafan->_('Скидка')).':</div>
							<span class="price-d__num">'.$price["discount"].'</span>
							<span class="price-d__curr">'.$price["discount_currency"].'</span>';
							if($price["discount_finish"])
							{
								echo '
								<div class="price-d__text">('.$this->diafan->_('до').' '.$price["discount_finish"].')</div>';
							}
							echo
						'</div>';
					}
					if (! $price["count"] || empty($price["price_no_format"]) && ! $result['result']["buy_empty_price"] || $result["row"]["no_buy"])
					{
						echo '<span class="_unavailable js_shop_no_buy">'.$this->diafan->_('Товар временно отсутствует').'</span>';
					}
					echo
				'</div>
			</div>';
			if (! $price["count"] || empty($price["price_no_format"]) && ! $result['result']["buy_empty_price"] || $result["row"]["no_buy"])
			{
				echo
				'<div class="offer-d__waitlist waitlist-d js_shop_waitlist">
					<div class="waitlist-d__title">'.$this->diafan->_('Сообщить, когда появится на e-mail').'</div>
					<div class="waitlist-d__field field-d _required">
						<input type="email" name="mail" value="'.$this->diafan->_users->mail.'">
					</div>
					<button class="waitlist-d__button button-d button-d_narrow" type="button" action="wait">
						<span class="button-d__name">'.$this->diafan->_('Ок').'</span>
					</button>
					<div class="errors error_waitlist" style="display:none"></div>
				</div>';
			}
			else
			{
				echo
				'<div class="offer-d__actionbar js_shop_buy">';
					if (empty($result["row"]['is_file']))
					{
						echo
						'<div class="offer-d__count count-d" title="'.$this->diafan->_('Кол-во', false).'">
							<div class="count-d__control">
								<button class="count-d__dec fas fa-minus js_count_minus" type="button"></button>
								<input class="count-d__input number js_count_input" type="text" value="1" name="count" pattern="[0-9]+([\.|,][0-9]+)?" step="any" data-min="0" data-step="1">
								<button class="count-d__inc fas fa-plus js_count_plus" type="button"></button>
							</div>';
							if(! empty($result["row"]["measure_unit"]))
							{
								echo '<div class="count-d__unit">'.$result["row"]["measure_unit"].'</div>';
							}
							echo
						'</div>';
					}
					echo
					'<button class="offer-d__button offer-d__button_tocart button-d" type="button" action="buy">
						<span class="button-d__icon icon-d fas fa-shopping-bag"></span>
						<span class="button-d__name">'.$this->diafan->_('В корзину').'</span>
					</button>
				</div>';
			}
			echo '<div class="error">';
			if(! empty($price["count_in_cart"]))
			{
				$measure_unit = ! empty($result["row"]["measure_unit"]) ? $result["row"]["measure_unit"] : $this->diafan->_('шт.');
				echo $this->diafan->_('В <a href="%s">корзине</a> %s %s', true, BASE_PATH_HREF.$result["result"]["cart_link"], $price["count_in_cart"], $measure_unit);
			}
			echo
			'</div>
		</form>';
	}
}

echo '</div>';

//форма быстрого заказа
if(! empty($result["result"]["one_click"]))
{
	$result["result"]["one_click"]["good_id"] = $result["row"]["id"];
	echo $this->get('one_click', 'cart', $result["result"]["one_click"]);
}

$this->diafan->_site->js_view[] = 'modules/shop/js/shop.buy_form.js';
$this->diafan->_site->js_view[] = 'modules/shop/js/shop.buy_form.custom.js';
