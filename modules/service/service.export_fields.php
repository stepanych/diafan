<?php
/**
 * Экспорт описаний
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

/**
 * Service_export_fields
 */

if(! $this->diafan->_users->roles("init", "service/express", array(), 'admin'))
{
	Custom::inc('includes/404.php');
}

$data = array(
	"fields_category" => DB::query_fetch_all("SELECT * FROM {%s_fields_category} WHERE trash='0'", "service_express"),
	"fields" => DB::query_fetch_all("SELECT * FROM {%s_fields} WHERE trash='0'", "service_express")
);
Custom::inc('includes/json.php');
$json = json_encode($data);

$is_zip = false;
if(class_exists('ZipArchive'))
{
	$name = ABSOLUTE_PATH.'tmp/'.md5(mt_rand(0, 9999)).'.zip';
	$zip = new ZipArchive;
	if ($zip->open($name, ZipArchive::CREATE) === true)
	{
		$zip->addFromString("/".DB_PREFIX."import_fields".".json", $json);
		$zip->close();
		$json = file_get_contents($name);
		unlink($name);
		$is_zip = true;
	}
}

$this->diafan->_site->nozip = true;
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: max-age=86400');
if($is_zip)
{
	header("Content-type: application/zip");
	header("Content-Disposition: attachment; filename=".DB_PREFIX."import_fields.json.zip");
}
else
{
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=".DB_PREFIX."import_fields.json");
}
header('Content-transfer-encoding: binary');
header("Connection: close");

echo $json;
exit;
