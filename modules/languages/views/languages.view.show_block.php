<?php
/**
 * Шаблон ссылок на языковые версии сайта
 *
 * Шаблонный тег <insert name="show_block" module="languages" id="номер_страницы" [template="шаблон"]>:
 * выводит блок ссылок на альтернативные языковые версии сайта
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



echo
'<nav class="langs-d">';
	foreach ($result as $row)
	{
		if($row["current"])
		{
			echo '<a class="lang-d lang-d_current">'.$row["name"].'</a>';		
		}
		else
		{
			echo '<a class="lang-d" href="'.$row["link"].'">'.$row["name"].'</a>';
		}
	}
	echo
'</nav>';
