<?php
/**
 * Шаблонный тег: подключает файл-блок шаблона.
 *
 * @param array $attributes атрибуты шаблонного тега
 * file - имя PHP-файла из папки *themes/blocks* без расширения
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
$this->diafan->attributes($attributes, 'file');
$attributes["file"] = preg_replace('/[^a-z_\-0-9]+/', '', $attributes["file"]);

if(! Custom::exists('themes/blocks/'.$attributes["file"].'.php'))
{
	return;
}
$inc = file_get_contents(Custom::path('themes/blocks/'.$attributes["file"].'.php'));

echo $this->get_function_in_theme($inc, true);
