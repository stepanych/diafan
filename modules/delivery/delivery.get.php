<?php
/**
 * Прямое обращение к файлам бэкенда доставки
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
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

$rewrite_array = explode('/', $_GET["rewrite"]);
if(Custom::exists('modules/delivery/backend/'.$rewrite_array[0].'/delivery.'.$rewrite_array[0].'.php'))
{
	include_once(Custom::path('modules/delivery/backend/'.$rewrite_array[0].'/delivery.'.$rewrite_array[0].'.php'));
	exit;
}
else
{
	Custom::inc('includes/404.php');
}
