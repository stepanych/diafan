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

<section class="page-d__subscript page-d__subscript_main">
	<!-- шаблонный тег вывода формы для подписчиков. Вид блока редактируется в файле modules/subscription/views/subscription.view.form_main.php.  -->
	<insert name="show_form" module="subscription" template="main" defer="emergence" defer_title="Подписаться на рассылку">
</section>

<section class="page-d__nav page-d__nav_broad">
	<!-- шаблонный тег вывода второго меню (параметр id=2). Настраивается в файле modules/menu/views/menu.view.show_block_nav_broad.php
	Документация тега http://www.diafan.ru/dokument/full-manual/templates-functions/#show_block_menu -->
	<insert name="show_block" module="menu" id="2" template="nav_broad">
</section>

<footer class="page-d__foot">
	<div class="foot-d _box">
		<div class="foot-d__item foot-d__contacts">
			<div class="foot-d__title"><insert value="Контакты"></div>
			<insert name="show_theme" module="site" tag="contacts">
		</div>
		<nav class="foot-d__item foot-d__nav">
			<div class="foot-d__title"><insert value="О магазине"></div>
			<!-- шаблонный тег вывода первого меню (параметр id=1). Настраивается в файле modules/menu/views/menu.view.show_menu.php, так как параметр template не был передан. Тогда в оформлении используются параметры tag
			Документация тега http://www.diafan.ru/dokument/full-manual/templates-functions/#show_block_menu -->
			<insert name="show_block" module="menu"
				id="1"
				count_level="1"
				tag_level_start_1="[div class='nav-d _underline'][ul class='menu-d nav-d__menu']"
				tag_start_1="[li class='item-d']"
				tag_end_1="[/li]"
				tag_level_end_1="[/ul][/div]"
				>
		</nav>
		<div class="foot-d__item foot-d__icons">
			<div class="foot-d__socnets">
				<div class="foot-d__title"><insert value="Поделиться в соцсетях"></div>
				<!-- шаблонный тег вывода кнопок социальных сетей. Правится в файле themes/functions/show_social_links_main.php -->
				<insert name="show_social_links_main">
			</div>
			<div class="foot-d__payments">
				<div class="foot-d__title"><insert value="Принимаем к оплате"></div>
				<insert name="show_theme" module="site" tag="payment">
			</div>
		</div>
		<div class="foot-d__item foot-d__website">
			<div class="website-d">
				<div class="website-d__item website-d__copyright">&copy; <insert name="show_year"> Demosite.ru</div>
				<div class="website-d__item website-d__cms">
					<!-- шаблонный тег подключает файл-блок -->
					<insert name="show_include" file="diafan">
				</div>
				<div class="website-d__item website-d__mistakes">
					<!-- шаблонный тег ошибка на сайте -->
					<insert name="show_block" module="mistakes">
				</div>
				<div class="website-d__item website-d__sitemap">
					<span class="icon-d fas fa-list"></span>
					<!-- шаблонный тег show_href выведет ссылку на карту сайта <a href="/map/"><img src="/img/map.png"></a>, на странице карты сайта тег выведет активную иконку -->
					<insert name="show_href" rewrite="map" alt="Карта сайта">
				</div>
				<div class="website-d__item website-d__statistic">
					<!-- шаблонный тег вывода количества пользователей on-line. Вид блока редактируется в файле modules/users/views/users.view.show_block.php. -->
					<insert name="show_block" module="users">
				</div>
			</div>
		</div>
	</div>
</footer>