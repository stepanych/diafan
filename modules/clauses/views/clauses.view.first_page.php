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



if (empty($result["categories"]))
	return false;

echo '<section class="section-d section-d_home section-d_clauses section-d_clauses_home">';

//категории
foreach ($result["categories"] as $cat_id => $cat)
{
	echo '<section class="section-d section-d_main">';

	//название категории
	echo
	'<header class="section-d__name">
		<a href="'.BASE_PATH_HREF.$cat["link_all"].'">'.$cat["name"].'</a>
	</header>';

	//рейтинг категории
	if (! empty($cat["rating"]))
	{
		echo $cat["rating"];
	}

	//изображения категории
	if (! empty($cat["img"]))
	{
		echo '<div class="_images">';
		foreach ($cat["img"] as $img)
		{
			switch($img["type"])
			{
				case 'animation':
					echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$cat_id.'clauses">';
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

	//краткое описание категории
	if (! empty($cat["anons"]))
	{
		echo '<div class="_text">'.$cat['anons'].'</div>';
	}

	//подкатегории
	if (! empty($cat["children"]))
	{
		foreach ($cat["children"] as $child)
		{
			echo '<section class="section-d section-d_child">';

			//название и ссылка подкатегории
			echo
			'<header class="section-d__name">
				<a href="'.BASE_PATH_HREF.$child["link"].'">'.$child["name"].' ('.$child["count"].')</a>
			</header>';

			//изображения подкатегории
			if(! empty($child["img"]))
			{
				echo '<div class="_images">';
				foreach($child["img"] as $img)
				{
					switch($img["type"])
					{
						case 'animation':
							echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$child["id"].'clauses">';
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

			//рейтинг подкатегории
			if(! empty($child["rating"]))
			{
				echo $child["rating"];
			}

			//краткое описание подкатегории
			if (! empty($child["anons"]))
			{
				echo '<div class="_text">'.$child['anons'].'</div>';
			}

			//статьи подкатегории
			if(! empty($child["rows"]))
			{
				$res = $result; unset($res["show_more"]);
				$res["rows"] = $child["rows"];

				echo '<div class="section-d__list _list">';
				echo $this->get('rows', 'clauses', $res);
				echo '</div>';
			}

			echo '</section>';
		}
	}

	//статьи в категории
	if(! empty($cat["rows"]))
	{
		$res = $result; unset($res["show_more"]);
		$res["rows"] = $cat["rows"];

		echo '<div class="section-d__list _list">';
		echo $this->get('rows', 'clauses', $res);
		echo '</div>';
	}

	//ссылка на все статьи в категории
	if ($cat["link_all"])
	{
		echo '<div class="show_all"><a href="'.BASE_PATH_HREF.$cat["link_all"].'">'
		.$this->diafan->_('Посмотреть все статьи в категории «%s»', true, $cat["name"])
		.'</a></div>';
	}

	echo '</section>';
}

//Кнопка "Показать ещё"
if (! empty($result["show_more"]))
{
	echo $result["show_more"];
}

//постраничная навигация
if(! empty($result["paginator"]))
{
	echo $result["paginator"];
}

echo '</section>';
