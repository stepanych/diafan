<?php
/**
 * Шаблон таблицы с товарами в списке желаний
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
'<div class="table-d table-d_wishlist table-d_xs table-d_sm table-d_md table-d_lg">';

	echo
	'<div class="table-d__rows table-d__head">
		<div class="table-d__row">
			<div class="cell-d cell-d_images"></div>
			<div class="cell-d cell-d_details">'.$this->diafan->_('Наименование').'</div>
			<div class="cell-d cell-d_count">'.$this->diafan->_('Количество').'</div>
			<div class="cell-d cell-d_price">'.$this->diafan->_('Цена').'</div>
			<div class="cell-d cell-d_sum">'.$this->diafan->_('Сумма').'</div>
			<div class="cell-d cell-d_buy">'.$this->diafan->_('Купить').'</div>
			<div class="cell-d cell-d_remove"></div>
		</div>
	</div>';

	if(! empty($result["rows"]))
	{
		echo '<div class="table-d__rows table-d__enum">';
		foreach ($result["rows"] as $row)
		{
			echo
			'<div class="table-d__row js_wishlist_item">
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
				<div class="cell-d cell-d_details details-d">';
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
					if($row["additional_cost"])
					{
						echo '<div class="detail-d detail-d_additions">';
						foreach($row["additional_cost"] as $a)
						{
							echo
							'<div class="addition-d">
								<span class="addition-d__name">'.$a["name"].'</span>';
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
					echo
				'</div>
				<div class="cell-d cell-d_count">
					<div class="count-d js_wishlist_count">
						<div class="count-d__control">
							<button class="count-d__dec fas fa-minus js_wishlist_count_minus" type="button"></button>
							<input type="text" class="number count-d__input js_count_input" value="'.$row["count"].'" min="0" name="editshop'.$row["id"].'" size="2">
							<button class="count-d__inc fas fa-plus js_wishlist_count_plus" type="button"></button>
						</div>
					</div>
				</div>
				<div class="cell-d cell-d_price">
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
				<div class="cell-d cell-d_buy js_wishlist_buy">';
					if($row["buy"] &&  $result["access_buy"])
					{
						echo '<input class="button-d button-d_narrow" type="button" value="'.$this->diafan->_('Купить', false).'" good_id="'.$row["id"].'">';
					}
					echo
				'</div>
				<div class="cell-d cell-d_remove">
					<span class="remover-d js_wishlist_remove" confirm="'.$this->diafan->_('Вы действительно хотите удалить товар из списка отложенных товаров?', false).'" title="'.$this->diafan->_('Убрать', false).'">
						<input class="remover-d__input" type="checkbox" id="del'.$row["id"].'" name="del'.$row["id"].'" value="1">
						<label class="remover-d__icon icon-d far fa-times-circle" for="del'.$row["id"].'"></label>
					</span>
				</div>
			</div>';
		}
		echo '</div>';
	}

	//итоговая строка таблицы
	echo
	'<div class="table-d__rows table-d__total">';
		echo
		'<div class="table-d__row">
			<div class="cell-d cell-d_images"></div>
			<div class="cell-d cell-d_details cell-d_title">'.$this->diafan->_('Итого').':</div>
			<div class="cell-d cell-d_count" title="'.$this->diafan->_('Количество', false).'"><strong>'.$result["count"].'</strong></div>
			<div class="cell-d cell-d_price"></div>
			<div class="cell-d cell-d_sum" title="'.$this->diafan->_('Сумма', false).'">
				<strong class="price-d">
					<span class="price-d__num">'.$result["summ"].'</span>
					<span class="price-d__curr">'.$result["currency"].'</span>
				</strong>
			</div>
			<div class="cell-d cell-d_buy"></div>
			<div class="cell-d cell-d_remove"></div>
		</div>';
		echo
	'</div>';

	echo 
'</div>';
