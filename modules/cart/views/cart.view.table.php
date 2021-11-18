<?php
/**
 * Шаблон таблицы с товарами в корзине
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
 */

if (! defined('DIAFAN')) {
	$path = __FILE__;
	while(! file_exists($path.'/includes/404.php'))
	{
		$parent = dirname($path);
		if($parent == $path) exit;
		$path = $parent;
	}
	include $path.'/includes/404.php';
}



echo '<div class="table-d table-d_cart table-d_sm table-d_md table-d_lg">';

//товары
if(! empty($result["rows"]))
{
	echo '<section class="table-d__section table-d__goods">';

	//шапка таблицы
	echo
	'<div class="table-d__rows table-d__head">
		<div class="table-d__row">
			<div class="cell-d cell-d_images"></div>
			<div class="cell-d cell-d_details">'.$this->diafan->_('Наименование').'</div>
			<div class="cell-d cell-d_count">'.$this->diafan->_('Количество').'</div>
			<div class="cell-d cell-d_unit">'.$this->diafan->_('Ед.').'</div>
			<div class="cell-d cell-d_price">'.$this->diafan->_('Цена').'</div>';
			if($result["discount"])
			{
				echo
				'<div class="cell-d cell-d_price_old">'.$this->diafan->_('Цена со скидкой').'</div>
				<div class="cell-d cell-d_discount">'.$this->diafan->_('Скидка').'</div>';
			}
			echo
			'<div class="cell-d cell-d_sum">'.$this->diafan->_('Сумма').'</div>
			<div class="cell-d cell-d_remove"></div>
		</div>
	</div>';

	echo '<div class="table-d__rows table-d__enum">';
	foreach ($result["rows"] as $row)
	{
		echo
		'<div class="table-d__row js_cart_item">';

			echo
			'<div class="cell-d cell-d_images">
				<a href="'.BASE_PATH_HREF.$row["link"].'">';
					if(! empty($row["img"]))
					{
						echo '<img src="'.$row["img"]["src"].'" width="'.$row["img"]["width"].'" height="'.$row["img"]["height"].'" alt="'.$row["img"]["alt"].'" title="'.$row["img"]["title"].'">';
					}
					else
					{
						echo '<figure class="_dummyimage"></figure>';
					}
					echo
				'</a>
			</div>';

			echo
			'<div class="cell-d cell-d_details details-d">';
				if(! empty($row["cats"]))
				{
					echo '<nav class="detail-d detail-d_breadcrumbs">';
					foreach($row["cats"] as $i => $cat)
					{
						if($i)
						{
							echo ' / ';
						}
						echo '<a href="'.BASE_PATH_HREF.$cat["link"].'">'.$cat["name"].'</a>';
					}
					echo '</nav>';
				}
				echo
				'<div class="detail-d detail-d_name">
					<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["good_name"].'</a>
				</div>';
				if(! empty($row["brand"]))
				{
					echo
					'<div class="detail-d detail-d_brand">
						<span class="detail-d__name">'.$this->diafan->_('Производитель').':</span>
						<strong class="detail-d__content">
							<a href="'.BASE_PATH_HREF.$row["brand"]["link"].'">'.$row["brand"]["name"].'</a>
						</strong>
					</div>';
				}
				if(! empty($row["article"]))
				{
					echo
					'<div class="detail-d detail-d_code">
						<span class="detail-d__name">'.$this->diafan->_('Артикул').':</span>
						<strong class="detail-d__content">'.$row["article"].'</strong>
					</div>';
				}
				if($row["additional_cost"])
				{
					echo '<div class="detail-d detail-d_additions">';
					foreach($row["additional_cost"] as $a)
					{
						echo
						'<div class="addition-d">
							<span class="addition-d__name">'.$a["name"].':</span>';
							if($a["summ"])
							{
								echo '
								<span class="addition-d__content"> +
									<strong class="price-d">
										<span class="price-d__num">'.$a["format_summ"].'</span>
										<span class="price-d__curr">'.$result["currency"].'</span>
									</strong>
								</span>';
							}
							echo
						'</div>';
					}
					echo '</div>';
				}
				if(! empty($row["params_name"]))
				{
					echo '<div class="detail-d detail-d_params">';
					foreach($row["params_name"] as $p)
					{
						echo
						'<div class="param-d">
							<span class="param-d__name">'.$p["name"].':</span>
							<strong class="param-d__value">'.$p["value"].'</strong>
						</div>';
					}
					echo '</div>';
				}
				echo
			'</div>';

			echo '<div class="cell-d cell-d_count">';
			if(empty($result["hide_form"]))
			{
				echo
				'<div class="count-d js_cart_count" title="'.$this->diafan->_('Количество', false).'">
					<div class="count-d__control">
						<button class="count-d__dec fas fa-minus js_cart_count_minus" type="button"></button>
						<input name="editshop'.$row["id"].'" class="count-d__input js_count_input" type="text" value="'.$row["count"].'" data-min="0" data-max="'.$row["price_count"].'">
						<button class="count-d__inc fas fa-plus js_cart_count_plus" type="button"></button>
					</div>
				</div>';
			}
			else
			{
				echo $row["count"];
			}
			echo '</div>';

			echo '<div class="cell-d cell-d_unit">';
			echo $row["measure_unit"] ? $row["measure_unit"] : $this->diafan->_('шт.');
			echo '</div>';

			echo
			'<div class="cell-d cell-d_price">
				<strong class="price-d">
					<span class="price-d__num">'.($row["old_price"] ? $row["old_price"] : $row["price"]).'</span>
					<span class="price-d__curr">'.$result["currency"].'</span>
				</strong>
			</div>';

			if($result["discount"])
			{
				echo '<div class="cell-d cell-d_price_old">';
				if($row["old_price"])
				{
					echo
					'<strong class="price-d">
						<span class="price-d__num">'.$row["price"].'</span>
						<span class="price-d__curr">'.$result["currency"].'</span>
					</strong>';
				}
				echo '</div>';

				echo
				'<div class="cell-d cell-d_discount" title="'.$this->diafan->_('Скидка', false).'">
					<strong class="price-d">
						<span class="price-d__num">';
							if($row["percent"])
							{
								echo $row["percent"].'%';
							}
							else
							{
								echo $row["discount_summ"];
							}
							echo
						'</span>';
						if(! $row["percent"])
						{
							echo ' <span class="price-d__curr">'.$result["currency"].'</span>';
						}
						echo
					'</strong>
				</div>';
			}

			echo
			'<div class="cell-d cell-d_sum">
				<strong class="price-d">
					<span class="price-d__num">'.$row["summ"].'</span>
					<span class="price-d__curr">'.$result["currency"].'</span>
				</strong>
			</div>';

			echo
			'<div class="cell-d cell-d_remove">
				<span class="remover-d" title="'.$this->diafan->_('Убрать', false).'">
					<input class="remover-d__input" type="checkbox" id="rem'.$row["id"].'" name="del'.$row["id"].'" value="1">
					<label class="remover-d__icon icon-d far fa-times-circle js_cart_remove" for="rem'.$row["id"].'" data-confirm="'.$this->diafan->_('Вы действительно хотите удалить товар из корзины?', false).'"></label>
				</span>
			</div>';

			echo
		'</div>';
	}
	echo '</div>';

	// Скидочный купон. Шаблон, встроенный в форму корзины*/
	// echo $this->htmleditor('<insert name="show_add_coupon" module="shop" template="cart">');

	// общая скидка от объема
	if(! empty($result["discount_total"]))
	{
		echo
		'<div class="table-d__rows table-d__discounts">';
			echo
			'<div class="table-d__row">
				<div class="cell-d cell-d_total-discount">
					<strong class="price-d">
						<span class="price-d__name">'.$this->diafan->_('Скидка').':</span>
						<span class="price-d__num">';
							if($result["discount_total"]["percent"])
							{
								echo $result["discount_total"]["percent"].'%';
							}
							else
							{
								echo $result["discount_total"]["discount_summ"];
							}
							echo
						'</span>';
						if(! $result["discount_total"]["percent"])
						{
							echo  ' <span class="price-d__curr">'.$result["currency"].'</span>';
						}
						echo
					'</strong>
				</div>
			</div>';
			echo
		'</div>';
	}

	// следующая скидка
	if(! empty($result["discount_next"]))
	{
		echo
		'<div class="table-d__rows table-d__promises">';
			echo
			'<div class="table-d__row">
				<div class="cell-d cell-d_promise">';
					if(! empty($result["discount_next"]) && empty($result["hide_form"]))
					{
						if($result["discount_next"]["percent"])
						{
							$discount = $result["discount_next"]["percent"].'%';
						}
						else
						{
							$discount = $result["discount_next"]["discount_summ"].' '.$result["currency"];
						}
						echo $this->diafan->_('До скидки %s осталось %s', true, $discount, $result["discount_next"]["summ"].' '.$result["currency"]);
					}
					echo
				'</div>
			</div>';
			echo
		'</div>';
	}

	//итоговая строка для товаров
	echo
	'<div class="table-d__rows table-d__total table-d__total_goods">';
		echo
		'<div class="table-d__row">
			<div class="cell-d cell-d_images"></div>
			<div class="cell-d cell-d_details cell-d_title">'.$this->diafan->_('Итого').':</div>
			<div class="cell-d cell-d_count" title="'.$this->diafan->_('Количество', false).'"><strong>'.$result["count"].'</strong></div>
			<div class="cell-d cell-d_unit">'.$this->diafan->_('шт.').'</div>
			<div class="cell-d cell-d_price"></div>';
			if($result["discount"])
			{
				echo
				'<div class="cell-d cell-d_price_old"></div>
				<div class="cell-d cell-d_discount"></div>';
			}
			echo
			'<div class="cell-d cell-d_sum" title="'.$this->diafan->_('Сумма', false).'">
				<strong class="price-d">
					<span class="price-d__num">'.$result["summ_goods"].'</span>
					<span class="price-d__curr">'.$result["currency"].'</span>
				</strong>';
				if(! empty($result["old_summ_goods"]))
				{
					echo
					'<strong class="price-d price-d_old">
						<span class="price-d__num">'.$result["old_summ_goods"].'</span>
						<span class="price-d__curr">'.$result["currency"].'</span>
					</strong>';
				}
				echo
			'</div>
			<div class="cell-d cell-d_remove"></div>
		</div>';
		echo
	'</div>';

	echo '</section>';

	//дополнительно
	if (! empty($result["additional_cost"]))
	{
		echo
		'<section class="table-d__section table-d__additions">
			<div class="table-d__rows table-d__title">
				<div class="table-d__row">
					<h4 class="cell-d cell-d_title">'.$this->diafan->_('Дополнительно').'</h4>
				</div>
			</div>
			<div class="table-d__rows table-d__enum">';
				foreach ($result["additional_cost"] as $row)
				{
					echo
					'<div class="table-d__row">
						<div class="cell-d cell-d_details details-d">';
							echo '<div class="detail-d detail-d_name">'.$row["name"].'</div>';
							echo '<div class="detail-d detail-d_desc _text">';
								echo $row['text'];
								if ($row['amount'])
								{
									echo
									'<strong class="price-d _block">
										<span class="price-d__name">'.$this->diafan->_('Бесплатно от суммы').'</span>
										<span class="price-d__num">'.$row['amount'].'</span>
										<span class="price-d__curr">'.$result["currency"].'</span>
									</strong>';
								}
								if($row['percent'])
								{
									echo
									'<strong class="price-d _block">
										<span class="price-d__name">'.$this->diafan->_('Стоимость').'</span>
										<span class="price-d__num">'.$row['percent'].'%</span>
									</strong>';
								}
							echo '</div>';
							echo
						'</div>';
						echo
						'<div class="cell-d cell-d_sum">
							<strong class="price-d">
								<span class="price-d__num">'.$row["summ"].'</span>
								<span class="price-d__curr">'.$result["currency"].'</span>
							</strong>
						</div>';
						echo
						'<div class="cell-d cell-d_select">
							<div class="field-d">
								<input name="additional_cost_ids[]" value="'.$row['id'].'" type="checkbox" id="additional_cost_'.$row['id'].'"'.($row["checked"] ? ' checked' : '').'>
								<label for="additional_cost_'.$row['id'].'"></label>
							</div>
						</div>
					</div>';
				}
				echo
			'</div>
		</section>';
	}

	//способы доставки
	if (! empty($result["delivery"]))
	{
		echo
		'<section class="table-d__section table-d__deliveries">
			<div class="table-d__rows table-d__title">
				<div class="table-d__row">
					<h4 class="cell-d cell-d_title">'.$this->diafan->_('Способ доставки').'</h4>
				</div>
			</div>
			<div class="table-d__rows table-d__enum">';
				foreach ($result["delivery"] as $row)
				{
					echo
					'<div class="table-d__row">';
						if (! empty($row["thresholds"]) && empty($result["hide_form"]))
						{
							foreach ($row["thresholds"]  as $r)
							{
								if($r["amount"])
								{
									$row['text'] .= '<br>'.($r["price"] ? $this->diafan->_('Стоимость').' '.$r["price"].' '.$result["currency"].' '.$this->diafan->_('от суммы') : $this->diafan->_('Бесплатно от суммы')).' '.$r['amount'].' '.$result["currency"];
								}
								else
								{
									$row['text'] .= '<br>'.($r["price"] ? $this->diafan->_('Стоимость').' '.$r["price"].' '.$result["currency"] : $this->diafan->_('Бесплатно'));
								}
							}
							if(! empty($row["discount_total"]))
							{
								$row['text'] .= '<br>'.$this->diafan->_('Скидка на сумму заказа').' '.$row["discount_total"]["discount_summ"].' '.$row["discount_total"]["currency"];
							}
						}
						echo
						'<div class="cell-d cell-d_details details-d">
							<div class="detail-d detail-d_name">'.$row["name"].'</div>
							<div class="detail-d detail-d_desc _text">'.$row['text'].'</div>
						</div>';
						echo
						'<div class="cell-d cell-d_sum">
							<strong class="price-d">
								<span class="price-d__num">';
									if (is_null($row["price"]))
									{
										echo $this->diafan->_('Недоступно');
									}
									elseif ($row["price"] !== false)
									{
										echo $row["price"];
									}
									echo
								'</span>';
								if (! is_null($row["price"]) && $row["price"] !== false)
								{
									echo ' <span class="price-d__curr">'.$result["currency"].'</span>';
								}
								echo
							'</strong>
						</div>';
						echo
						'<div class="cell-d cell-d_select">
							<div class="field-d">
								<input name="delivery_id" id="delivery_id_'.$row['id'].'" value="'.$row['id'].'" type="radio" '.($row["selected"] ? ' checked' : '').' data-service="'.$row["service"].'">
								<label for="delivery_id_'.$row['id'].'"></label>
							</div>
						</div>
					</div>';
					if ($row["service_view"])
					{
						echo '<div class="table-d__row table-d__delivservice _block">';
						echo $row["service_view"];
						echo '</div>';
					}
				}
				echo
			'</div>
		</section>';
	}
}

