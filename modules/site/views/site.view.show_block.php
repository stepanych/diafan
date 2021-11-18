<?php
/**
 * Шаблон блока на сайте
 * 
 * Шаблонный тег <insert name="show_block" module="site" id="номер_страницы" [template="шаблон"]>:
 * выводит блок на сайте
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



if(! empty($result["name"]))
{
	echo '<header class="block-d__name">'.$result["name"].'</header>';
}

echo $result['text'];
