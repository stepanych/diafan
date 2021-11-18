<?php
/**
 * Шаблон облака тегов
 *
 * Шаблонный тег <insert name="show_block" module="tags" [template="шаблон"]>:
 * облако тегов
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



if(empty($result["rows"])) return false;

foreach ($result["rows"] as $row)
{
	echo '<article class="element-d element-d_tags element-d_tags_item _bounded">';
	if (! empty($row["img"]))
	{
		echo '<div class="element-d__images _images">';
		foreach ($row["img"] as $img)
		{
			switch($img["type"])
			{
				case 'animation':
					echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$row["id"].'tags">';
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
			echo '</a>';
		}
		echo '</div>';
	}
	echo '<div class="element-d__details details-d">';
	if (! $row["selected"])
	{
		echo '<a class="detail-d detail-d_name" href="'.BASE_PATH_HREF.$row["link"].'" style="font-size: '.$row["size"].'em;">'.$row["name"].'</a>';
	}
	else
	{
		echo '<span class="detail-d detail-d_name" style="font-size: '.$row["size"].'em;">'.$row["name"].'</span>';
	}
	echo '</div>';
	echo '</article>';
}