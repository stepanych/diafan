<?php
/**
 * Шаблон списка фотографий для модуля «Теги»
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



echo '<section class="section-d section-d_list section-d_photo section-d_photo_tags">';
//фотографии
if(! empty($result["rows"]))
{
	echo '<div class="section-d__list _viewgrid">';
	echo $this->get('rows_tags', 'photo', $result);
	echo '</div>';
}
echo '</section>';
