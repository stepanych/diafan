<?php
/**
 * Шаблон кнопки «Сравнить» для товаров
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



$check = empty($_SESSION['shop_compare'][$result["site_id"]][$result["id"]]) ? false : true;

echo
'<button class="interact-d interact-d_compare'.($check ? ' _active' : '').' js_shop_compare" type="submit"
	title="'.($check ? $this->diafan->_('Убрать из сравнения', false) : $this->diafan->_('Добавить к сравнению', false)).'"
	data-title1="'.$this->diafan->_('Добавить к сравнению', false).'"
	data-title2="'.$this->diafan->_('Убрать из сравнения', false).'">
	<span class="interact-d__icon icon-d fas fa-balance-scale"></span>
</button>';

echo
'<form action="" method="POST" class="js_shop_compare_form _hidden ajax" style="display:none;">
	<input type="hidden" name="module" value="shop">
	<input type="hidden" name="action" value="compare_goods">
	<input type="hidden" name="id" value="'.$result["id"].'">
	<input type="hidden" name="site_id" value="'.$result["site_id"].'">
	<input type="hidden" name="add" value="'.($check ? 0 : 1).'">
</form>';
