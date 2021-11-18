<?php
/**
 * Шаблон блока производителей
 *
 * Шаблонный тег <insert name="show_brand" module="shop" [count="количество"]
 * [cat_id="категория"] [site_id="страница_с_прикрепленным_модулем"]
 * [images="количество_изображений"] [images_variation="тег_размера_изображений"]
 * [only_module="true|false"]
 * [template="шаблон"]>:
 * блок производителей
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
	return;
}

echo '<section class="block-d block-d_shop block-d_shop_brand">';

//заголовок блока
echo '<header class="block-d__name">'.$this->diafan->_('Производители').'</header>';

echo '<div class="block-d__list _list">';

//вывод производителей
foreach ($result["rows"] as $row)
{
	echo '<article class="element-d element-d_shop element-d_shop_brand">';

	echo '<div class="element-d__impress _images">';
	//изображения производителя
	if(! empty($row["img"]))
	{
		foreach ($row["img"] as $img)
		{
			switch ($img["type"])
			{
				case 'animation':
					echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$row["id"].'shop_brand">';
					break;

				case 'large_image':
					echo '<a href="'.BASE_PATH.$img["link"].'" rel="large_image" width="'.$img["link_width"].'" height="'.$img["link_height"].'">';
					break;

				default:
					echo '<a href="'.BASE_PATH_HREF.$img["link"].'">';
					break;
			}
			if($img["source"])
			{
				echo $img["source"];
			}
			else
			{
				echo '<img src="'.$img["src"].'" width="'.$img["width"].'" height="'.$img["height"].'" alt="'.$img["alt"].'" title="'.$img["title"].'">';	
			}	
			echo '</a> ';
		}
	}
	echo '</div>';
	echo '<div class="element-d__details details-d">';

	//название и ссылка производителя
	echo 
	'<div class="detail-d detail-d_name">
		<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>
	</div>';

	//описание производителя
	//if (! empty($row["text"]))
	//{
	//	echo '<div class="detail-d detail-d_text _text">'.$row['text'].'</div>';
	//}

	echo '</div>';
	echo '</article>';
}
echo '</div>';

echo '<section>';
