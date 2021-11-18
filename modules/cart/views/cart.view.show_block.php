<?php
/**
 * Шаблон блока корзины
 *
 * Шаблонный тег <insert name="show_block" module="cart" [template="шаблон"]>:
 * выводит информацию о заказанных товарах
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



echo '<div class="intercap-d intercap-d_cart js_show_cart">';
echo $this->get('info', 'cart', $result);
echo '</div>';