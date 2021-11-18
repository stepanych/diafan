<?php
/**
 * Шаблон информации о товарах в корзине
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



$goods = '%s товаров на %s';
if(substr($result["count"], -1) == 1 || substr($result["count"], -1) == 1 && substr($result["count"], -2, 1) != 1)
{
	$goods = '%s товар на %s';
}
elseif(substr($result["count"], -1) > 1 && substr($result["count"], -1) < 5 && substr($result["count"], -2, 1) != 1)
{
	$goods = '%s товара на %s';
}

echo
'<a class="intercap-d__tip tip-d" href="'.$result['link'].'" rel="nofollow" title="'.str_replace('"', '', $this->diafan->_($goods, false, $result['count'], $result['summ'].' '.$result['currency'])).'">
	<span class="tip-d__icon icon-d fas fa-shopping-bag"></span>
	<span class="tip-d__amount amount-d amount-d_stick">
		<strong class="amount-d__num">';
			if(! empty($result['count']))
			{
				echo $result['count'];
			}
			echo
		'</strong>
	</span>
</a>';

if(! empty($result['rows']))
{
	echo
	'<div class="intercap-d__result">
		<div class="intercap-d__box _scroll">';
		
			// echo
			// '<a class="intercap-d__button intercap-d__button_cart intercap-d__button_cart_top button-d" href="'.$result['link'].'">
				// <span class="button-d__icon icon-d fas fa-shopping-bag"></span>
				// <span class="button-d__name">'.$this->diafan->_('Оформить').'</span>
			// </a>';

			echo
			'<form action="" method="POST" class="js_cart_block_form cart_block_form ajax">
				<input type="hidden" name="module" value="cart">
				<input type="hidden" name="action" value="recalc">
				<input type="hidden" name="form_tag" value="'.$result["form_tag"].'">';

				echo
				'<div class="table-d table-d_intercap table-d_intercap_cart table-d_sm table-d_md table-d_lg">';

					echo
					'<div class="table-d__rows table-d__head">
						<div class="table-d__row">
							<div class="cell-d cell-d_images"></div>
							<div class="cell-d cell-d_details">'.$this->diafan->_('Наименование').'</div>
							<div class="cell-d cell-d_count">'.$this->diafan->_('Количество').'</div>
							<div class="cell-d cell-d_price">'.$this->diafan->_('Стоимость').'</div>
							<div class="cell-d cell-d_sum">'.$this->diafan->_('Сумма').'</div>
							<div class="cell-d cell-d_remove"></div>
						</div>
					</div>';

					echo
					'<div class="table-d__rows table-d__enum">';

						foreach ($result["rows"] as $row)
						{
							echo
							'<div class="table-d__row">
								<div class="cell-d cell-d_images">
									<a href="'.BASE_PATH_HREF.$row["link"].'">';
										if (! empty($row["img"]))
										{
											echo '<img src="'.$row["img"]["src"].'" width="'.$row["img"]["width"].'" height="'.$row["img"]["height"].'" alt="'.$row["img"]["alt"].'" title="'.$row["img"]["title"].'">';
										}
										else
										{
											echo '<figure class="_dummyimage"></figure>';
										}
										echo
									'</a>
								</div>
								<div class="cell-d cell-d_details details-d" title="'.$this->diafan->_('Наименование', false).'">';
									echo '<div class="detail-d detail-d_name">';
									if(! empty($row["cat"]))
									{
										echo '<a href="'.BASE_PATH_HREF.$row["cat"]["link"].'">'.$row["cat"]["name"].'</a> / ';
									}
									echo '<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>';
									echo '</div>';
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
									if(! $row["count"])
									{
										echo '<div class="detail-d detail-d_available _unavailable">'.$this->diafan->_('Товар временно отсутствует').'</div>';
									}
									echo
								'</div>
								<div class="cell-d cell-d_count">
									<div class="count-d js_cart_count" title="'.$this->diafan->_('Количество', false).'">
										<div class="count-d__control">
											<button class="count-d__dec fas fa-minus js_cart_count_minus" type="button"></button>
											<input type="text" class="count-d__input number js_count_input" value="'.$row["count"].'" min="0" name="editshop'.$row["id"].'" size="2">
											<button class="count-d__inc fas fa-plus js_cart_count_plus" type="button"></button>
										</div>
									</div>
								</div>
								<div class="cell-d cell-d_price" title="'.$this->diafan->_('Цена', false).'">
									<strong class="price-d">
										<span class="price-d__num">'.$row["price"].'</span>
										<span class="price-d__curr">'.$result["currency"].'</span>
									</strong>
								</div>
								<div class="cell-d cell-d_sum" title="'.$this->diafan->_('Сумма', false).'">
									<strong class="price-d">
										<span class="price-d__num">'.$row["summ"].'</span>
										<span class="price-d__curr">'.$result["currency"].'</span>
									</strong>
								</div>
								<div class="cell-d cell-d_remove">';
									$rand = rand(0, 99999);
									echo
									'<span class="remover-d js_cart_remove" confirm="'.$this->diafan->_('Вы действительно хотите удалить товар из корзины?', false).'" title="'.$this->diafan->_('Убрать', false).'">
										<input class="remover-d__input" type="checkbox" id="del'.$row["id"].$rand.'" name="del'.$row["id"].'" value="1">
										<label class="remover-d__icon icon-d far fa-times-circle" for=""></label>
									</span>
								</div>
							</div>';
						}

						echo
					'</div>';

					echo
					'<div class="table-d__rows table-d__total">
						<div class="table-d__row">
							<div class="cell-d cell-d_images"></div>
							<div class="cell-d cell-d_details cell-d_title">'.$this->diafan->_('Итого за товары').'</div>
							<div class="cell-d cell-d_count" title="'.$this->diafan->_('Количество', false).'"><strong>'.$result["count"].'</strong></div>
							<div class="cell-d cell-d_price"'.(! empty($result["discount_total"]) ? ' title="'.$this->diafan->_('Цена', false).'"' : '').'>';
								if(! empty($result["discount_total"]))
								{
									echo
									'<strong class="price-d">
										<span class="price-d__num">'.$result["old_summ_goods"].'</span>
										<span class="price-d__curr">'.$result["currency"].'</span>
									</strong>';
								}
								echo
							'</div>
							<div class="cell-d cell-d_sum" title="'.$this->diafan->_('Сумма', false).'">
								<strong class="price-d">
									<span class="price-d__num">'.$result["summ_goods"].'</span>
									<span class="price-d__curr">'.$result["currency"].'</span>
								</strong>
							</div>
							<div class="cell-d cell-d_remove"></div>
						</div>
					</div>';

					echo
				'</div>
				<div class="error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>
			</form>';

			echo
			'<a class="intercap-d__button intercap-d__button_cart intercap-d__button_cart_bottom button-d" href="'.$result['link'].'">
				<span class="button-d__icon icon-d fas fa-shopping-bag"></span>
				<span class="button-d__name">'.$this->diafan->_('Оформить').'</span>
			</a>';

			echo
		'</div>
	</div>';
}
