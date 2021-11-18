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



<section class="toolbar-d">
	<div class="toolbar-d__inside _box">
		<div class="toolbar-d__signboard">
			<insert name="show_theme" module="site" tag="logo" template="logo">
		</div>
		<div class="toolbar-d__search">
			<!-- шаблонный тег вывода формы поиска. Вид формы редактируется в файле modules/search/views/search.view.show_search.php. -->
			<insert name="show_search" module="search" template="top" ajax="true">
		</div>
		<!-- шаблонный тег вывода формы входа и регистрации пользователей. Вид формы редактируется в файле modules/registration/views/registration.view.show_login_top.php. -->
		<?php if($this->diafan->_site->theme('show_lk'))
		{
			echo '<div class="toolbar-d__auth">';
			echo $this->diafan->_tpl->htmleditor('<insert name="show_login" module="registration" template="top">');
			echo '</div>';
		}
		?>
		<div class="toolbar-d__intercaps intercaps-d">
			<insert name="show_compare_block" module="shop">
			<!-- шаблонный тег вывода количества отложенных товаров. Вид формы редактируется в файле modules/wishlist/views/wishlist.view.show_block.php. -->
			<?php if($this->diafan->_site->theme('show_favorite'))
			{
				echo $this->diafan->_tpl->htmleditor('<insert name="show_block" module="wishlist">');
			}
			?>
			<!-- шаблонный тег вывода формы корзины. Вид формы редактируется в файле modules/cart/views/cart.view.show_block.php. -->
			<insert name="show_block" module="cart">
		</div>
		<div class="toolbar-d__nav">
			<!-- шаблонный тег вывода второго меню (параметр id=2). Настраивается в файле modules/menu/views/menu.view.show_block_nav_tool.php
			Документация тега http://www.diafan.ru/dokument/full-manual/templates-functions/#show_block_menu -->
			<insert name="show_block" module="menu" id="2" template="nav_tool">
		</div>
	</div>
</section>

<!-- шаблонный тег show_js подключает JS-файлы. Описан в файле themes/functions/show_js.php. -->
<insert name="show_js">

<button class="upper-d button-d button-d_up" type="button"><span class="button-d__icon icon-d fas fa-arrow-up"></span></button>
<script type="text/javascript" src='<insert name="custom" path="js/swiper.js" absolute="true">' charset="UTF-8"></script>
<script type="text/javascript" src='<insert name="custom" path="js/main.js" absolute="true" compress="js">' charset="UTF-8"></script>

<!-- шаблонный тег подключает on-line консультант -->
<insert name="show_block" module="consultant" system="jivosite" defer="async">

<!-- шаблонный тег подключает вывод информации о Политике конфиденциальности. Если необходимо вывести свой текст сообщения, то добавле его в атрибут "text". -->
<insert name="show_privacy" hash="false" text="">

<insert name="show_include" file="counters">
</body>
</html>