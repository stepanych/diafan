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
	echo '<section class="block-d block-d_shop block-d_shop_order_rel block-d_shop_item block-d_shop_item_order_rel">';

	echo '<header class="block-d__name">'.$this->diafan->_('C этим товаром покупают').'</header>';

	echo '<div class="block-d__list _viewgrid">';
	foreach ($result["rows"] as $row)
	{
		echo '<article class="element-d element-d_postcard element-d_shop element-d_shop_item element-d_shop_item_order_rel js_shop">';

		echo '<div class="element-d__images">';
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
		echo '</div>';

		echo '<div class="element-d__details details-d">';

		//вывод названия и ссылки на товар
		echo 
		'<div class="detail-d detail-d_name">
			<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>
		</div>';

		//кнопка "Купить"
		echo $this->get('buy_form_order_rel', 'shop', array("row" => $row, "result" => $result));  

		echo '</div>';

		echo '</article>';
	}
	echo '</div>';

	echo '</section>';
}
