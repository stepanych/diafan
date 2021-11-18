<?php
/**
 * Шаблон список новостей
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



if(! empty($result['error']))
{
	echo '<p class="_note">'.$result['error'].'</p>';
	return;
}

echo '<section class="block-d block-d_news block-d_news_item block-d_news_item_search">';

//вывод списка новостей
if(! empty($result['rows']))
{
	$result['search'] = true;

	echo '<div class="block-d__list _list">';
	echo $this->get($result['view_rows'], 'news', $result);
	echo '</div>';
}

echo '</section>';
