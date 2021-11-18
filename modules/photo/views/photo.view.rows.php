<?php
/**
 * Шаблон элементов в списке фотографий
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2014 OOO «Диафан» (http://diafan.ru)
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



//вывод списка фотографий
if(empty($result["rows"])) return false;

foreach ($result["rows"] as $row)
{
	echo '<article class="element-d element-d_postcard element-d_photo element-d_photo_item">';

	//вывод маленького изображения
	if (! empty($row["img"]))
	{
		echo '<div class="element-d__images">';	
		switch($row["img"]["type"])
		{
			case 'animation':
				echo '<a href="'.BASE_PATH.$row["img"]["link"].'" data-fancybox="galleryphoto">';
				break;
			case 'large_image':
				echo '<a href="'.BASE_PATH.$row["img"]["link"].'" rel="large_image" width="'.$row["img"]["link_width"].'" height="'.$row["img"]["link_height"].'">';
				break;
			default:
				echo '<a href="'.BASE_PATH_HREF.$row["img"]["link"].'">';
				break;
		}
		echo '<img src="'.$row["img"]["src"].'" alt="'.$row["img"]["alt"].'" title="'.$row["img"]["title"].'">'
		.'</a>
		</div>';
	}

	echo '<div class="element-d__details details-d">';

	//вывод названия и, если используется, ссылки на отдельную страницу фотографии
	if ($row["name"])
	{		
		echo '<div class="detail-d detail-d_name">';
		if($row["link"])
		{
			echo '<a href="'.BASE_PATH_HREF.$row["link"].'">';
		}
		echo $row["name"];
		if ($row["link"])
		{
			echo '</a>';
		}
		echo '</div>';
	}

	//вывод краткого описания фотографии
	if(! empty($row["anons"]))
	{
		echo '<div class="detail-d detail-d_anons _text">'.$row['anons'].'</div>';
	}

	//вывод рейтинга фотографии
	if (! empty($row["rating"]))
	{
		echo '<div class="detail-d detail-d_rating">'.$row['rating'].'</div>';
	}

	//теги фотографии
	if(! empty($row["tags"]))
	{
		echo '<div class="detail-d detail-d_tags">'.$row["tags"].'</div>';
	}

	echo '</div>';

	echo '</article>';
}

//Кнопка "Показать ещё"
if(! empty($result["show_more"]))
{
	echo $result["show_more"];
}
