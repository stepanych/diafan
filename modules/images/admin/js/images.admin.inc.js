/**
 * Подключение модуля к административной части других модулей, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */
var images_count = 0;
var images_view_search = '';
var images_view_cat_id = '';

init_sort();

$('.fileupload').each(function(){
	var id = $(this).attr('id');
	var self = $(this);
	var param_id = $(this).attr('param_id');
	var tmpcode = $('input[name=tmpcode]').val();
	if(tmpcode == undefined) {
		tmpcode = '';
	}
	$(this).fileupload({
		dataType: 'json',
		submit: function(event, data) {
			if(self.data('maxsize') && data.files[0].size > self.data('maxsize'))
			{
				alert(self.data('maxsize_error')+': '+data.files[0].name);
				return false;
			}
			event = event || window.event;
			var target = event.target || event.srcElement;
			if (target.classList.contains("fileupload")
			&& event.originalEvent && event.originalEvent.type == 'paste') {
				var obj = $(event.originalEvent.target);
				if (obj.length && ! obj.closest('.div_images_links').length) {
					event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);
					return false;
				}
			}

			images_count = images_count+1;
			$('.errors').hide();
			data.formData = {
				action : "upload",
				ajax: 1,
				module: "images",
				id: $('input[name=id]').val(),
				tmpcode: tmpcode,
				name: $('input[name=name]').val(),
				site_id: $('input[name=site_id], select[name=site_id]').val(),
				param_id: param_id
			};
			$.each(data.files, function (k,v){
				$('.images[param_id='+param_id+'] .fileupload').after('<div class="images_status" name="'+v.name.replace(/[^a-z0-9]+/, '')+'">...uploading '+htmlentities(v.name)+'</div>');
			});
		},
		done: function (event, data) {
			images_count = images_count-1;
			if(images_count < 1) {
				diafan_ajax.init({
					data:{
						action : "show",
						module: "images",
						id: $('input[name=id]').val(),
						tmpcode: tmpcode,
						site_id: $('input[name=site_id], select[name=site_id]').val(),
						param_id: param_id
					},
					success: function(response) {
						if (response.data && response.target) {
							$(response.target).html(prepare(response.data));
							init_sort();
						}
						$('.images[param_id='+param_id+'] .images_status_ok').remove();
					}
				});
			}
			result_upload(data.result, param_id);
			$.each(data.files, function (k,v){
				$('.images[param_id='+param_id+'] .images_status[name="'+v.name.replace(/[^a-z0-9]+/, '')+'"]').html(htmlentities(v.name)+' <span style="color:green">ok</span>').removeClass('images_status').addClass('images_status_ok');
			});
		}
	});
});

$(document).on('click', ".images_actions a", function() {
	var param_id = $(this).parents('td').attr('param_id');
	var self = $(this);
	if (! self.attr("action")) {
		return true;
	}
	if (self.attr("confirm") && ! confirm(self.attr("confirm"))) {
		return false;
	}
	diafan_ajax.init({
		data:{
			action: self.attr("action"),
			module: 'images',
			element_id : self.parents(".images_actions").attr("element_id"),
			tmpcode: $('input[name=tmpcode]').val(),
			image_id : self.parents(".images_actions").attr("image_id")
		},
		success: function(response) {
			if (response.error) {
				$(".error_images"+param_id).html(prepare(response.error)).show();
			}
			if(response.errors && response.errors['image']) {
				$(form).find(".error_images"+param_id).html(prepare(response.errors['image'])).show();
			}
			if (response.target) {
				$(response.target).html(prepare(response.data));
				init_sort();
				if ($(response.target).is('.ipopup')) {
					centralize($(response.target));
				}
			} else {
				self.parents(".images_actions").html(prepare(response.data));
				init_sort();
			}
		}
	});
	return false;
});

$(document).on('click', ".images_actions .image", function() {
	if($(this).is('.image_check'))
	{
		$(this).removeClass('image_check');
	}
	else
	{
		$(this).addClass('image_check');
	}
	if($(this).parents('.images').find('.image_check').length)
	{
		$(this).parents('.images').find('.js_delete_images').removeClass('btn_disabled');
	}
	else
	{
		$(this).parents('.images').find('.js_delete_images').addClass('btn_disabled');
	}
});

