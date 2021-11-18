/**
 * JS-сценарий виджета доставки Saferoute
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2020 OOO «Диафан» (http://www.diafan.ru/)
 */

diafan_delivery.service['saferoute'] = {
	config: false,
	widget: false,
	height: 'auto',
	upload: false,
	init: function()
	{
		this.widget = new SafeRouteCartWidget("diafan_delivery_service", this.config);
		this.widget.on("change", this.change);
		this.widget.on("done", this.done);
		this.widget.on("error", this.error);
	},
	
	destruct: function()
	{
		//this.widget.destruct();
	},
	
  done: function (response) {
			$.ajax({
				"type":"POST",
				"data":{
					 "module": "delivery",
					 "backend": "saferoute",
					 "action": "done",
					 "data": response
				},
				"dataType": "JSON",
				 success: function(result){
					$('.js_cart_table_form').submit();
				 }
			});
  },
	
	error: function (errors) {
			$.ajax({
				"type":"POST",
				"data":{
					"module": "delivery",
					"backend": "saferoute",
					"action": "error",
					"data": errors
				},
				"dataType": "JSON",
				success: function(result){
					$('.js_cart_table_form').submit();
				}
			});
	},
	  
	change: function (data) {
		if(data.contacts)
		{
		    if(data.contacts.email)
		    {
					$("input[data-info=email]", ".js_form_order").val(data.contacts.email);
		    }
		    if(data.contacts.phone)
		    {
			$("input[data-info=phone]", ".js_form_order").val(data.contacts.phone);
		    }
		    if(data.contacts.additPhone)
		    {
			$("input[data-info=phone-extra]", ".js_form_order").val(data.contacts.additPhone);
		    }
		    if(data.contacts.fullName)
		    {
			$("input[data-info=name]", ".js_form_order").val(data.contacts.fullName);
		    }
		    if(data.contacts.address.apartment)
		    {
			$("input[data-info=flat]", ".js_form_order").val(data.contacts.address.apartment);
		    }
		    if(data.contacts.address.building)
		    {
			$("input[data-info=building]", ".js_form_order").val(data.contacts.address.building);
		    }
		    if(data.contacts.address.bulk)
		    {
			$("input[data-info=suite]", ".js_form_order").val(data.contacts.address.bulk);
		    }
		    if(data.contacts.address.street)
		    {
			$("input[data-info=street]", ".js_form_order").val(data.contacts.address.street);
		    }
		}
		if(data.city && data.city.name)
		{
		    $("input[data-info=city]", ".js_form_order").val(data.city.name);
		}
		if(data.delivery)
		{
		    $.ajax({
					"type":"POST",
					"data":{
						 "module": "delivery",
						 "backend": "saferoute",
						 "action": "update",
						 "data": data
					},
					"dataType": "JSON",
					success: function(result){
						$('.js_cart_table_form').submit();
					}
				});
		}
	},
};