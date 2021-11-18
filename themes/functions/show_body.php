<?php
/**
 * Шаблонный тег: выводит основной контент страницы: заголовка (если не запрещен его вывод в настройке странице «Не показывать заголовок»), текста страницы и прикрепленного модуля. Заменяет три тега: show_h1, show_text, show_module.
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



ob_start();
$this->functions('show_h1');
$name = ob_get_contents();
ob_end_clean();
if ($name)
{
	echo '<h1>'.$name.'</h1>';
}

ob_start();
$this->functions('show_text');
$text = ob_get_contents();
ob_end_clean();
if ($text)
{
	echo '<section class="_text">'.$text.'</section>';
}

$this->functions('show_module');
