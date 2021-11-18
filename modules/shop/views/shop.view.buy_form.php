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
'<form class="offer-d js_shop_form ajax" method="post" action="">
	<input type="hidden" name="good_id" value="'.$result["row"]["id"].'">
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
		echo
		'<div class="offer-d__pricelist pricelist-d">';
			foreach ($result["row"]["price_arr"] as $price)
			{
				$param_code = '';
				foreach ($price["param"] as $p)
				{
					if($p["value"])
					{
						$param_code .= ' param'.$p["id"].'="'.$p["value"].'"';
					}
				}
				if(! empty($price["image_rel"]))
				{
					$param_code .= ' image_id="'.$price["image_rel"].'"';
				}
				echo
				'<div class="pricelist-d__row offer-d__pricerow js_shop_param_price"'.$param_code.'>
					<strong class="offer-d__price price-d price-d_basic">
						<span class="price-d__num js_shop_price"
							summ="'.$price["price_no_format"].'"
							format_price_1="'.$this->diafan->configmodules("format_price_1", "shop").'"
							format_price_2="'.$this->diafan->configmodules("format_price_2", "shop").'"
							format_price_3="'.$this->diafan->configmodules("format_price_3", "shop").'">'.$price["price"].'</span>
						<span class="price-d__curr">'.$result["result"]["currency"].'</span>
					</strong>';
					if(! empty($price["old_price"]))
					{
						echo
						'<strong class="offer-d__price price-d price-d_old">
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
					if (! $price["count"] && empty($hide_submit) || empty($price["price_no_format"]) && ! $result['result']["buy_empty_price"])
					{
						echo '<span class="_unavailable js_shop_no_buy">'.$this->diafan->_('Товар временно отсутствует').'</span>';
						$waitlist = true;
					}
					echo
				'</div>';
			}
			echo
		'</div>';

		echo
		'<div class="offer-d__choices choices-d">
			<div class="offer-d__choice choice-d js_shop_form_param">';
				foreach ($result["result"]["depends_param"] as $param)
				{
					if(! empty($result["row"]["param_multiple"][$param["id"]]))
					{
						if(count($result["row"]["param_multiple"][$param["id"]]) == 1)
						{
							foreach ($result["row"]["param_multiple"][$param["id"]] as $value => $depend)
							{
								echo '<input type="hidden" name="param'.$param["id"].'" value="'.$value.'"'.($depend == 'depend' ? ' class="js_shop_depend_param"' : '').'>';
							}
						}
						else
						{
							$select = '';
							foreach ($param["values"] as $value)
							{
								if(! empty($result["row"]["param_multiple"][$param["id"]][$value["id"]]))
								{
									if(! $select)
									{
										$select =
										'<div class="field-d">
											<label class="field-d__name">'.$param["name"].'</label>
											<select name="param'.$param["id"].'" class="inpselect'.($result["row"]["param_multiple"][$param["id"]][$value["id"]] == 'depend' ? ' js_shop_depend_param' : '').'">';
									}

									$select .= '<option value="'.$value["id"].'"'
									.(! empty($value["selected"]) ? ' class="js_form_option_selected" selected' : '')
									.'>'.$value["name"].'</option>
									';
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
		</div>';
	}

	if(! empty($result["row"]["additional_cost"]))
	{
		$rand = rand(0, 9999);

		echo
		'<div class="offer-d__additions additions-d js_shop_additional_cost">';
			foreach($result["row"]["additional_cost"] as $r)
			{
				echo
				'<div class="offer-d__addition addition-d">
					<div class="field-d">
						<input type="checkbox" name="additional_cost[]" value="'.$r["id"].'" id="shop_additional_cost_'.$result["row"]["id"].'_'.$r["id"].'_'.$rand.'" summ="';
						if(! $r["percent"] && $r["summ"])
						{
							echo $r["summ"];
						}
						echo '"';
						if($r["required"])
						{
							echo ' checked disabled';
						}
						echo '>';
						echo
						'<label for="shop_additional_cost_'.$result["row"]["id"].'_'.$r["id"].'_'.$rand.'">
							<div class="addition-d__name">'.$r["name"].'</div>
							<div class="addition-d__content">';
								if($r["percent"])
								{
									foreach ($result["row"]["price_arr"] as $price)
									{
										$param_code = '';
										foreach ($price["param"] as $p)
										{
											if($p["value"])
											{
												$param_code .= ' param'.$p["id"].'="'.$p["value"].'"';
											}
										}
										echo '<div class="js_shop_additional_cost_price" summ="'.$r["price_summ"][$price["price_id"]].'"'.$param_code.'>';
										echo ' <b>+'.$r["format_price_summ"][$price["price_id"]].' '.$result["result"]["currency"].'</b></div>';
									}
								}
								elseif($r["summ"])
								{
									echo ' <div class="js_shop_additional_cost" summ="'.$r["summ"].'"><b>+'.$r["format_summ"].' '.$result["result"]["currency"].'</b></div>';
								}
								echo
							'</div>
						</label>
					</div>
				</div>';
			}
			echo
		'</div>';
	}
	if(! empty($waitlist))
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
	echo
	'<div class="offer-d__actionbar js_shop_buy">';
		if (empty($result["row"]['is_file']) && empty($hide_submit))
		{
			echo
			'<div class="offer-d__count count-d">
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
		if(empty($hide_submit))
		{
			echo
			'<button class="offer-d__button offer-d__button_tocart button-d" type="button" action="buy">
				<span class="button-d__icon icon-d fas fa-shopping-bag"></span>
				<span class="button-d__name">'.$this->diafan->_('В корзину').'</span>
			</button>';
		}
		if(empty($hide_submit) && ! empty($result["result"]["one_click"]))
		{
			echo
			'<button class="offer-d__button offer-d__button_oneclick button-d button-d_dark js_shop_one_click" type="button" action="one_click">
				<span class="button-d__name">'.$this->diafan->_('Купить в один клик').'</span>
			</button>';
		}
		echo
	'</div>
	<div class="error"';
	if (! empty($result["row"]["count_in_cart"]))
	{
		$measure_unit = ! empty($result["row"]["measure_unit"]) ? $result["row"]["measure_unit"] : $this->diafan->_('шт.');
		echo '>'.$this->diafan->_('В <a href="%s">корзине</a> %s %s', true, BASE_PATH_HREF.$result["result"]["cart_link"], $result["row"]["count_in_cart"], $measure_unit);
	}
	else
	{
		echo ' style="display:none;">';
	}
	echo
	'</div>
</form>';

//форма быстрого заказа
if(! empty($result["result"]["one_click"]))
{
	$result["result"]["one_click"]["good_id"] = $result["row"]["id"];
	echo $this->get('one_click', 'cart', $result["result"]["one_click"]);
}
