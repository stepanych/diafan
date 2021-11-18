<?php
/**
 * Шаблон списка товаров для поиска
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

echo '<section class="block-d block-d_shop block-d_shop_item block-d_shop_item_search">';

//вывод списка товаров
if(! empty($result['rows']))
{
	$result['search'] = true;

	echo '<div class="block-d__list _viewgrid">';
	echo $this->get($result['view_rows'], 'shop', $result);
	echo '</div>';
}

echo '</section>';
