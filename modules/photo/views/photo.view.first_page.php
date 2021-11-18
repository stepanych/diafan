<?php
/**
 * Шаблон первой страницы модуля, если в настройках модуля подключен параметр «Использовать категории»
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




if (empty($result["categories"])) return false;

if(empty($result["ajax"]))
{
	echo '<section class="section-d section-d_home section-d_photo section-d_photo_home">';
}

//вывод альбомов
foreach ($result["categories"] as $cat_id => $cat)
{
	echo '<section class="section-d section-d_main">';

	//название альбома
	echo
	'<header class="section-d__name">
		<a href="'.BASE_PATH_HREF.$cat["link_all"].'">'.$cat["name"].' ('.$cat["count"].')</a>
	</header>';

	//рейтинг альбома
	if (! empty($cat["rating"]))
	{
		echo $cat["rating"];
	}

	//вывод изображений альбома
	if (! empty($cat["img"]))
	{
		echo '<div class="_images">';
		foreach ($cat["img"] as $img)
		{
			switch($img["type"])
			{
				case 'animation':
					echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$cat_id.'photo">';
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
	else
	{
		//вывод нескольких фотографий из текущей категории (задается в настройках модуля)
		if(! empty($cat["rows"]))
		{
			$res = $result; unset($res["show_more"]);
			$res["rows"] = $cat["rows"];

			echo '<div class="section-d__list _viewgrid">';
			echo $this->get('rows', 'photo', $res);
			echo '</div>';
		}
	}

	//краткое описание альбома
	if (! empty($cat["anons"]))
	{
		echo '<div class="_text">'.$cat['anons'].'</div>';
	}

	echo '</section>';
}

//Кнопка "Показать ещё"
if(! empty($result["show_more"]))
{
	echo $result["show_more"];
}

if(empty($result["ajax"]))
{
	echo '</section>';
}

//постраничная навигация
if(! empty($result["paginator"]))
{
	echo $result["paginator"];
}
