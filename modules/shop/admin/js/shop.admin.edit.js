/**
 * Редактирование товаров, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$(document).on('click', ".good_set_actions a", function() {
	var self = $(this);
	if (self.attr("action") != 'delete_good_set')
	{
		return true;
	}
	if (! confirm(self.attr("confirm")))
	{
		return false;
	}
	diafan_ajax.init({
		data:{
			action: 'delete_good_set',
			module: 'shop',
			element_id : self.parents(".good_set").attr("element_id"),
			good_set_id : self.parents(".good_set").attr("good_set_id")
		},
		success: function(response){
			self.parents(".good_set").remove();
		}
	});
	return false;
});
$('#js_good_set_plus').click(function() {
	var self = $(this);
	diafan_ajax.init({
		data:{
			action: 'show_goods_set',
			module: 'shop',
			element_id: $('input[name=id]').val()
		},
		success: function(response){
			if (response.data)
			{
				$("#ipopup").html(prepare(response.data));
				centralize($("#ipopup"));
			}
		}
	});
	return false;
});
var set_search = '';
var set_cat_id = '';
$(document).on('click', '.goods_set_navig a', function() {
	var self = $(this);
	diafan_ajax.init({
		data:{
			action: 'show_goods_set',
			module: 'shop',
			element_id: $('input[name=id]').val(),
			page: self.attr("page"),
			search: set_search,
			cat_id: set_cat_id
		},
		success: function(response){
			if (response.data)
			{
				$(".goods_set_container").html(prepare(response.data));
			}
		}
	});
	return false;
});
$(document).on('keyup change', '.good_set_search, .good_set_cat_id', function() {
	if($(this).is('.good_set_search'))
	{
		set_search = $(this).val();
	}
	if($(this).is('.good_set_cat_id'))
	{
		set_cat_id = $(this).val();
	}
	diafan_ajax.init({
		data:{
			action: 'show_goods_set',
			module: 'shop',
			element_id: $('input[name=id]').val(),
			search: set_search,
			cat_id: set_cat_id
		},
		success: function(response){
			if (response.data)
			{
				$(".goods_set_container").html(prepare(response.data));
			}
		}
	});
});
$(document).on('click', '.good_set_module a', function() {
	var self = $(this);
	if (! self.parents('.good_set_module').is('.good_set_module_selected'))
	{
		diafan_ajax.init({
			data:{
				action: 'add_good_set',
				module: 'shop',
				good_set_id: self.parents(".good_set_module").attr("element_id"),
				element_id: $('input[name=id]').val()
			},
			success: function(response){
				self.parents('.good_set_module').addClass('good_set_module_selected');
				if (response.data)
				{
					$(".goods_set").html(prepare(response.data));
				}
				if (response.id)
				{
					$("input[name=id]").val(response.id);
				}
			}
		});
	}
	else
	{
		diafan_ajax.init({
			data:{
				action: 'delete_good_set',
				module: 'shop',
				element_id : $('input[name=id]').val(),
				good_set_id : self.parents(".good_set_module").attr("element_id")
			},
			success: function(response){
				self.parents('.good_set_module').removeClass('good_set_module_selected');
				$(".good_set[good_set_id="+self.parents(".good_set_module").attr("element_id")+"]").remove();
			}
		});
	}
	return false;
});
