/**
 * Редактирование файлов, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$(document).on('change', 'input.radio_tab', function () {
	var name = $(this).attr("name");
	$("."+name).hide();
	$("input.radio_tab:radio:checked").filter("[name="+name+"]").each(function() {
		$("."+name+"[for="+$(this).attr("id")+"]").show();
	});
});

$(document).on('click', ".overdue-form .js-pay-period", function(event) {
	event = event || window.event;
	event.preventDefault ? event.preventDefault() : (event.returnValue=false);
	var target = event.target || event.srcElement;

	$(".overdue-form .js-pay-value").val($(this).data("value"));
	$(".overdue-form  input[name=period]").val($(this).data("period"));
});

$('.tabs a.tabs__item[href *= "account/projects"]').attr({"href":"http://pro.user.diafan.ru/", "target":"_blank"});
