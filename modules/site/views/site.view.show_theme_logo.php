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

//кастомизированный шаблон для демо-сайта: если загружен логотип пользователя, выводим его, иначе выводим SVG-логотип DIAFAN
if(! empty($result['value']))
{
	$useradmin_link = $this->diafan->_useradmin->get_image(USERFILES.'/site/theme/'.$result['value']);
    
	echo '<div class="signboard-d__logo logo-d">';
	echo '<img src="'.BASE_PATH.USERFILES.'/site/theme/'.$result['value']
    .(! empty($_GET["ua"]) ? '?ua='.$this->diafan->filter($_GET["useradmin"], "integer") : '')
    .'"'
    .($useradmin_link ? ' data-useradmin="'.$useradmin_link.'"' : '').' class="useradmin_contener"'
    .'>';
	echo '</div>';
}
else
{
	echo '<div class="signboard-d__logo logo-d logo-d_diafan"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44.1 44.1"><path fill="#c53c12" d="M44.1,44.1H0V0H44.1ZM2.43,41.67H41.68V2.43H2.43Z"></path><path fill="#c53c12" d="M34.3,9.58H30.86l-8.41,23.9h5.44l1.73-5.67H34.3V23.58H30.89c1.69-5.48,3.06-9,3.41-10.41C34.35,13.38,34.3,9.58,34.3,9.58Z"></path><path fill="#c53c12" d="M10.61,33.48h2.22q6.32,0,9.66-3.13t3.35-9q0-5.52-3.23-8.59t-9-3.08h-3c.06,1,0,4.13,0,4.13h3.22q6.76,0,6.77,7.67,0,7.83-7.3,7.83H10.56"></path></svg></div>';
}
