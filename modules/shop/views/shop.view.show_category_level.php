<?php
/**
 * Шаблон вложенных уровней блока категорий
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



if (empty($result["rows"][$result["parent_id"]]))
{
	return false;
}

//вывод категорий
foreach ($result["rows"][$result["parent_id"]] as $row)
{
	echo '<article class="element-d element-d_child element-d_child_'.$result["level"].' element-d_shop element-d_shop_cat element-d_shop_cat_child">';

	//изображения категорий
	if (! empty($row["img"]))
	{
		echo '<div class="element-d__images _images">';
		foreach ($row["img"] as $img)
		{
			switch ($img["type"])
			{
				case 'animation':
					echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$row["id"].'shop_category">';
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
		echo '</div>';
	}
	echo '<div class="element-d__details details-d">';
	//название и ссылка категории
	echo 
	'<div class="detail-d detail-d_name">
		<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"];
			if(isset($row["number_elements"]))
			{
				echo ' ('.$row["number_elements"].')';
			}
			echo
		'</a>
	</div>';
	//описание категории
	//if(! empty($row["anons"]))
	//{
	//	echo '<div class="detail-d detail-d_anons _text">'.$row['anons'].'</div>';
	//}
	echo '</div>';
	echo '<div class="element-d__list element-d__list_children _list">';
	if(! empty($result["rows"][$row["id"]]))
	{
		$res = $result;
		$res["level"] = $result["level"] + 1;
		$res["parent_id"] = $row["id"];

		echo $this->get('show_category_level', 'shop', $res);
	}
	echo '</div>';
	echo '</article>';
}
