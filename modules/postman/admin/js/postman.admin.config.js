/**
 * Настройки модуля, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$(document).on('click', '#js_smtp_check', function(){
	$('#js_smtp_check').next('.ok,.error').remove();
	diafan_ajax.init({
		data:{
			action: "smtp_check",
			module: 'postman',
			smtp_host: $('input[name=smtp_host').val(),
			smtp_login: $('input[name=smtp_login').val(),
			smtp_password: $('input[name=smtp_password').val(),
			smtp_port: $('input[name=smtp_port').val(),
		},
		success: function(response) {
			if (response.error) {
				$('#js_smtp_check').after('<div class="error">'+prepare(response.error)+'</div>');
			}
			if (response.data) {
				$('#js_smtp_check').after('<div class="ok">'+prepare(response.data)+'</div>');
			}
			if (response.hash) {
				$('input[name=check_hash_user]').val(response.hash);
				$('.check_hash_user').text(response.hash);
			}
		}
	});
	return false;
});