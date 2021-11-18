<?php
/**
 * Экспорт телефонов и электронных ящиков
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

if(! $this->diafan->_users->roles("init", "subscription", array(), 'admin'))
{
	Custom::inc('includes/404.php');
}
if($_GET["rewrite"] == 'emails')
{
	$name = 'mail';
	$table = 'emails';
}
else
{
	$name = 'phone';
	$table = 'phones';
}
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: max-age=86400');
header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=".$table.".txt");
header('Content-transfer-encoding: binary');
header("Connection: close");

$rows = DB::query_fetch_all("SELECT * FROM {subscription_".$table."} WHERE trash='0' ORDER BY id ASC");
foreach ($rows as $row)
{
	echo str_replace("\n", "", $row["name"]);
	echo ";";
	echo str_replace("\n", "", $row[$name]);
	echo "\n";
}
exit;