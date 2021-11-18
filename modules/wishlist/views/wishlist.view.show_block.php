<?php
/**
 * Шаблон блока списка желаний
 *
 * Шаблонный тег <insert name="show_block" module="wishlist" [template="шаблон"]>:
 * выводит информацию об отложенных товарах
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



echo
'<div class="intercap-d intercap-d_wish">
	<a class="intercap-d__tip tip-d" href="'.$result['link'].'">
		<span class="tip-d__icon icon-d far fa-heart"></span>
		<span class="tip-d__amount amount-d amount-d_stick">
			<strong class="amount-d__num js_show_wishlist">';
				echo $this->get('info', 'wishlist', $result);
				echo
			'</strong>
		</span>
	</a>
</div>';
