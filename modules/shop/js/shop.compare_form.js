/**
 * JS-сценарий модуля
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */



$(document).on('click', '.js_shop_compare', function() {
	$(this).closest('.js_shop').find('.js_shop_compare_form').first().submit();
});
diafan_ajax.before['shop_compare_goods'] = function (form) {
	$(form).closest('.js_shop').find('.js_shop_compare').attr('disabled', 'disabled');
}
diafan_ajax.success['shop_compare_goods'] = function (form, result) {

	var $form = $(form);
	var $compare = $form.closest('.js_shop').find('.js_shop_compare');

	if (result.result == 'ok')
	{
		var add = $compare.hasClass('_active') ? 1 : 0;
		$compare.toggleClass('_active');
		$form.find('input[name=add]').val(add);

		var title = '';
		if (add)
		{
			title = $compare.data('title1');
		}
		else
		{
			title = $compare.data('title2');
		}
		if (title)
		{
			$compare.attr('title', title)
		}
	}
	$compare.removeAttr('disabled');
}
