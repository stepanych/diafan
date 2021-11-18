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
switch($result["type"])
{
    case "image":
        echo BASE_PATH.USERFILES.'/site/theme/'.$result['value'];
        break;

    default:
        if($result['useradmin'])
        {
            echo $this->diafan->_useradmin->get($result['value'], 'value', $result["id"], 'site_theme', $result["lang_id"], ($result['type'] == 'editor' ? 'editor' : 'text'));
        }
        else
        {
            echo $result['value'];
        }
        break;
}
