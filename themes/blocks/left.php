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
<aside class="content-d__aside">

	<!-- шаблонный тег вывода формы поиска по товарам. Вид формы редактируется в файле modules/shop/views/shop.view.show_search.php. -->
	<insert name="show_search" module="shop" cat_id="current" ajax="true" defer="emergence" defer_title="Поиск по товарам">

	<!-- шаблонный тег вывода формы входа и регистрации пользователей. Вид формы редактируется в файле modules/registration/views/registration.view.show_login.php. -->
	<?php if($this->diafan->_site->theme('show_lk'))
	{
		echo $this->diafan->_tpl->htmleditor('<insert name="show_login" module="registration" defer="emergence" defer_title="Профиль">');
	}
	?>
	
	<!-- шаблонный тег вывода блока некоторых товаров из магазина. Вид блока товаров редактируется в файле modules/shop/views/shop.view.show_block.php. -->
	<insert name="show_block" module="shop" count="1" images="1" sort="rand" template="left" defer="emergence" defer_title="Товары">	

	<!-- шаблонный тег вывода блока новостей. Вид блока файлов редактируется в файле modules/news/views/news.view.show_block.php. -->
	<insert name="show_block" module="news" count="2" images="1" defer="emergence" defer_title="Новости">

	<!-- шаблонный тег вывода блока некоторых изображений из фотогалереи. Вид блока фотографий редактируется в файле modules/photo/views/photo.view.show_block.php. -->
	<insert name="show_block" module="photo" sort="rand" count="1" cat_id="1" defer="emergence" defer_title="Фотографии">

</aside>