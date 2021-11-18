/**
 * JS-сценарий модуля «Список желаний»
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$('.js_wishlist_form :submit, .wishlist_form :submit').hide();
$(document).on('change', '.js_wishlist_form input[type=text], .js_wishlist_form input[type=number], .wishlist_form input[type=text], .wishlist_form input[type=number]', wishlist_submit);

$(document).on('click', '.js_wishlist_remove, .wishlist_remove span', function() {
	if ($(this).attr('confirm') && ! confirm($(this).attr('confirm'))) {
		return false;
	}
	$(this).find('input[type=checkbox]').prop('checked',true);
	$(this).find('input[type=hidden]').val(1);
	wishlist_submit();
});
$(document).on('click', '.js_wishlist_count_minus, .wishlist_count_minus', function() {
	var count = $(this).parents('.js_wishlist_count, .wishlist_count').find('input');
	if(count.val() > 1) {
		count.val(count.val() * 1 - 1);
	}
	wishlist_submit();
});

$(document).on('click', '.js_wishlist_count_plus, .wishlist_count_plus', function() {
	var count = $(this).parents('.js_wishlist_count, .wishlist_count').find('input');
	count.val(count.val() * 1 + 1);
	wishlist_submit();
});

function wishlist_submit() {
  $('.js_wishlist_form, .wishlist_form').submit();
}

$(document).on('click', '.js_wishlist_buy :button, .wishlist_buy :button', function() {
	var self = $(this);
	var additional_cost = [];
	self.parents('.js_wishlist_item').find('.js_shop_additional_cost input[type="checkbox"]:checked').each(function() {
		additional_cost.push($(this).val());
	});
	$(".js_wishlist_form, .wishlist_form").ajaxSubmit({
		data: {
			action: 'buy',
			module: 'wishlist',
			good_id: self.attr('good_id'),
			additional_cost: additional_cost,
			count: self.parents('.js_wishlist_item, .wishlist tr').find('.js_wishlist_count input, .wishlist_count input').val()
		},
		success: function (result, statusText, xhr, form) {
			
			try
			{
				var response = $.parseJSON(result);

				if (response.data)
				{
					if (response.data.hasOwnProperty('#show_cart')) {
						$('.js_show_cart').html(prepare(response.data['#show_cart']));
					}
					if (response.data.hasOwnProperty('#show_wishlist')) {
						$('.js_show_wishlist').html(prepare(response.data['#show_wishlist']));
					}
				}
			}
			catch (err) { }

			return diafan_ajax.result(form, result);
		}
	});
});

diafan_ajax.success['wishlist_recalc'] = function (form, result) {

	if (result.data && result.data.hasOwnProperty('#show_wishlist')) {
		$('.js_show_wishlist').html(prepare(result.data['#show_wishlist']));
	}
}
