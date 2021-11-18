<?php
/**
 * Шаблон элементов в списке товаров
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



if(empty($result['rows'])) return false;

foreach ($result['rows'] as $row)
{
	echo '<div class="slide-d swiper-slide">';

	echo
	'<article class="element-d element-d_card element-d_shop element-d_shop_item js_shop">';

		echo
		'<div class="element-d__images">';
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

					if(! empty($result['search']))
					{
						break;
					}
				}
			}
			else
			{
				echo
				'<a href="'.BASE_PATH_HREF.$row["link"].'">
					<figure class="_dummyimage"></figure>
				</a>';
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
			if(empty($result['search']))
			{
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
					if(empty($result['hide_compare']))
					{
						echo $this->get('compare_form', 'shop', $row);
					}
					echo
				'</div>';
			}
			echo
		'</div>';

		echo
		'<div class="element-d__details details-d">
			<div class="details-d__enum">';
				//вывод названия и ссылки на товар
				echo
				'<div class="detail-d detail-d_name">
					<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>
				</div>';
				//рейтинг товара
				if(! empty($row["rating"]))
				{
					echo '<div class="detail-d detail-d_rating">'.$row["rating"].'</div>';
				}
				//вывод краткого описания товара
				if(! empty($row["anons"]))
				{
					echo '<div class="detail-d detail-d_anons _text">'.$this->htmleditor($row['anons']).'</div>';
				}
				echo
				'<div class="detail-d detail-d_params">';
					//вывод производителя
					if(! empty($row["brand"]))
					{
						echo
						'<div class="param-d param-d_brand">
							<span class="param-d__name">'.$this->diafan->_('Производитель').': </span>
							<strong class="param-d__value">
								<a href="'.BASE_PATH_HREF.$row["brand"]["link"].'">'.$row["brand"]["name"].'</a>
							</strong>
						</div>';
					}
					//вывод артикула
					if(! empty($row["article"]))
					{
						echo
						'<div class="param-d param-d_code">
							<span class="param-d__name">'.$this->diafan->_('Артикул').': </span>
							<strong class="param-d__value">'.$row["article"].'</strong>
						</div>';
					}
					//вывод параметров товара
					if(empty($result['search']) && ! empty($row["param"]))
					{
						echo $this->get('param', 'shop', array("rows" => $row["param"], "id" => $row["id"]));
					}
					echo
				'</div>';
				//теги товара
				if(! empty($row["tags"]))
				{
					echo '<div class="detail-d detail-d_tags">'.$row["tags"].'</div>';
				}
				echo
			'</div>';
			if(empty($result['search']))
			{
				//вывод кнопки "Купить"
				echo $this->get('buy_form', 'shop', array("row" => $row, "result" => $result));
			}
			echo
		'</div>

	</article>';

	echo '</div>';
}
