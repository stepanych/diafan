<?php
/**
 * Шаблон страницы сравнения товаров
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



if (empty($result['rows']))
{
	echo '<p class="_note">'.$this->diafan->_('Не выбраны товары для сравнения.').'</p>';
	return;
}

$result["one_click"] = false;

echo '<section class="section-d section-d_shop section-d_shop_compare">';

echo
'<section class="compare-d">
	<div class="compare-d__params">';
		if (! empty($result['existed_params']))
		{
			foreach ($result['existed_params'] as $param)
			{
				echo
				'<div class="compare-d__param param-d param-d_compare'.(in_array($param['id'], $result['param_differences']) ? ' _diff' : '').'" data-param_id="'.$param['id'].'">
					<span class="param-d__name">'.$param['name'].'</span>
				</div>';
			}
			if(count($result['existed_params']) != count($result['param_differences']))
			{
				echo
				'<button class="compare-d__button compare-d__button_toggle button-d button-d_short" type="button"
					data-name1="'.$this->diafan->_('Показать все', false).'"
					data-name2="'.$this->diafan->_('Убрать совпадающие', false).'">
					<span class="button-d__name">'.$this->diafan->_('Убрать совпадающие', false).'</span>
				</button>';
			}
		}
		echo
	'</div>
	<div class="compare-d__goods">

		<div class="compare-d__gall gall-d gall-d_navbottom swiper-container" data-gall-show="1" data-gall-gap="30" data-gall-pagintype="progressbar" data-gall-breakpoints=\'{"768":{"slidesPerView":2},"922":{"slidesPerView":1},"1024":{"slidesPerView":2}}\'>';

			echo '<div class="gall-d__list swiper-wrapper">';
			foreach ($result["rows"] as $row)
			{
				echo '<div class="slide-d swiper-slide">';

				echo
				'<article class="element-d element-d_card element-d_shop element-d_shop_item element-d_shop_item_compare js_shop">
					<div class="element-d__images">';

						//вывод изображений товара
						if(! empty($row["img"]))
						{
							foreach ($row["img"] as $img)
							{
								switch ($img["type"])
								{
									case 'animation':
										echo '<a class="_fit js_shop_img" image_id="'.$img["id"].'" href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$row["id"].'shop">';
										break;
									case 'large_image':
										echo '<a class="_fit js_shop_img" image_id="'.$img["id"].'" href="'.BASE_PATH.$img["link"].'" rel="large_image" width="'.$img["link_width"].'" height="'.$img["link_height"].'">';
										break;
									default:
										echo '<a class="_fit js_shop_img" image_id="'.$img["id"].'" href="'.BASE_PATH_HREF.$img["link"].'">';
										break;
								}
								if($img["source"])
								{
									echo $img["source"];
								}
								else
								{
									echo '<img src="'.$img["src"].'" alt="'.$img["alt"].'" title="'.$img["title"].'">';
								}
								echo '</a>';
							}
						}
						else
						{
							echo '<figure class="_dummyimage"></figure>';
						}

						echo '<div class="element-d__stickers stickers-d">';
						if(! empty($row['hit']))
						{
							echo '<div class="sticker-d sticker-d_hit">'.$this->diafan->_('Хит').'</div>';
						}
						if(! empty($row['action']))
						{
							echo '<div class="sticker-d sticker-d_action">'.$this->diafan->_('Акция').'</div>';
						}
						if(! empty($row['new']))
						{
							echo '<div class="sticker-d sticker-d_new">'.$this->diafan->_('Новинка').'</div>';
						}
						echo '</div>';

						echo
						'<div class="element-d__interacts interacts-d">';
                            if(empty($result["access_buy"]))
                            {
                                echo '
                                <button class="interact-d interact-d_wish'.(! empty($row['wish']) ? ' _active' : '').' js_shop_wishlist" type="button"
    								title="'.(! empty($row['wish']) ? $this->diafan->_('Убрать из избранного', false) : $this->diafan->_('Добавить в избранное', false)).'"
    								data-title1="'.$this->diafan->_('Добавить в избранное', false).'"
    								data-title2="'.$this->diafan->_('Убрать из избранного', false).'">
    								<span class="interact-d__icon icon-d far fa-heart"></span>
    							</button>';
                            }
							echo '
							<button class="interact-d interact-d_compare js_shop_compare_delete" type="button"
								title="'.$this->diafan->_('Удалить из списка для сравнения', false).'"
								data-id="'.$row["id"].'"
								data-site_id="'.$row["site_id"].'">
								<span class="interact-d__icon icon-d fas fa-times"></span>
							</button>
						</div>';

						echo
					'</div>';

					echo
					'<div class="element-d__details details-d">';

						echo '<div class="detail-d detail-d_params">';
						echo $this->get('compare_param', 'shop', array("params" => $row["param"], "id" => $row["id"], "existed_params" => $result['existed_params'], "param_differences" => $result["param_differences"]));
						//вывод артикула
						if(! empty($row["article"]))
						{
							echo
							'<div class="param-d param-d_code">
								<span class="param-d__name">'.$this->diafan->_('Артикул').': </span>
								<strong class="param-d__value">'.$row["article"].'</strong>
							</div>';
						}
						echo '</div>';

						//вывод названия и ссылки на товар
						echo
						'<div class="detail-d detail-d_name">
							<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>
						</div>';

						echo $this->get('buy_form', 'shop', array("row" => $row, "result" => $result));

						echo
					'</div>';

					echo
				'</article>';

				echo '</div>';
			}
			echo '</div>';

			echo
			'<div class="gall-d__nav">
				<button class="gall-d__button gall-d__button_prev swiper-button-prev" title="'.$this->diafan->_('Предыдущий', false).'" type="button">
					<span class="icon-d fas fa-chevron-circle-left"></span>
				</button>
				<button class="gall-d__button gall-d__button_next swiper-button-next" title="'.$this->diafan->_('Следующий', false).'" type="button">
					<span class="icon-d fas fa-chevron-circle-right"></span>
				</button>
			</div>';
			echo '<div class="gall-d__pagin swiper-pagination"></div>';
			echo
		'</div>

	</div>
</section>';

echo
'<form method="POST" action="" class="ajax">
	<input type="hidden" name="module" value="shop">
	<input type="hidden" name="action" value="compare_delete_goods">
	<button class="button-d" type="submit">
		<span class="button-d__name">'.$this->diafan->_('Очистить список сравнения').'</span>
	</button>
</form>';

echo '</section>';
