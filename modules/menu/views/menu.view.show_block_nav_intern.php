<?php
/**
 * Шаблон меню template=navint
 *
 * Шаблонный тег: вывод меню
 * Полный аналог функции show_block, но с другим оформлением. 
 * Нужен, если необходимо оформить другое меню на сайте
 * Вызывается с параметром template=topmenu при вызове тега. 
 * <insert name="show_block" module="menu" id="1" template="topmenu"> 
 * Параметр должен быть приклеен к имени функции в конце
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



if (empty($result['rows']))
{
	return false;
}

// echo '<section class="block-d block-d_menu _tile">';
echo '<section class="block-d block-d_menu">';

echo '<header class="block-d__name">'.$this->diafan->_('Продукция').'</header>';

// echo '<nav class="nav-d nav-d_vertical nav-d_internal">
echo
'<nav class="nav-d nav-d_vertical nav-d_vertical_gray">
	<div class="nav-d__inside">';
		echo $this->get('show_level_navmenu', 'menu', $result);
		echo
	'</div>
</nav>';

echo '</section>';
