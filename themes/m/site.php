<?php
/**
 * Шаблон сайта для мобильной версии
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

if(! defined("DIAFAN"))
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><insert name="show_title"></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="Russian" name="language">
<meta content="DiAfan <?php echo "http".(IS_HTTPS ? "s" : '')."://"; ?>www.diafan.ru/" name="author">
<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=0.7, user-scalable=yes" />
<link href="<insert name="path">css/default.css" rel="stylesheet" type="text/css">
<link href="<insert name="path">css/style.css" rel="stylesheet" type="text/css">
<link href="<insert name="path">css/new.css" rel="stylesheet" type="text/css">
<link href="<insert name="path">css/m/style.css" rel="stylesheet" type="text/css">
<insert name="show_head">
<insert name="show_css">
</head>
<body>

<div class="slide">
    <a class="signboard-d" href='<insert name="path_url">'><insert name="show_theme" module="site" tag="logo" template="logo">
				<div class="signboard-d__inscript inscript-d">
					<div class="inscript-d__monogram"><insert name="show_theme" module="site" tag="logo_name"></div>
					<div class="inscript-d__slogan"><insert name="show_theme" module="site" tag="logo_text"></div>
				</div>
			</a>
<div id="h"><h1><insert name="show_h1"></h1></div>

<div id="content">
    <h2>Скачайте наше мобильное приложение</h2>
    <a href="https://www.apple.com/iphone/appstore/" target="_blank"><i class="fa fa-apple"></i> App Store</a> 
    <a href="https://play.google.com/store?hl=ru" target="_blank"><i class="fa fa-android"></i> PLAY Market</a>
    <h2>Остаться на сайте</h2>
	<a href="<insert name="path_url" mobile="no">?mobile=no">Перейти к адаптивной версии сайта</a>
	
</div>

<div id="footer" class="footer_inside">
	&copy; <insert name="show_year"> <insert name="title">
</div>
</div>
<!-- шаблонный тег show_js подключает JS-файлы. Описан в файле themes/functions/show_js.php. -->
<insert name="show_js">
<script type="text/javascript" src="<insert name="custom" path="js/main.js" absolute="true" compress="js">" charset="UTF-8"></script>

<!-- шаблонный тег подключает вывод информации о Политике конфиденциальности. Если необходимо вывести свой текст сообщения, то добавле его в атрибут "text". -->
<insert name="show_privacy" hash="false" text="">

<insert name="show_include" file="counters">

</body>
</html>
