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
	echo '<p class="_note">'.$this->diafan->_('Некорректная ссылка.').' <a href="'.BASE_PATH_HREF.$result["shop_link"].'">'.$this->diafan->_('Перейти к покупкам.').'</a></p>';
	return;
}

echo '<a name="top"></a>';

echo '<section class="section-d section-d_home section-d_cart section-d_cart_home">';

echo
'<form class="cart__invoice ajax" method="POST" action="">
	<input type="hidden" name="module" value="cart">
	<input type="hidden" name="action" value="">
	<input type="hidden" name="share" value="'.$result["share"].'">';
	//вывод таблицы с товарами
	echo '<div class="cart_table">';

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
			echo $row["count"];
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
		'</div>';
	}
	echo '</div>';

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
				echo
			'</div>
			<div class="cell-d cell-d_remove"></div>
		</div>';
		echo
	'</div>';

	echo '</section>';
}

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
						</div>
					</div>';
				}
				echo
			'</div>
		</section>';
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
				</strong>
			</div>';
			echo
		'</div>';

		echo
	'</div>
</section>';

echo '</div>';
	echo '</div>';
if($result["is_cart"])
{
	echo '<input class="js_cart_share" type="submit" value="'.$this->diafan->_('Очистить Вашу корзину и добавить туда отправленные товары', false).'" data-action="share_cart">';
	echo '<input class="js_cart_share" type="submit" value="'.$this->diafan->_('Добавить товары из отправленной Вам корзины в избранное', false).'" data-action="share_wishlist">';
}
else
{
	echo '<input class="js_cart_share" type="submit" value="'.$this->diafan->_('Добавить в корзину', false).'" data-action="share_cart">';
}
echo '
</form>';
