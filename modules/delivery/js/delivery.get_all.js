/**
 * JS-сценарий модуля
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2020 OOO «Диафан» (http://www.diafan.ru/)
 */

var diafan_delivery = {
	service: {},
	name: false,
	interval: false,
	config_string: false,
	
	init: function(name, config)
	{
		var config_string = JSON.stringify(config);
		
		if(diafan_delivery.name == name
			 && diafan_delivery.service[name]
			 && diafan_delivery.config_string == config_string)
		{
			diafan_delivery.place();
			return;
		}
		
		diafan_delivery.destruct();

		if (typeof(diafan_delivery.service[name]) === "undefined")
		{
			return false;
		}

		diafan_delivery.name = name;
		diafan_delivery.service[name].config = config;
		diafan_delivery.config_string = config_string;

		$('body').append('<div id="diafan_delivery_service"></div>');

		diafan_delivery.service[name].init();
		
		diafan_delivery.place();
		
		if(diafan_delivery.service[name].height)
		{
			if(diafan_delivery.service[name].height == 'auto')
			{
				diafan_delivery.interval = setInterval(function(){
					$('#delivery_' + diafan_delivery.name + '_place').height($('#diafan_delivery_service').height());
				}, 200);
			}
			else
			{
					$('#delivery_' + diafan_delivery.name + '_place').height(diafan_delivery.service[name].height);
			}
		}
	},
	
	place: function(){
		$('#diafan_delivery_service').css('position', 'absolute');
		$('#delivery_' + diafan_delivery.name + '_place').css('min-width', '100%');
		$('#diafan_delivery_service').css('width', $('#delivery_' + diafan_delivery.name + '_place').width());
		$('#diafan_delivery_service').css($('#delivery_' + diafan_delivery.name + '_place').offset());
		$('#delivery_' + diafan_delivery.name + '_place').height($('#diafan_delivery_service').height());
	},
	
	destruct: function()
	{
		if(diafan_delivery.name && diafan_delivery.service[diafan_delivery.name])
		{
			diafan_delivery.service[diafan_delivery.name].destruct();
			clearInterval(diafan_delivery.interval);
			diafan_delivery.config_string = '';
			$('#diafan_delivery_service').remove();
		}
	}
}

$('input[name=delivery_id]').each(function(){
	var service = $(this).data('service');
	if(! service)
	{
		return;
	}

	if(typeof(diafan_delivery_config) !== "undefined"
		 && diafan_delivery_config[service])
	{
			var tmp_interval=setInterval(function(){
				if (typeof diafan_delivery.service[service] === "undefined") return;
				clearInterval(tmp_interval);
				diafan_delivery.init(service, diafan_delivery_config[service]);
			}, 300);
	}
	
	document.addEventListener("delivery_"+service+"_ready", function(){
		diafan_delivery.init(service, diafan_delivery_config[service]);
	});
});

$(document).on('click change', 'input[name=delivery_id]', function(){
	diafan_delivery.destruct();
});
