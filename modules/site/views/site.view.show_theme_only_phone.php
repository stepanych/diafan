<?php
/**
 * Шаблон вывода настроек шаблона
 * 
 * Шаблонный тег <insert name="show_theme" module="site" name="название_настройки" [template="шаблон"] [useradmin="true|false"]>:
 * выводит настройку в шаблоне сайта
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

if(empty($result['value']))
{
    return;
}
$result['value'] = preg_replace('/[^0-9]+/', '', $result['value']);
echo '+';
if(! in_array(substr($result['value'], 0, 1), array(7, 8)))
{
    echo '7';
}
echo $result['value'];
