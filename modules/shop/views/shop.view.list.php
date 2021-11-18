<?php
/**
 * Шаблон списка товаров
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



if(! empty($result["error"]))
{
	echo '<p class="_note">'.$result["error"].'</p>';
	return;
}

if(empty($result["ajax"]))
{
	echo '<section class="section-d section-d_list section-d_shop section-d_shop_cat shop_list js_shop_list">';
}

echo '<section class="section-d section-d_main">';

//вывод изображений текущей категории
if(! empty($result["img"]))
{
	echo '<div class="_images">';
	foreach($result["img"] as $img)
	{
		switch ($img["type"])
		{
			case 'animation':
				echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$result["id"].'shop_cat">';
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

if(! empty($result['text']))
{
	echo '<div class="_text">'.$result['text'].'</div>';
}

//вывод подкатегории
if(! empty($result["children"]))
{
	foreach($result["children"] as $child)
	{
		echo '<section class="section-d section-d_child">';

		//название и ссылка подкатегории
		echo
		'<header class="section-d__name">
			<a href="'.BASE_PATH_HREF.$child["link"].'">'.$child["name"].' ('.$child["count"].')</a>
		</header>';

		//вывод изображений подкатегории
		if(! empty($child["img"]))
		{
			echo '<div class="_images">';
			foreach($child["img"] as $img)
			{
				switch ($img["type"])
				{
					case 'animation':
						echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$child["id"].'shop">';
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
					echo '<img src="'.$img["src"].'" width="'.$img["width"].'" height="'.$img["height"].'" alt="'.$img["alt"].'" title="'.$img["title"].'">'
						. '</a>';
				}
			}
			echo '</div>';
		}

		//краткое описание подкатегории
		if($child["anons"])
		{
			echo '<div class="_text">'.$child['anons'].'</div>';
		}

		//вывод списка товаров подкатегории
		if(! empty($child["rows"]))
		{
			$res = $result; unset($res["show_more"]);
			$res["rows"] = $child["rows"];

			$view = '_viewgrid';
			if(! empty($_COOKIE['_diafan_shop_view']))
			{
				switch($_COOKIE['_diafan_shop_view'])
				{
					case 'rows':
						$view = '_viewrows';
					break;
				}
			}

			echo '<div class="section-d__list '.$view.'">';
			echo $this->get($result["view_rows"], 'shop', $res);
			echo '</div>';
		}

		echo '</section>';
	}
}

//вывод списка товаров
if(! empty($result["rows"]))
{
	//вывод сортировки товаров
	if(! empty($result["link_sort"]))
	{
		echo $this->get('sort_block', 'shop', $result);
	}

	$view = '_viewgrid';
	if(! empty($_COOKIE['_diafan_shop_view']))
	{
		switch($_COOKIE['_diafan_shop_view'])
		{
			case 'rows':
				$view = '_viewrows';
			break;
		}
	}

	echo '<div class="section-d__list '.$view.'">';
	echo $this->get($result["view_rows"], 'shop', $result);
	echo '</div>';
}

//постраничная навигация
if(! empty($result["paginator"]))
{
	echo $result["paginator"];
}

//вывод ссылок на предыдущую и последующую категории
echo $this->htmleditor('<insert name="show_previous_next" module="shop">');

//вывод комментариев ко всей категории товаров (комментарии к конкретному товару в функции id())
if(! empty($result["comments"]))
{
	echo $result["comments"];
}

echo '</section>';

if(empty($result["ajax"]))
{
	echo '</section>';
}
