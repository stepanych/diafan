<?php
/**
 * Шаблон вложенных страниц сайта
 *
 * Шаблонный тег <insert name="show_links" module="site" [template="шаблон"]>:
 * выводит ссылки на страницы нижнего уровня, принадлежащие текущей странице
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

if (! $result)
{
	return;
}
echo '<ul class="show_links">';
foreach ($result as $row)
{
	echo '<li><a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a></li>';
}
echo '</ul>';
