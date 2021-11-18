<?php
/**
 * Файл-блок шаблона
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2020 OOO «Диафан» (http://www.diafan.ru/)
 */

?>
<html>
<head>

<!-- шаблонный тег show_head выводит часть HTML-шапки сайта. Описан в файле themes/functions/show_head.php. -->
<insert name="show_head">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
	document.onreadystatechange = function () {
		switch (document.readyState) {
			case 'loading': document.body.classList.add('_loading'); break;
			case 'interactive': document.body.classList.remove('_loading'); document.body.classList.add('_ready'); break;
			case 'complete': document.body.classList.add('_load'); break;
		}
	}
</script>
<?php
if(SOURCE_JS != 6)
{
	echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i,900,900i&amp;amp;subset=cyrillic&amp;display=fallback">';
}
?>

<link rel="shortcut icon" href="<insert name="path">favicon.ico" type="image/x-icon">
<!-- шаблонный тег show_css подключает CSS-файлы. Описан в файле themes/functions/show_css.php. -->
<insert name="show_css" files="normalize.css,swiper.css,fa.css,new.css">

</head>
<body class="_loading">