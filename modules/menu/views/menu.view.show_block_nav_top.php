<?php
/**
 * Шаблон меню template=navtop
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

echo
'<nav class="nav-d nav-d_horizontal nav-d_horizontal_laptop nav-d_horizontal_gray nav-d_horizontal_laptop_gray">
	<div class="nav-d__inside">
		<button class="nav-d__burger burger-d" type="button" title="'.$this->diafan->_('Информация', false).'">
			<span class="burger-d__icon icon-d"><span></span></span>
			<span class="burger-d__name">'.$this->diafan->_('Информация').'</span>
		</button>';
		echo $this->get('show_level_navmenu', 'menu', $result);
		echo
	'</div>
</nav>';