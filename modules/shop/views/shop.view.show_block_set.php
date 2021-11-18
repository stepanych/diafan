<?php
/**
 * Шаблон набора товаров
 *
 * Шаблонный тег <insert name="show_block_set" module="shop" [count="количество"]
 * [images="количество_изображений"] [images_variation="тег_размера_изображений"]
 * [template="шаблон"]>:
 * блок похожих товаров
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

echo '<section class="block-d block-d_gall block-d_shop block-d_shop_rel block-d_shop_item block-d_shop_item_rel">';

echo '<header class="block-d__name">'.$this->diafan->_('Данный товар состоит из товаров').'</header>';

echo
'<div class="gall-d gall-d_navbottom swiper-container" data-gall-show="1" data-gall-gap="30" data-gall-breakpoints=\'{"576":{"slidesPerView":2},"768":{"slidesPerView":3},"922":{"slidesPerView":2},"1200":{"slidesPerView":3}}\'>';

	echo '<div class="gall-d__list swiper-wrapper">';
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
				$img = $row["img"][0];
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
			else
			{
				echo
				'<a href="'.BASE_PATH_HREF.$row["link"].'">
					<figure class="_dummyimage"></figure>
				</a>';
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
				echo
			'</div>';
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
echo '</div>';

echo '</section>';
