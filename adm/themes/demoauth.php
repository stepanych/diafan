<?php
/**
 * Шаблон формы авторизации для демо-сайта
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
include_once(ABSOLUTE_PATH.'adm/brand.php');
?>

<html>

<head>
<meta charset="UTF-8">
<title>Демо-версия DIAFAN.CMS</title>
<meta name="HandheldFriendly" content="True">
<meta name="viewport" content="width=device-width, initial-scale=-0.2, minimum-scale=-0.2, maximum-scale=3.0">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="pragma" content="no-cache">

<link href="<?php echo BASE_PATH;?>adm/css/main.css" rel="stylesheet" type="text/css"></head>

<!--[if lte IE 8]>
	<link rel="stylesheet" href="<?php echo BASE_PATH;?>adm/css/ie/ie.css" media="all" />
<![endif]-->


<body>

<body class="login-page">
	<div id="wrapper">
		<!-- |===============| header start |===============| -->
		<header class="header">
			<a href="<?php echo "http".(IS_HTTPS ? "s" : '')."://".BASE_URL; ?>/" class="logo">
				<img src="<?php echo BASE_PATH; ?>adm/img/logo.png" alt="">
				<span class="logo__title">Система управления</span>
				<span class="logo__link"><?php echo BASE_URL?></span>
			</a>
			
		</header>
		<!-- |===============| header end |===============| -->
		
		<!-- |===============| wrap start |===============| -->
		<div class="wrap">
			<div>
				<div class="box_half box_height">
					<form class="login-form">
						<p><img src="https://cloud.diafan.ru/img/whitelogo.jpg"></p><p>Создание тематического сайта с шаблоном дизайна на тестовый период в 21 день.<br>
						Для сайта будет выделен поддомен и Вы сможете полноценно работать с ним в течение всего срока и затем использовать на реальном проекте.						</p>
						<p><b>Внимание!</b>		Обязательна регистрация по email или телефону.</p>
						<a href="https://cloud.diafan.ru/templates/" class="btn btn_blue btn_small" target="_blank"><i class="fa fa-external-link"></i>Создать сайт в Diafan.Cloud</a>
					</form>
				</div>
				<div class="box_half box_height box_right">
					<form name="auth" method="post" action="http<?php echo (IS_HTTPS ? "s" : ''); ?>://<?php echo BASE_URL;?>/admin/" class="login-form">
						<input type="hidden" name="create" value="1">
						<p><img src="https://www.diafan.ru/img/error404/logo.png"></p><p>Временный демонстрационный сайт со всеми модулями <a href="https://www.diafan.ru/" target="_blank">DIAFAN.CMS</a> с демо-дизайном. <br>Демо-сайт будет существовать до тех пор, пока открыто окно Вашего браузера.
						Никакие данные не сохраняются.</p>
						<p><b>Внимание!</b> Это версия для демонстрации всех возможностей системы, предназначена для веб-разработчиков.</p>
						<button class="btn btn_blue btn_small">
							<i class="fa fa-plus"></i>
							Создать демо-сайт
						</button>
					</form>
				</div>
			</div>
		</div>
		<!-- |===============| wrap end |===============| -->
		
	</div>
	<insert name="show_include" file="counters">
</body>
</html>
