<?php
/**
 * Шаблон страницы статьи
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



echo '<section class="section-d section-d_id section-d_clauses section-d_clauses_id">';

//рейтинг статьи
if(! empty($result["rating"]))
{
	echo $result["rating"];
}

//дата статьи
if (! empty($result["date"]))
{
	echo '<div class="date-d">'.$result["date"].'</div>';
}

//описание статьи
echo '<div class="_text">'.$result['text'].'</div>';

//счетчик просмотров
if(! empty($result["counter"]))
{
	echo
	'<div class="counter-d">
		<span class="counter-d__name">'.$this->diafan->_('Просмотров').':</span>
		<span class="counter-d__value">'.$result["counter"].'</span>
	</div>';
}

//теги статьи
if (! empty($result["tags"]))
{
	echo $result["tags"];
}

//изображения статьи
if(! empty($result["img"]))
{
	echo '<div class="_images">';
	foreach($result["img"] as $img)
	{
		switch($img["type"])
		{
			case 'animation':
				echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$result["id"].'clauses">';
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

//ссылки на предыдущую и последующую статью
echo $this->htmleditor('<insert name="show_previous_next" module="clauses">');

//ссылки на все статьи
if (! empty($result["allclauses"]))
{
	echo '<div class="show_all"><a href="'.BASE_PATH_HREF.$result["allclauses"]["link"].'">'.$this->diafan->_('Вернуться к списку').'</a></div>';
}

//комментарии к статье
if(! empty($result["comments"]))
{
	echo $result["comments"];
}

echo '</section>';

echo $this->htmleditor('<insert name="show_block_rel" module="clauses" count="4" images="1">');
