<?php
/**
 * Шаблон страницы новости
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



echo '<section class="section-d section-d_id section-d_news section-d_news_id">';

//вывод даты новости
if (! empty($result["date"]))
{
	echo '<div class="date-d">'.$result["date"]."</div>";
}		

//изображения новости
if(! empty($result["img"]))
{
	echo '<div class="_images">';
	foreach($result["img"] as $img)
	{
		switch($img["type"])
		{
			case 'animation':
				echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$result["id"].'news">';
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

echo $this->htmleditor('<insert name="show_dynamic" module="site" id="1">');

//вывод основного текста новости
echo '<div class="_text">'.$this->htmleditor($result['text']).'</div>';

//счетчик просмотров
if(! empty($result["counter"]))
{
	echo
	'<div class="counter-d">
		<span class="counter-d__name">'.$this->diafan->_('Просмотров').':</span>
		<span class="counter-d__value">'.$result["counter"].'</span>
	</div>';
}

//вывод тегов к новости
if (! empty($result["tags"]))
{
	echo $result["tags"];
}

//рейтинг новости
if(! empty($result["rating"]))
{
	echo $result["rating"];
}

//комментарии к новости
if(! empty($result["comments"]))
{
	echo $result["comments"];
}

echo $this->htmleditor('<insert name="show_block_rel" module="news" count="3" images="1">');

//ссылки на предыдущую и последующую новость
echo $this->htmleditor('<insert name="show_previous_next" module="news">');

//ссылки на все новости
if (! empty($result["allnews"]))
{
	echo '<div class="show_all"><a href="'.BASE_PATH_HREF.$result["allnews"]["link"].'">'.$this->diafan->_('Вернуться к списку').'</a></div>';
}

echo '</section>';