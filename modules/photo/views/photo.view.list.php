<?php
/**
 * Шаблон списка фотографий
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



echo '<section class="section-d section-d_list section-d_photo section-d_photo_cat">';

//вывод описания текущей категории
if(! empty($result["text"]))
{
	echo '<div class="_text">'.$result['text'].'</div>';
}

//рейтинг альбома
if(! empty($result["rating"]))
{
	echo $result["rating"];
}

//вывод изображений текущей категории
if(! empty($result["img"]))
{
	echo '<div class="_images">';
	foreach($result["img"] as $img)
	{
		switch($img["type"])
		{
			case 'animation':
				echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$result["id"].'photo">';
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

//вывод подкатегорий
if(! empty($result["children"]))
{
	foreach($result["children"] as $child)
	{
		echo '<section class="section-d section-d_child">';

		//вывод названий и ссылок на подкатегории
		echo
		'<header class="section-d__name">
			<a href="'.BASE_PATH_HREF.$child["link"].'">'.$child["name"].' ('.$child["count"].')</a>
		</header>';

		//рейтинг подкатегории
		if(! empty($child["rating"]))
		{
			echo $child["rating"];
		}

		//вывод изображений альбома
		if(! empty($child["img"]))
		{
			echo '<div class="_images">';
			foreach($child["img"] as $img)
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
		elseif(! empty($child["rows"]))
		{
			//фотографии подкатегории
			$res = $result; unset($res["show_more"]);
			$res["rows"] = $child["rows"];

			echo '<div class="section-d__list _viewgrid">';
			echo $this->get($result["view_rows"], 'photo', $res);
			echo '</div>';		
		}

		//краткое описание подкатегории
		if($child["anons"])
		{
			echo '<div class="_text">'.$child['anons'].'</div>';
		}
		echo '</section>';
	}
}

//вывод списка фотографий
if(! empty($result['rows']))
{	
	echo '<div class="section-d__list _viewgrid">';
	echo $this->get($result["view_rows"], 'photo', $result);
	echo '</div>';
}

//вывод комментариев к категориям, если подключены в настройках
if(! empty($result["comments"]))
{
	echo $result["comments"];
}

//вывод постраничной навигации
if(! empty($result["paginator"]))
{
	echo $result["paginator"];
}

//ссылки на предыдущую и последующую категории
echo $this->htmleditor('<insert name="show_previous_next" module="photo">');

echo '</section>';