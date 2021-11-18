<?php
/**
 * Шаблон вывода информации о последнем совершенном заказе
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
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



//JS-layer for analitics ecommerce, dataLayer
$ecommerce_for_metrika = '<script type="text/javascript">
window.dataLayer.push({
    "ecommerce": {
        "purchase": {
            "actionField": {
                "id" : "'.$result["id"].'"
            },
			"products": [';
			
echo '<section class="section-d section-d_cart section-d_cart_order">';

echo '<div class="table-d table-d_cart table-d_cart_order table-d_sm table-d_md table-d_lg">';

//товары
if(! empty($result["rows"]))
{
	echo
	'<section class="table-d__section table-d__goods">';

		//шапка таблицы
		echo
		'<div class="table-d__rows table-d__head">
			<div class="table-d__row">
				<div class="cell-d cell-d_images"></div>
				<div class="cell-d cell-d_details">'.$this->diafan->_('Наименование').'</div>
				<div class="cell-d cell-d_count">'.$this->diafan->_('Количество').'</div>
				<div class="cell-d cell-d_unit">'.$this->diafan->_('Единицы измерения').'</div>
				<div class="cell-d cell-d_price">'.$this->diafan->_('Цена').'</div>';
				if($result["discount"])
				{
					echo
					'<div class="cell-d cell-d_price_old">'.$this->diafan->_('Цена со скидкой').'</div>
					<div class="cell-d cell-d_discount">'.$this->diafan->_('Скидка').'</div>';
				}
				echo
				'<div class="cell-d cell-d_sum">'.$this->diafan->_('Сумма').'</div>
			</div>
		</div>

		<div class="table-d__rows table-d__enum">';
			foreach ($result["rows"] as $row)
			{
				$ecommerce_for_metrika .= '    {
					"id": "'.$row["id"].'",
					"name": "'.$row["name"].'",
					"price": '.(preg_replace('/[^0-9.,]/', '', $row["price"])).',
					"category": "'.$row["cat"]["name"].'",
					';
					
				echo
				'<div class="table-d__row">';

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
						if(! empty($row["cat"]))
						{
							echo
							'<nav class="detail-d detail-d_breadcrumbs">
								<a href="'.BASE_PATH_HREF.$row["cat"]["link"].'">'.$row["cat"]["name"].'</a> / 
							</nav>';
						}
						echo
						'<div class="detail-d detail-d_name">
							<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>
						</div>';
						if(! empty($row["article"]))
						{
							echo
							'<div class="detail-d detail-d_code">
								<span class="detail-d__name">'.$this->diafan->_('Артикул').':</span>
								<strong class="detail-d__content">'.$row["article"].'</strong>
							</div>';
						}
						if(! empty($row["param"]))
						{
							echo
							'<div class="detail-d detail-d_params">';
								foreach($row["param"] as $name => $value)
								{
									echo
									'<div class="param-d">
										<span class="param-d__name">'.$name.'</span>
										<strong class="param-d__value">'.$value.'</strong>
									</div>';

									$ecommerce_for_metrika .= '"variant": "'.$value.'",
									';
								}
								echo
							'</div>';
						}
						if($row["additional_cost"])
						{
							echo
							'<div class="detail-d detail-d_additions">';
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
								echo
							'</div>';
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
						echo
						'<div class="cell-d cell-d_price_old">';
							if($row["old_price"])
							{
								echo
								'<strong class="price-d">
									<span class="price-d__num">'.$row["price"].'</span>
									<span class="price-d__curr">'.$result["currency"].'</span>
								</strong>';
							}
							echo
						'</div>
						<div class="cell-d cell-d_discount" title="'.$this->diafan->_('Скидка', false).'">
							<strong class="price-d">
								<span class="price-d__num">'.(! empty($row["discount"]) ? $row["discount"] : '').'</span>
							</strong>
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

				$ecommerce_for_metrika .= '"quantity": '.$row["count"].'
					},';
			}
			echo
		'</div>';

		// общая скидка от объема
		if(! empty($result["discount_summ"]))
		{
			echo
			'<div class="table-d__rows table-d__discounts">';
				echo
				'<div class="table-d__row">
					<div class="cell-d cell-d_total-discount">
						<strong class="price-d">
							<span class="price-d__name">'.$this->diafan->_('Скидка').':</span>
							<span class="price-d__num">'.$result["discount_summ"].'</span>
							<span class="price-d__curr">'.$result["currency"].'</span>
						</strong>
					</div>
				</div>';
				echo
			'</div>';
		}

		if(! empty($result["coupon"]))
		{
			echo
			'<div class="table-d__rows table-d__coupons">';
				echo
				'<div class="table-d__row">
					<div class="cell-d cell-d_coupon">
						<strong class="price-d">
							<span class="price-d__name">'.$this->diafan->_('Купон').':</span>
							<span class="price-d__num">'.$result["coupon"].'</span>
						</strong>
					</div>
				</div>';
				echo
			'</div>';
		}

		//итоговая строка для товаров
		echo
		'<div class="table-d__rows table-d__total">';
			echo
			'<div class="table-d__row">
				<div class="cell-d cell-d_images"></div>
				<div class="cell-d cell-d_details">'.$this->diafan->_('Итого за товары').':</div>
				<div class="cell-d cell-d_count">'.$result["count"].'</div>
				<div class="cell-d cell-d_unit">'.$this->diafan->_('шт.').'</div>
				<div class="cell-d cell-d_price"></div>';
				if($result["discount"])
				{
					echo
					'<div class="cell-d cell-d_price_old"></div>
					<div class="cell-d cell-d_discount"></div>';
				}
				echo
				'<div class="cell-d cell-d_sum">
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
			</div>';
			echo
		'</div>

	</section>';
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
					<div class="cell-d cell-d_details details-d">
						<div class="detail-d detail-d_name">'.$row["name"].'</div>
						<div class="detail-d detail-d_desc _text">';
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
							echo
						'</div>
					</div>
					<div class="cell-d cell-d_sum">
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

$ecommerce_for_metrika .= '            ]
        }
    }
});
</script>';

//способы доставки
if (! empty($result["delivery"]))
{
	echo
	'<section class="table-d__section table-d__deliviries">
		<div class="table-d__rows table-d__title">
			<div class="table-d__row">
				<h4 class="cell-d cell-d_title">'.$this->diafan->_('Способ доставки').'</h4>
			</div>
		</div>
		<div class="table-d__rows table-d__enum">';
			echo
			'<div class="table-d__row">
				<div class="cell-d cell-d_details details-d">
					<div class="detail-d detail-d_name">'.$result["delivery"]["name"].'</div>
				</div>
				<div class="cell-d cell-d_sum">
					<strong class="price-d">
						<span class="price-d__num">'.$result["delivery"]["summ"].'</span>
						<span class="price-d__curr">'.$result["currency"].'</span>
					</strong>
				</div>
			</div>';
			echo
		'</div>
	</section>';
}

echo
'<section class="table-d__section table-d__fulltotal">
	<div class="table-d__rows table-d__total">';
		echo
		'<div class="table-d__row">
			<div class="cell-d cell-d_details cell-d_title">'.$this->diafan->_('Итого к оплате').':</div>
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

echo '<div class="params-d">';
foreach($result["param"] as $param)
{
	echo '<div class="param-d">';
	echo '<div class="param-d__name">'.$param["name"].':</div>';
	if ($param["value"])
	{
		echo '<strong class="param-d__value">';
		if($param["type"] == "attachments")
		{
			foreach ($param["value"] as $a)
			{
				if ($a["is_image"])
				{
					if($param["use_animation"])
					{
						echo ' <a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'cart"><img src="'.$a["link_preview"].'"></a> <a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'cart_link">'.$a["name"].'</a>';
					}
					else
					{
						echo ' <a href="'.$a["link"].'"><img src="'.$a["link_preview"].'"></a> <a href="'.$a["link"].'">'.$a["name"].'</a>';
					}
				}
				else
				{
					echo ' <a href="'.$a["link"].'">'.$a["name"].'</a>';
				}
			}
		}
		elseif($param["type"] == "images")
		{
			foreach ($param["value"] as $img)
			{
				if($img["source"])
				{
					echo $img["source"];
				}
				else
				{
					echo '<img src="'.$img["src"].'" width="'.$img["width"].'" height="'.$img["height"].'" alt="'.$img["alt"].'" title="'.$img["title"].'">';
				}
			}
		}
		elseif (is_array($param["value"]))
		{
			foreach ($param["value"] as $p)
			{
				if ($param["value"][0] != $p)
				{
					echo ', ';
				}
				if (is_array($p))
				{
					if ($p["link"])
					{
						echo '<a href="'.BASE_PATH_HREF.$p["link"].'">'.$p["name"].'</a>';
					}
					else
					{
						echo $p["name"];
					}
				}
				else
				{
					echo $p;
				}
			}
		}
		else
		{
			echo $param["value"];
		}
		echo '</strong>';
	}
	if($param["text"])
	{
		echo '<div class="param-d__text _text">'.$param["text"].'</div>';
	}
	echo '</div>';
}
echo '</div>';

echo '</section>';

//-------js для счетчиков, передающих информацию о продажах
echo '<script type="text/javascript">
window.dataLayer = window.dataLayer || [];
</script>';
echo $ecommerce_for_metrika;
//--------js для счетчиков, передающих информацию о продажах
