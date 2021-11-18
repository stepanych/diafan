/**
 * JS-сценарий модуля «Корзина товаров, оформление заказа»
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
 */

var cart_form = function(){
	var self = {
		objects: {
			form: {
				name: '.js_cart_table_form',
				children: {
					submit: ':submit',
					checkbox: ':checkbox',
					radio: ':radio',
					number: 'input[type=number]',
					item: {
						name: '.js_cart_item',
						children: {
							remove: {
									name: '.js_cart_remove',
									attr: {
										confirm: 'data-confirm',
									},
							},
							count: {
								name: '.js_cart_count',
								children: {
									input:
									{
										name: 'input',
										attr: {
											max: 'data-max',
											min: 'data-min',
										},
									},
									plus:
									{
										name: '.js_cart_count_plus',
										class_disabled: 'disabled',
									},
									minus:
									{
										name: '.js_cart_count_minus',
										class_disabled: 'disabled',
									},
								},
							},
						},
					},
				},
			},
			share_link_copy: {
				name: '#js_cart_share_copy',
				attr: {
					result: 'data-result',
				},
			},
			share_link_email: {
				name: '#js_cart_share_email',
				attr: {
					send: 'data-send',
				},
			},
			share_link_email_input: {
				name: '#js_share_link_email_input'
			},
			share_link_error: {
				name: '#js_share_link_error'
			}
		},
		init: function(){
			$(_('form.submit')).hide();
			return this;
		},
		events: function(){
			// $(document).on('change', _('form.item.count.input'), self.submit);
			$(document).on('change', _('form.number'), self.submit);
			$(document).on('change', _('form.radio'), self.submit);
			$(document).on('change', _('form.checkbox'), self.submit);
			$(document).on('click', _('form.item.remove'), self.remove);
			$(document).on('click', _('form.item.count.minus'), self.count_minus);
			$(document).on('click', _('form.item.count.plus'), self.count_plus);
			$(document).on('keyup', _('form.item.count.input'), self.count_change);
			$(document).on('click', _('share_link_copy'), self.share_link_copy);
			$(document).on('click', _('share_link_email'), self.share_link_email);
			$(document).on('change', _('share_link_email_input'), self.share_link_change);
			return this;
		},
		count_plus: function(){
			var input = $(this).parents(__('form.item.count')).find(__('form.item.count.input'));
			self.format_count(input);
			input.val(input.val() * 1 + 1);
			var max = input.attr(__('form.item.count.input.attr.max'));
			if(max != 0 && input.val() * 1 > max * 1)
			{
				input.val(max);
				$(this).addClass(__('form.item.count.plus.class_disabled'));
			}
			else
			{
				self.submit();
			}
			return false;
		},
		count_minus: function(){
			var input = $(this).parents(__('form.item.count')).find(__('form.item.count.input'));
			self.format_count(input);
			var min = input.attr(__('form.item.count.input.attr.min'));
			if(input.val() > 0) {
				input.val(input.val() * 1 - 1);
			}
			if(min && input.val() < min)
			{
				input.val(min);
				$(this).addClass(__('form.item.count.minus.class_disabled'));
			}
			else
			{
				self.submit();
			}
		},
		count_change: function(){
			self.format_count($(this));
		},
		remove: function(){
			if ($(this).attr(__('form.item.remove.attr.confirm')) && ! confirm($(this).attr(__('form.item.remove.attr.confirm')))) {
				return false;
			}
		},
		submit: function(){
			$(_('form')).submit();
		},
		format_count: function(input){
			input.val().replace(/,/g, ".");
			return;
		},
		share_link_copy: function(){
			$(this).after('<input type="text" value="'+$(this).attr('href')+'" id="js_share_link_copy_input">');
			$('#js_share_link_copy_input').select();
			document.execCommand("copy");
			$('#js_share_link_copy_input').remove();
			$(this).text($(this).attr(__('share_link_copy.attr.result')));
			return false;
		},
		share_link_email: function(){
			if($(this).attr('data-action') == 'share_send_mail')
			{
				$.ajax({
					type: "POST",
					data: {
						action: "share_send_mail",
						module: "cart",
						mail: $(_('share_link_email_input')).val(),
						ajax: 1
					},
					dataType: "json",
					url: window.location.href,
					success: function (response, statusText, xhr, form)
					{
						if(response.error)
						{
							$(_('share_link_error')).html(prepare(response.error)).show();
						}
						if(response.success)
						{
							$(_('share_link_email_input')).val('');
						}
					}
				});
			}
			else
			{
				$(_('share_link_email_input')).show('slide');
				var text = $(this).text();
				$(this).text($(this).attr(__('share_link_email.attr.send')));
				$(this).attr(__('share_link_email.attr.send'), text);
				$(this).attr('data-action', 'share_send_mail');
			}
			return false;
		},
		share_link_change: function()
		{
			$(_('share_link_error')).hide();
		}
	};
	var __ = function(name)
	{
		var res = name.split(".");
		var o = self.objects;
		var children = false;
		$.each(res, function(i, k){
			if(children)
			{
				o = children;
			}
			if(typeof o[k] == "object" && typeof o[k].children == "object")
			{
				children = o[k].children;
			}
			else
			{
				children = false;
			}
			o = o[k];
		});
		if(typeof o.name == "string")
		{
			o = o.name;
		}
		return o;
	}
	var _ = function(name)
	{
		var result = '';
		var res = name.split(".");
		var o = self.objects;
		var children = false;
		$.each(res, function(i, k){
			if(typeof o.name == "string")
			{
				if(result)
				{
					result += ' ';
				}
				result += o.name;
			}
			if(children)
			{
				o = children;
			}
			if(typeof o[k] == "object" && typeof o[k].children == "object")
			{
				children = o[k].children;
			}
			else
			{
				children = false;
			}
			o = o[k];
		});
		if(typeof o == "string")
		{
			if(result)
			{
				result += ' ';
			}
			result += o;
		}
		else
		{
			if(result)
			{
				result += ' ';
			}
			result += o.name;
		}
		return result;
	}
	return self;
}

cart_form().init().events();

$(document).on('change', 'form input[type=radio][name=payment_id]', function() {
    var form = $('.js_cart_table_form')
	payment = $('input[name=payment_id]', form);
	if (payment.length) {
		payment.val(this.value);
	} else {
		form.append('<input type="hidden" name="payment_id" value="'+this.value+'">');
	}
	form.submit();
});

$(document).on('keypress', 'input[type="tel"]', function(evt) {
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode( key );
	var regex = /[0-9]|\+/;
	if( !regex.test(key) ) {
		theEvent.returnValue = false;
		if(theEvent.preventDefault) theEvent.preventDefault();
	}
	regex = /^[\+]?[0-9]*$/;
	if( !regex.test($(this).val()+key) ) {
		theEvent.returnValue = false;
		if(theEvent.preventDefault) theEvent.preventDefault();
	}
});
