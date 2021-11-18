<?php
/**
 * @package    DIAFAN.CMS
 * Bootstrap
 * @author     diafan.ru
 * @version    7.0
 * @license    https://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (https://www.diafan.ru/)
 */
 
/**
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * ВНИМАНИЕ! Это файл ядра, не допускается его редактирование! 
 *  
 * Оформление сайта находится в директориях:
 * Базовый дизайн:
 * 		/themes/ (шаблоны страниц сайта)
 * 		/modules/имя_модуля/views/ (шаблоны оформления модулей)
 * Измененный дизайн:
 *		/custom/my/themes/
 * 		/custom/my/modules/имя_модуля/views/
 * 
 * Документация https://www.diafan.ru/dokument/full-manual/templates/
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */

define('DIAFAN', 1);
define('ABSOLUTE_PATH', dirname(__FILE__).'/');

if (empty($_GET["rewrite"]))
{
	$_GET["rewrite"] = '';
}
if(isset($_POST["_data_"]) && count($_POST) == 1)
{
	if(is_string($_POST["_data_"])) $variable = json_decode($_POST["_data_"], true);
	elseif(is_array($_POST["_data_"])) $variable = $_POST["_data_"];
	else $variable = null;
	if($variable && is_array($variable))
	{
		unset($_POST["_data_"]); unset($_REQUEST["_data_"]);
		$_POST = array_combine(array_keys($variable), array_values($variable));
		foreach($variable as $key => $value) $_REQUEST[$key] = $value;
	}
}

define('IS_HTTPS', (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' || ! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || isset($_SERVER['HTTP_X_HTTPS']) && $_SERVER['HTTP_X_HTTPS'] == '1'));

include ABSOLUTE_PATH.'config.php';

if (! defined('TIMEZONE') || !TIMEZONE || @!date_default_timezone_set(TIMEZONE))
{
	@date_default_timezone_set('Europe/Moscow');
}

include_once ABSOLUTE_PATH.'includes/custom.php';
Custom::init();

Custom::inc('includes/developer.php');

Dev::init();

try
{
	Custom::inc('includes/core.php');

	if (preg_match('/^'.ADMIN_FOLDER.'(\/|$)/', $_GET["rewrite"]))
	{
		include_once(Custom::path('adm/index.php'));
	}

	define('IS_ADMIN', 0);

	Custom::inc('includes/init.php');

	$diafan = new Init();

	if (file_exists(ABSOLUTE_PATH.'install.php'))
	{
		include ABSOLUTE_PATH.'install.php';
	}
	elseif($_GET["rewrite"] == 'installation')
	{
		header('Location: http'.(IS_HTTPS ? "s" : '').'://'.getenv("HTTP_HOST").str_replace('installation/', '', getenv("REQUEST_URI")), true, 301);
		exit;
	}

	define('BASE_PATH', "http".(IS_HTTPS ? "s" : '')."://".getenv("HTTP_HOST")."/".(REVATIVE_PATH ? REVATIVE_PATH.'/' : ''));

	$diafan->start();
}
catch (Exception $e)
{
	Dev::exception($e);
}

exit;
