var captcha_qa_i = 1; var captcha_qa_a_i = 1;
$('.js_captcha_qa_plus').click(function() {
	$('.js_captcha_qa_item_tpl').before($('.js_captcha_qa_item_tpl').clone(true));
	$('.js_captcha_qa_item_tpl:first').show().removeClass('js_captcha_qa_item_tpl').addClass('js_captcha_qa_item');
	$('.js_captcha_qa_item:last input[type=checkbox]').each(function(){
		$(this).attr('id', $(this).attr('id') + 'n' + captcha_qa_i);
	});
	$('.js_captcha_qa_item:last label').each(function(){
		$(this).attr('for', $(this).attr('for') + 'n' + captcha_qa_i);
	});
	$('.js_captcha_qa_item:last .js_add_name').each(function(){
		$(this).attr("name", $(this).attr("name").replace(/\[\]/g, "['"+captcha_qa_i+"']"));
	});
	$('.js_captcha_qa_item:last .js_add_name_a').each(function(){
		$(this).attr("name", $(this).attr("name").replace(/\[\]\[\]/g, "['"+captcha_qa_i+"'][]"));
	});
	$('.js_captcha_qa_item:last').attr('data-i', captcha_qa_i);
	captcha_qa_i = captcha_qa_i + 1;
	return false;
});
$('.js_captcha_qa_a_plus').click(function() {
	$(this).before($('.js_captcha_qa_item_tpl .js_captcha_qa_a').clone(true));

	var i = $(this).parents('.js_captcha_qa_item').data('i');
	$(this).prev('.js_captcha_qa_a').find('.js_add_name_a').each(function(){
		$(this).attr("name", $(this).attr("name").replace(/\[\]\[\]/g, "['"+i+"'][]"));
	});

	captcha_qa_a_i = captcha_qa_a_i + 1;
	
	$(this).prev('.js_captcha_qa_a').find('input[type=checkbox]').each(function(){
		$(this).attr('id', $(this).attr('id') + 'a' + captcha_qa_a_i);
	});
	$(this).prev('.js_captcha_qa_a').find('label').each(function(){
		$(this).attr('for', $(this).attr('for') + 'a' + captcha_qa_a_i);
	});
	return false;
});
$(document).on('change', '.js_captcha_qa_item input[type=checkbox]', function() {
	var val = 0;
	if($(this).is(':checked'))
	{
		val = 1;
	}
	$(this).prev('input[type=hidden]').val(val);
});

$(document).on('click', ".js_captcha_qa_item a[action=delete_param]", function(){
	if ( $(this).attr("confirm") && ! confirm( $(this).attr("confirm")))
	{
		return false;
	}
	$(this).parents(".js_captcha_qa_item").remove();
	return false;
});

$(document).on('click', ".js_captcha_qa_a a[action=delete_a]", function(){
	if ( $(this).attr("confirm") && ! confirm( $(this).attr("confirm")))
	{
		return false;
	}
	$(this).parents(".js_captcha_qa_a").remove();
	return false;
});
