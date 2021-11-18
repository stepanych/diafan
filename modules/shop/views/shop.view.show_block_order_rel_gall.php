<?php
/**
 * Шаблон блока товаров, которые обычно покупают с текущим товаром
 *
 * Шаблонный тег <insert name="show_block" module="shop" [count="количество"]
 * [images="количество_изображений"] [images_variation="тег_размера_изображений"]
 * [template="шаблон"]>:
 * блок товаров, которые обычно покупают с текущим товаром
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



if(! empty($result["rows"]))
{
	echo '<section class="block-d block-d_gall block-d_shop block-d_shop_order_rel block-d_shop_item block-d_shop_item_order_rel">';

	echo '<header class="block-d__name">'.$this->diafan->_('C этим товаром покупают').'</header>';

	echo
	'<div class="gall-d gall-d_navbottom swiper-container" data-gall-show="1" data-gall-gap="30" data-gall-breakpoints=\'{"576":{"slidesPerView":2},"768":{"slidesPerView":3},"922":{"slidesPerView":2},"1200":{"slidesPerView":3}}\'>
		<div class="gall-d__list swiper-wrapper">';
			foreach ($result["rows"] as $row)
			{
				echo
				'<div class="slide-d swiper-slide">
					<article class="element-d element-d_postcard element-d_shop element-d_shop_item element-d_shop_item_order_rel js_shop">
						<div class="element-d__images">';
							//изображения товара
							if(! empty($row["img"]))
							{			
								$img = $row["img"][0];

								echo
								'<a class="_fit" href="'.BASE_PATH_HREF.$row["link"].'">';
								if($img["source"])
								{
									echo $img["source"];
								}
								else
								{
									echo '<img src="'.$img["src"].'" width="'.$img["width"].'" height="'.$img["height"].'" alt="'.$img["alt"].'" title="'.$img["title"].'">';
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
						'</div>
						<div class="element-d__details details-d">';

							//вывод названия и ссылки на товар
							echo 
							'<div class="detail-d detail-d_name">
								<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>
							</div>';

							//кнопка "Купить"
							echo $this->get('buy_form_order_rel', 'shop', array("row" => $row, "result" => $result));  

							echo
						'</div>
					</article>
				</div>';
			}
			echo
		'</div>
		<div class="gall-d__nav">
			<button class="gall-d__button gall-d__button_prev swiper-button-prev" title="'.$this->diafan->_('Предыдущий', false).'" type="button">
				<span class="icon-d fas fa-chevron-circle-left"></span>
			</button>
			<button class="gall-d__button gall-d__button_next swiper-button-next" title="'.$this->diafan->_('Следующий', false).'" type="button">
				<span class="icon-d fas fa-chevron-circle-right"></span>
			</button>
		</div>';
	// echo '<div class="gall-d__pagin swiper-pagination"></div>';
	echo '</div>';

	echo '</section>';
}