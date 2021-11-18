<?php
/**
 * Основной шаблон сайта
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2020 OOO «Диафан» (http://www.diafan.ru/)
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
?>

<!-- Полный справочник по шаблонным тегам в документации:  https://www.diafan.ru/dokument/full-manual/templates-functions/ -->


<!-- шаблонный тег подключает файл-блок blocks/head.php -->
<insert name="show_include" file="head">

<div class="page-d page-d_internal">
	
	<!-- шаблонный тег подключает файл-блок blocks/header.php -->
	<insert name="show_include" file="header">

	<section class="page-d__content content-d _box">
		<main class="content-d__main">
			
			<!-- шаблонный тег вывода навигации "Хлебные крошки"-->
			<insert name="show_breadcrumb">

			<!-- шаблонный тег вывода основного контента сайта -->
			<insert name="show_body">

		</main>
		
		<!-- шаблонный тег подключает файл-блок blocks/left.php -->
		<insert name="show_include" file="left">
	</section>
	
	<!-- шаблонный тег подключает файл-блок blocks/footer.php -->
	<insert name="show_include" file="footer">

</div>

<!-- шаблонный тег подключает файл-блок blocks/toolbar.php -->
<insert name="show_include" file="toolbar">