echo
'<section class="table-d__section table-d__fulltotal">
	<div class="table-d__rows table-d__total table-d__total_cart">';

		echo
		'<div class="table-d__row">
			<div class="cell-d cell-d_details cell-d_title">'.$this->diafan->_('Итого').':</div>
			<div class="cell-d cell-d_total">
				<strong class="price-d">
					<span class="price-d__num">'.$result["summ"].'</span>
					<span class="price-d__curr">'.$result["currency"].'</span>
				</strong>';
				if(! empty($result["tax"]))
				{
					echo
					'<strong class="price-d price-d_tax _block">
						<span class="price-d__name">'.$this->diafan->_('в т. ч. %s', true, $result["tax_name"]).'</span>
						<br>
						<span class="price-d__num">'.$result["tax"].'</span>
						<span class="price-d__curr">'.$result["currency"].'</span>
					</strong>';
				}
				echo
			'</div>';
			echo
		'</div>';

		echo
	'</div>
</section>';

echo '</div>';

if(! empty($result["share_link"]))
{
	echo '<br>
	<input type="text" placeholder="'.$this->diafan->_('Введите e-mail', false).'" value="" id="js_share_link_email_input" style="display:none">
	<a href="'.BASE_PATH_HREF.$result["share_link"].'" class="button-d" id="js_cart_share_email" data-send="'.$this->diafan->_('Отправить', false).'">'.$this->diafan->_('Отправить товары из корзины на E-mail').'</a>';

	echo '<a href="'.BASE_PATH_HREF.$result["share_link"].'" class="button-d" id="js_cart_share_copy" data-result="'.$this->diafan->_('Скопировано', false).'">'.$this->diafan->_('Скопировать ссылку на корзину').'</a>

	<div class="errors error-share" id="js_share_link_error" style="display:none"></div>';
}
