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



<section class="page-d__hpanel">
	<div class="hpanel-d _box">
		<div class="hpanel-d__nav">
			<!-- шаблонный тег вывода первого меню (параметр id=1). Настраивается в файле modules/menu/views/menu.view.show_block_nav_top.php
			Документация тега http://www.diafan.ru/dokument/full-manual/templates-functions/#show_block_menu -->
			<insert name="show_block" module="menu" id="1" template="nav_top">
		</div>
		<div class="hpanel-d__langs">
			<!-- шаблонный тег вывода блока ссылок на разные языковые версии сайта. Вид формы редактируется в файле modules/languages/views/languages.view.show_block.php. -->
			<insert name="show_block" module="languages">
		</div>
		<div class="hpanel-d__contacts contacts-d">
			<div class="contact-d contact-d_email contact-d_tooltip">
				<div class="contact-d__icon icon-d fas fa-envelope-open"></div>
				<div class="contact-d__list">
					<a href='mailto:<insert name="show_theme" module="site" tag="email" useradmin="false">'><insert name="show_theme" module="site" tag="email"></a>
				</div>
			</div>
			<div class="contact-d contact-d_phone contact-d_tooltip">
				<div class="contact-d__icon icon-d fas fa-phone"></div>
				<div class="contact-d__list">
					<a href='tel:<insert name="show_theme" module="site" tag="phone" useradmin="false" template="only_phone">'><insert name="show_theme" module="site" tag="phone"></a>
				</div>
			</div>
		</div>
	</div>
</section>

<header class="page-d__hboard">
	<div class="hboard-d _box">
		<div class="hboard-d__signboard">
			<a class="signboard-d" href='<insert name="path_url">'><insert name="show_theme" module="site" tag="logo" template="logo">
				<div class="signboard-d__inscript inscript-d">
					<div class="inscript-d__monogram"><insert name="show_theme" module="site" tag="logo_name"></div>
					<div class="inscript-d__slogan"><insert name="show_theme" module="site" tag="logo_text"></div>
				</div>
			</a>
		</div>
		<div class="hboard-d__search">
			<!-- шаблонный тег вывода формы поиска. Вид формы редактируется в файле modules/search/views/search.view.show_search.php. -->
			<insert name="show_search" module="search" template="top" ajax="true">
		</div>
		<!-- шаблонный тег вывода формы входа и регистрации пользователей. Вид формы редактируется в файле modules/registration/views/registration.view.show_login_top.php. -->
		<?php if($this->diafan->_site->theme('show_lk'))
		{
			echo '<div class="hboard-d__auth">';
			echo $this->diafan->_tpl->htmleditor('<insert name="show_login" module="registration" template="top">');
			echo '</div>';
		}
		?>
		<div class="hboard-d__intercaps intercaps-d">
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
	</div>
</header>

<section class="page-d__nav page-d__nav_main">
	<!-- шаблонный тег вывода второго меню (параметр id=2). Настраивается в файле modules/menu/views/menu.view.show_block_nav_main.php
	Документация тега http://www.diafan.ru/dokument/full-manual/templates-functions/#show_block_menu -->
	<insert name="show_block" module="menu" id="2" template="nav_main">
</section>