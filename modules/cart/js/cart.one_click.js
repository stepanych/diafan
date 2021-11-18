/**
 * JS-сценарий модуля
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */



$(document).on('click', '.oneclick-d__close', function (e) {
    e.preventDefault();
    $(this).closest('.oneclick-d').hide();
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
