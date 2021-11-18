<?php
/**
 * Шаблон формы активации купона
 * 
 * Шаблонный тег <insert name="show_add_coupon" module="shop" [template="шаблон"]>:
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
'<section class="coupon-d">
	<form method="post" action="" class="js_shop_form ajax">
		<input type="hidden" name="action" value="add_coupon">
		<input type="hidden" name="form_tag" value="'.$result["form_tag"].'">
		<input type="hidden" name="module" value="shop">

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
			</div>
			<button class="coupon-d__button button-d button-d_short" type="submit">
				<span class="button-d__name">'.$this->diafan->_('Активировать', false).'</span>
			</button>
		</div>

		<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>
	</form>
</section>';
