<?php
/**
 * Шаблон ссылок на предыдущую и следующую страницы сайта
 *
 * Шаблонный тег <insert name="show_previous_next" module="faq" [template="шаблон"]>:
 * выводит ссылки на предыдующую и следующую страницы
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2020 OOO «Диафан» (http://www.diafan.ru/)
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



if (empty($result["previous"]) && empty($result["next"]))
{
	return;
}

echo '<div class="prevnext-d">';
if(! empty($result["previous"]))
{
	echo '<a class="prevnext-d__prev" href="'.BASE_PATH_HREF.$result["previous"]["link"].'"><span class="prevnext-d__icon icon-d fas fa-arrow-left"></span>'.$result["previous"]["text"].'</a>';
}
if(! empty($result["next"]))
{
	echo '<a class="prevnext-d__next" href="'.BASE_PATH_HREF.$result["next"]["link"].'">'.$result["next"]["text"].'<span class="prevnext-d__icon icon-d fas fa-arrow-right"></span></a>';
}
echo '</div>';
