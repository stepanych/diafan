/**
 * JS-сценарий модуля
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
 */



$(document).on('click', '.subscript-d_main .subscript-d__robot', function () {
	$(this).closest('.subscript-d').toggleClass('_subscript_captcha-show');
});
diafan_ajax.success['subscription_add'] = function (form, response) {
	if (response.captcha) {
		$(':submit', form).closest('.subscript-d').addClass('_subscript_captcha-show');
	}
	if (response.result == 'success') {
		$(':submit', form).closest('._subscript_captcha-show').removeClass('_subscript_captcha-show');
	}
}