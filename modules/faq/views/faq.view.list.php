<?php
/**
 * Шаблон списка вопросов и ответов
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



echo '<section class="section-d section-d_list section-d_faq section-d_faq_cat">';

//описание текущей категории
if(! empty($result["text"]))
{
	echo '<div class="_text">'.$result['text'].'</div>';
}

//рейтинг категории
if(! empty($result["rating"]))
{
	echo $result["rating"];
}

//подкатегории
if(! empty($result["children"]))
{
	foreach($result["children"] as $child)
	{
		echo '<section class="section-d section-d_child">';

		//название и ссылка подкатегории
		echo
		'<header class="section-d__name">
			<a href="'.BASE_PATH_HREF.$child["link"].'">'.$child["name"].'</a>
		</header>';

		//рейтинг подкатегории
		if(! empty($child["rating"]))
		{
			echo $child["rating"];
		}

		//краткое описание подкатегории
		if($child["anons"])
		{
			echo '<div class="_text">'.$child['anons'].'</div>';
		}

		//вопросы подкатегории
		if(! empty($child["rows"]))
		{
			$res = $result; unset($res["show_more"]);
			$res["rows"] = $child["rows"];

			echo '<div class="section-d__list _list">';
			echo $this->get($result["view_rows"], 'faq', $res);
			echo '</div>';
		}
		echo '</section>';
	}
}

//комментарии к категории
if(! empty($result["comments"]))
{
	echo $result["comments"];
}

//вопросы
if(! empty($result["rows"]))
{
	echo '<div class="section-d__list _list">';
	echo $this->get($result["view_rows"], 'faq', $result);
	echo '</div>';
}

//постраничная навигация
if(! empty($result["paginator"]))
{
	echo $result["paginator"];
}

//ссылки на предыдущую и последующую категории
echo $this->htmleditor('<insert name="show_previous_next" module="faq">');

echo '</section>';

//форма добавления вопроса
if (! empty($result["form"]))
{
	echo $this->get('form', 'faq', $result["form"]);
}
