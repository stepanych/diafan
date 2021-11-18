/**
 * JS-сценарий модуля «Сохраненная корзина»
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$('.js_cart_share').click(function() {
	$('input[name=action]', $(this).parents('form')).val($(this).data('action'));
	$(this).parents('form').submit();
	return false;
});