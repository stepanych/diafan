<?php
/**
 * Шаблон страницы фотографии
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

if(empty($result["ajax"]))
{
	echo '<section class="section-d section-d_id section-d_photo section-d_photo_id js_photo_id">';
}

//рейтинг фотографии
if(! empty($result["rating"]))
{
	echo $result["rating"];
}

//изображение и ссылка на следующее фото
if(! empty($result["img"]))
{
	echo '<div class="_images">';
	echo (! empty($result["next"])?'<a href="'.BASE_PATH_HREF.$result["next"]["link"].'" class="js_photo_link_ajax">':'');
	echo '<img src="'.$result["img"]["src"].'" width="'.$result["img"]["width"].'" height="'.$result["img"]["height"].'"	alt="'.$result["img"]["alt"].'" title="'.$result["img"]["title"].'">';
	echo (! empty($result["next"])?'</a>':'');
	echo '</div>';
}

//полное описание фотографии
if(! empty($result['text']))
{
	echo '<div class="_text">'.$result['text'].'</div>';
}

//счетчик просмотров
if(! empty($result["counter"]))
{
	echo
	'<div class="counter-d">
		<span class="counter-d__name">'.$this->diafan->_('Просмотров').':</span>
		<span class="counter-d__value">'.$result["counter"].'</span>
	</div>';
}

//теги фотографии
if (! empty($result["tags"]))
{
	echo $result["tags"];
}

//ссылки на предыдущую и последующую фотографии
echo $this->htmleditor('<insert name="show_previous_next" module="photo">');

//комментарии к фотографии
if(! empty($result["comments"]))
{
	echo $result["comments"];
}

if(empty($result["ajax"]))
{
	echo '</section>';
}

echo $this->htmleditor('<insert name="show_block_rel" module="photo" count="4">');
