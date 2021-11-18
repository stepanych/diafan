<?php
/**
 * Шаблон изображений к странице сайта
 *
 * Шаблонный тег <insert name="show_images" module="site" [template="шаблон"]>:
 * выводит изображения, прикрепленные к старинце сайта
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



if (empty($result["images"]))
{
	return;
}

echo '<div class="_images">';
foreach ($result["images"] as $img)
{
	switch($img["type"])
	{
		case 'animation':
			echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$result["id"].'site">';
			break;
		case 'big_image':
			echo '<a href="'.BASE_PATH.$img["link"].'" rel="big_image" width="'.$img["link_width"].'" height="'.$img["link_height"].'">';
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