$(document).on('click', '.js_delete_images', function(){
	if($(this).is('.btn_disabled'))
	{
		return false;
	}
	if ($(this).data("confirm") && ! confirm($(this).data("confirm"))) {
		return false;
	}
	var image_ids = new Array();
	var element_id = 0;
	$(this).parents('.images').find('.image_check').each(function()
	{
		image_ids.push($(this).parents('.images_actions').attr('image_id'));
		element_id = $(this).parents('.images_actions').attr('element_id');
	});
	diafan_ajax.init({
		data:{
			action: 'delete',
			module: 'images',
			element_id : element_id,
			tmpcode: $('input[name=tmpcode]').val(),
			image_ids : image_ids
		},
		success: function(response) {
			if (response.error) {
				$(".error_images"+param_id).html(prepare(response.error)).show();
			}
			if(response.errors && response.errors['image']) {
				$(form).find(".error_images"+param_id).html(prepare(response.errors['image'])).show();
			}
			if (response.target) {
				$(response.target).html(prepare(response.data));
				init_sort();
				if ($(response.target).is('.ipopup')) {
					centralize($(response.target));
				}
			} else {
				self.parents(".images_actions").html(prepare(response.data));
				init_sort();
			}
		}
	});
});

$(document).on('click', '.view_images', function() {
	var param_id = $(this).parents('.unit').attr('param_id');
	diafan_ajax.init({
		data:{
			action: 'view',
			module: 'images',
			param_id: param_id,
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

$(document).on('keyup', '.view_images_search', function() {
	images_view_search = $(this).val();
	diafan_ajax.init({
		data:{
			action: 'view',
			module: 'images',
			param_id: $(".view_images_all_container").attr("param_id"),
			search: images_view_search,
			cat_id: images_view_cat_id
		},
		success: function(response) {
			if (response.data) {
				$(".view_images_all_container").html(prepare(response.data));
			}
		}
	});
});

$(document).on('change', '.view_images_cat_id', function() {
	images_view_cat_id = $(this).val();
	diafan_ajax.init({
		data:{
			action: 'view',
			module: 'images',
			param_id: $(".view_images_all_container").attr("param_id"),
			search: images_view_search,
			cat_id: images_view_cat_id
		},
		success: function(response) {
			if (response.data) {
				$(".view_images_all_container").html(prepare(response.data));
			}
		}
	});
});

$(document).on('click', '.view_images_navig a', function() {
	var self = $(this);
	diafan_ajax.init({
		data:{
			action: 'view',
			module: 'images',
			param_id: $(".view_images_all_container").attr("param_id"),
			page: self.attr("page"),
			search: images_view_search,
			cat_id: images_view_cat_id
		},
		success: function(response) {
			if (response.data) {
				$(".view_images_all_container").html(prepare(response.data));
			}
		}
	});
	return false;
});

$(document).on('click', '.view_image', function() {
	var self = $(this);
	diafan_ajax.init({
		data:{
			action: 'upload_view',
			mode: (! self.hasClass("view_image_selected") ? 'upload' : 'delete'),
			module: 'images',
			image_id: $(self).attr("image_id"),
			tmpcode: $('input[name=tmpcode]').val(),
			id: $('input[name=id]').val(),
		},
		success: function(response) {
			if (! self.hasClass("view_image_selected")) {
				self.addClass('view_image_selected');
			} else {
				self.removeClass('view_image_selected');
			}
			if (response.target) {
				$(response.target).html(prepare(response.data));
				init_sort();
			}
			if (response.remove) {
				self.remove();
			}
		}
	});
	return false;
});

$(document).on('click', ".ajax_save_image", function() {
	var self = $(this);
	var main = 0;
	if(self.closest("div").find("input[name=main]").is(':checked'))
	{
		main = 1;
	}
	diafan_ajax.init({
		data:{
			action: 'save',
			module: 'images',
			element_id : $('input[name=id]').val(),
			tmpcode: $('input[name=tmpcode]').val(),
			image_id : self.attr("image_id"),
			alt : self.closest("div").find("input[name=alt]").val(),
			title : self.closest("div").find("input[name=title]").val(),
			main: main
		},
		success: function(response) {
			if (response.result) {
				$('.ipopup__close').click();
			}
			if (response.data && response.target) {
				$(response.target).html(prepare(response.data));
				init_sort();
			}
		}
	});
});
$(document).on('click', ".images_selectarea_button", function() {
	var selectarea_div = $(this).parents('.selectarea');
	var param_id = selectarea_div.parents('.images').attr('param_id');
	if(selectarea_div.find("input[name=x1]").val() == selectarea_div.find("input[name=x2]").val()
	||  selectarea_div.find("input[name=y1]").val() == selectarea_div.find("input[name=y2]").val())
	{
		alert(selectarea_div.find(".images_selectarea_info").text());
		return false;
	}
	diafan_ajax.init({
		data:{
			action: "selectarea",
			module : "images",
			x1: selectarea_div.find("input[name=x1]").val(),
			x2: selectarea_div.find("input[name=x2]").val(),
			y1: selectarea_div.find("input[name=y1]").val(),
			y2: selectarea_div.find("input[name=y2]").val(),
			id: selectarea_div.find("input[name=image_id]").val(),
			variation_id: selectarea_div.find("input[name=variation_id]").val()
		},
		success: function(response) {
			selectarea_div.find(".images_selectarea").imgAreaSelect({remove : true});
			$('.selectarea').text('');
			selectarea_div.text('');
			get_selectarea(param_id);
		}
	});
	return false;
});

$(".images_upload_links").click(function() {
	var param_id = $(this).attr('param_id');
	var tmpcode = $('input[name=tmpcode]').val();
	if(tmpcode == undefined) {
		tmpcode = '';
	}
	var textarea_links = $(this).parents('.div_images_links').find('input[type=text]');
	diafan_ajax.init({
		data:{
			action : "upload_links",
			links : textarea_links.val(),
			module: "images",
			id: $('input[name=id]').val(),
			tmpcode: tmpcode,
			name: $('input[name=name]').val(),
			site_id: $('input[name=site_id], select[name=site_id]').val(),
			param_id: param_id
		},
		success: function(response) {
			result_upload(response, param_id);
			textarea_links.val('');
		}
	});
});

function result_upload(response, param_id) {
	if (response.selectarea) {
		$.each(response.selectarea, function (k, v) {
			$('.images[param_id='+param_id+'] .selectarea').after('<div class="selectarea_next" style="display:none">'+prepare(v)+'</div>');
		});
		get_selectarea(param_id);
	}
	if (response.id) {
		$("input[name=id]").val(response.id);
	}
	if (response.error) {
		$(".error_images"+param_id).html(prepare(response.error)).show();
	}
	if(response.errors && response.errors['image']) {
			$(".error_images"+param_id).html(response.errors['image']).show();
	}
	if (response.data && response.target) {
		$(response.target).html(prepare(response.data));
		init_sort();
	}
	if (response.hash) {
		$('input[name=check_hash_user]').val(response.hash);
		$('.check_hash_user').text(response.hash);
	}
}
function get_selectarea(param_id) {
	var selectarea = $(".images[param_id="+param_id+"] .selectarea");
	if(selectarea.text()) {
		return;
	}
	if($('.images[param_id='+param_id+'] .selectarea_next').length) {
		selectarea.html($('.images[param_id='+param_id+'] .selectarea_next').last().html()).show();
		$('.images[param_id='+param_id+'] .selectarea_next').last().remove();

		selectarea.show();

		selectarea.find(".images_selectarea").imgAreaSelect({remove : true});

		selectarea.find(".images_selectarea").imgAreaSelect({
			aspectRatio: selectarea.find('.images_selectarea').attr('select_width')+":"+selectarea.find('.images_selectarea').attr('select_height'),
			handles: true,
			onSelectEnd: function (img, selection) {
				selectarea.find("input[name=x1]").val(selection.x1);
				selectarea.find("input[name=y1]").val(selection.y1);
				selectarea.find("input[name=x2]").val(selection.x2);
				selectarea.find("input[name=y2]").val(selection.y2);
			}
		});
	}
}
function init_sort()
{
	$(".images_sort").sortable({
		cursor: 'move',
		revert: 100,
		stop:function (event, ui) {
			var s = 1;
			var sort = new Array();
			$(this).find('.images_actions').each(function()
			{
				sort.push({i:$(this).attr('image_id'),s:s});
				s++;
			});
			diafan_ajax.init({
				data:{
					action : "sort",
					module: "images",
					sort: sort
				}
			});
		}
	});
}