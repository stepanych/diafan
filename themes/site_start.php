<?php
/**
 * Шаблон страницы сайта, назначенной по умолчанию как стартовая для сайта
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

<div class="page-d page-d_home">
	
	<!-- шаблонный тег подключает файл-блок blocks/header.php -->
	<insert name="show_include" file="header">

	<section class="page-d__slideshow page-d__slideshow_main">
		
		<!-- параметр show_slider (показывать слайдер) из модуля «Настройки шаблона» входящий в состав модуля «Страницы сайта» -->
		<?php if($this->diafan->_site->theme('show_slider'))
		{
			//шаблонный тег вывода баннеров. Блок выводит баннеры слайдера. Вид блока редактируется в файле modules/bs/views/bs.view.show_block_slider_main.php
			echo $this->diafan->_tpl->htmleditor('<insert name="show_block" module="bs" count="3" cat_id="1" template="slider_main">');
		}
		?>
	</section>

	<section class="page-d__content content-d _box">
		<main class="content-d__main">

			<!-- шаблонный тег вывода блока некоторых товаров из магазина. Вид блока товаров редактируется в файле modules/shop/views/shop.view.show_block.php. -->
			<insert name="show_block" module="shop" count="4" images="1" sort="rand" template="gall" defer="emergence" defer_title="Интернет-магазин">

			<!-- шаблонный тег вывода основного контента сайта -->
			<insert name="show_body">

			<!-- шаблонный тег вывода баннеров. Блок выводит все баннеры. Вид блока редактируется в файле modules/bs/views/bs.view.show_block.php-->
			<insert name="show_block" module="bs" count="2" cat_id="2" template="banners">

			<!-- шаблонный тег вывода блока статей. Вид блока статей редактируется в файле modules/clauses/views/clauses.view.show_block.php. -->
			<insert name="show_block" module="clauses" count="1" images="1" template="gall" defer="emergence" defer_title="Статьи">

			<!-- шаблонный тег вывода блока последних комментариев на сайте сайта. Вид блока редактируется в файле modules/comments/views/comments.view.show_block.php -->
			<insert name="show_block" module="comments" defer="emergence" defer_title="Последние комментарии">

			<!-- шаблонный тег вывода блока вопросов и ответов сайта. Вид блока редактируется в файле modules/faq/views/faq.view.show_block.php. -->
			<insert name="show_block" module="faq" count="2" often="0" defer="emergence" defer_title="Вопрос-Ответ">

		</main>
		
		<!-- шаблонный тег подключает файл-блок blocks/left.php -->
		<insert name="show_include" file="left">
	</section>
	
	<!-- шаблонный тег подключает файл-блок blocks/footer.php -->
	<insert name="show_include" file="footer">

</div>

<!-- шаблонный тег подключает файл-блок blocks/toolbar.php -->
<insert name="show_include" file="toolbar">
