<?php
/**
 * Шаблон блока похожих статей
 * 
 * Шаблонный тег <insert name="show_block_rel" module="clauses" [count="количество"]
 * [images="количество_изображений"] [images_variation="тег_размера_изображений"]
 * [template="шаблон"]>:
 * блок похожих статей
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


//статьи
foreach ($result["rows"] as $row)
{
	echo '<article class="element-d element-d_row element-d_clauses element-d_clauses_item _bounded">';

	//изображения статьи
	if (! empty($row["img"]))
	{
		echo '<div class="element-d__images">';
		foreach ($row["img"] as $img)
		{
			switch($img["type"])
			{
				case 'animation':
					echo '<a class="_fit" href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$row["id"].'clauses">';
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

	//название и ссылка статьи
	echo
	'<div class="detail-d detail-d_name">
		<a href="'.BASE_PATH_HREF.$row['link'].'">'.$row['name'].'</a>
	</div>';

	//рейтинг статьи
	if (! empty($row["rating"]))
	{
		echo '<div class="detail-d detail-d_rating">'.$row['rating'].'</div>';
	}

	//анонс статьи
	if (! empty($row['anons']))
	{
		echo
		'<div class="detail-d detail-d_anons _text">
			<a href="'.BASE_PATH_HREF.$row['link'].'">'.$row['anons'].'</a>
		</div>';
	}

	//теги статьи
	if(! empty($row["tags"]))
	{
		echo '<div class="detail-d detail-d_tags">'.$row["tags"].'</div>';
	}

	//дата статьи
	if (! empty($row['date']))
	{
		echo
		'<div class="detail-d detail-d_date">
			<span class="date-d">'.$row['date'].'</span>
		</div>';
	}

	echo '</div>';

	echo '</article>';
}