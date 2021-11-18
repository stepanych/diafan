<?php
/**
 * Шаблон блока похожих товаров
 *
 * Шаблонный тег <insert name="show_block_rel" module="shop" [count="количество"]
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

echo '<header class="block-d__name">'.$this->diafan->_('Похожие товары').'</header>';

echo
'<div class="gall-d gall-d_navbottom swiper-container" data-gall-show="1" data-gall-gap="30" data-gall-breakpoints=\'{"576":{"slidesPerView":2},"768":{"slidesPerView":3},"922":{"slidesPerView":2},"1200":{"slidesPerView":3}}\'>';

	echo '<div class="gall-d__list swiper-wrapper">';
	echo $this->get('rows_gall', 'shop', $result);
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
	// echo '<div class="gall-d__pagin swiper-pagination"></div>';
	echo
'</div>';

echo '</section>';
