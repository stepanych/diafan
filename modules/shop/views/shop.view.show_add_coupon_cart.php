<?php
/**
 * Шаблон формы активации купона template="cart"
 * 
 * Шаблонный тег <insert name="show_add_coupon" module="shop" template="cart">:
 * форма активации купона
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
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
'<div class="table-d__rows table-d__coupons">
	<div class="table-d__row">
		<div class="cell-d cell-d_coupon">';

			echo
			'<div class="coupon-d js_shop_add_coupon_cart">

				<div class="coupon-d__inside">
					<div class="coupon-d__title">';
						if($result["coupon"])
						{
							echo $this->diafan->_('Вы активировали купон'.(count($result["coupons"]) > 1 ? 'ы' : '').' %s. Есть другой купон?', true, implode(', ', $result["coupons"]));
						}
						else
						{
							echo $this->diafan->_('Код купона на скидку');
						}
						echo
					'</div>
					<div class="coupon-d__field field-d">
						<input type="text" name="coupon" placeholder="'.$this->diafan->_('Введите код', false).'" autocomplete="off">
						<div class="errors js_shop_add_coupon_cart_error error_coupon" style="display:none"></div>
					</div>
					<button class="coupon-d__button button-d button-d_short" type="button">
						<span class="button-d__name">'.$this->diafan->_('Активировать', false).'</span>
					</button>
				</div>

			</div>';

			echo
		'</div>
	</div>
</div>';