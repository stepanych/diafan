<?php
/**
 * Шаблон элементов в списке новостей
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

//вывод списка новостей
foreach ($result["rows"] as $row)
{		           
	echo '<article class="element-d element-d_row element-d_news element-d_news_item _bounded">';

	//вывод изображений новости
	if (! empty($row["img"]))
	{
		echo '<div class="element-d__images">';
		foreach ($row["img"] as $img)
		{
			switch($img["type"])
			{
				case 'animation':
					echo '<a class="_fit" href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$row["id"].'news">';
					break;
				case 'large_image':
					echo '<a class="_fit" href="'.BASE_PATH.$img["link"].'" rel="large_image" width="'.$img["link_width"].'" height="'.$img["link_height"].'">';
					break;
				default:
					echo '<a class="_fit" href="'.BASE_PATH_HREF.$img["link"].'">';
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
	
	//вывод названия и ссылки на новость
	echo 
	'<div class="detail-d detail-d_name">
		<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>
	</div>';

	//вывод рейтинга новости за названием, если рейтинг подключен
	if (! empty($row["rating"]))
	{
		echo '<div class="detail-d detail-d_rating">'.$row["rating"].'</div>';
	}

	//вывод анонса новостей
	if(! empty($row["anons"]))
	{
		echo '<div class="detail-d detail-d_anons _text">'.$row['anons'].'</div>';
	}

	//вывод даты новости
	if (! empty($row['date']))
	{
		echo
		'<div class="detail-d detail-d_date">
			<span class="date-d">'.$row["date"].'</span>
		</div>';
	}

	//вывод прикрепленных тегов к новости
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
