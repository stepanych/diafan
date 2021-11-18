/**
 * Редактирование скидки, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */
var search;

$(document).on('click', ".rel_element_actions a", function() {
	var self = $(this);
	if (self.attr("action") != 'delete_rel_element') {
		return true;
	}
	if (! confirm(self.attr("confirm"))) {
		return false;
	}
	diafan_ajax.init({
		data:{
			action: 'delete_discount_good',
			module: 'shop',
			discount_id : $('input[name=id]').val(),
			good_id : self.parents(".rel_element").attr("good_id")
		},
		success: function(response) {
			self.parents(".rel_element").remove();
		}
	});
	return false;
});
$('.rel_module_plus').click(function() {
	var self = $(this);
	diafan_ajax.init({
		data:{
			action: 'show_discount_goods',
			module: 'shop',
			discount_id: $('input[name=id]').val()
		},
		success: function(response) {
			if (response.data) {
				$("#ipopup").html(prepare(response.data));
				centralize($("#ipopup"));
			}
		}
	});
	return false;
});
$(document).on('click', '.rel_module_navig a', function() {
	var self = $(this);
	diafan_ajax.init({
		data:{
			action: 'show_discount_goods',
			module: 'shop',
			discount_id: $('input[name=id]').val(),
			page: self.attr("page"),
			search: search,
		},
		success: function(response) {
			if (response.data) {
				$(".rel_all_elements_container").html(prepare(response.data));
			}
		}
	});
	return false;
});
$(document).on('keyup', '.rel_module_search', function() {
	search = $(this).val();
	diafan_ajax.init({
		data:{
			action: 'show_discount_goods',
			module: 'shop',
			discount_id: $('input[name=id]').val(),
			search: search
		},
		success: function(response) {
			if (response.data) {
				$(".rel_all_elements_container").html(prepare(response.data));
			}
		}
	});
	return false;
});

$(document).on('click', '.rel_module a', function() {
	var self = $(this);
	if (! self.parents('.rel_module').find('div').is('.rel_module_selected')) {
		diafan_ajax.init({
			data:{
				action: 'discount_good',
				module: 'shop',
				good_id: self.parents(".rel_module").attr("element_id"),
				discount_id: $('input[name=id]').val()
			},
			success: function(response) {
				self.parents('.rel_module').find('div').addClass('rel_module_selected');
				if (response.data) {
					$(".rel_elements").html(prepare(response.data));
				}
				if (response.id) {
					$("input[name=id]").val(response.id);
				}
			}
		});
	} else {
		diafan_ajax.init({
			data:{
				action: 'delete_discount_good',
				module: 'shop',
				discount_id: $('input[name=id]').val(),
				good_id : self.parents(".rel_module").attr("element_id")
			},
			success: function(response) {
				self.parents('.rel_module').find('div').removeClass('rel_module_selected');
				$(".rel_element[good_id="+self.parents(".rel_module").attr("good_id")+"]").remove();
			}
		});
	}
	return false;
});
$(".coupon_generate").click(function() {
	var r
	var digit = new Array("0","1","2","3","4","5","6","7","8","9")
	var lalp = new Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","y","z")
	pasw = new String()
	for (var i= 0; i<5; i++) {
		r = Math.random()
		if ( (r - 1.0/3.0) < 0.0) {
			r = Math.floor(Math.random() * 9);
			pasw += digit[r]
		} else {
			r = Math.floor(Math.random() * 24);
			pasw += lalp[r]
		}
	}
	$(this).prev("input").val(pasw);
	return false;
});

$(document).on('click', ".param_actions a[action=delete_param]", function() {
	if ( $(this).attr("confirm") && ! confirm( $(this).attr("confirm"))) {
		return false;
	}
	var param_container = $(this).parents('.param_container');
	if(param_container.find('.param').length == 1) {
		return false;
	}
	$(this).parents(".param").remove();
	if(param_container.find('.param').length == 1) {
		param_container.find('.param a[action=delete_param]').hide();
	}
	return false;
});

$('.param_plus').click(function() {
	var last = $(this).parents('.param_container').find('.param:last');
	last.after(last.clone(true));
	$(this).parents('.param_container').find('.param:last input').val('');
	$(this).parents('.param_container').find('.param a[action=delete_param]').show();
	return false;
});

$('input[name=deduction]').blur(function() {
	if($(this).val()) {
		$('input[name=discount]').val('');
	}
}).focus(function() {
	$('input[name=discount]').addClass('item_disable');
	$(this).removeClass('item_disable');
});

$('input[name=discount]').blur(function() {
	if($(this).val()) {
		$('input[name=deduction]').val('');
	}
}).focus(function() {
	$('input[name=deduction]').addClass('item_disable');
	$(this).removeClass('item_disable');
});
