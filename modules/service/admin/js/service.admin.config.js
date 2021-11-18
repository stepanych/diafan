/**
 * Настройки модуля, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$('.btn_convert').on('click', function(event) {
	var url = $('input[name="express_another_file_path"]').val();
	url = encodeURIComponent(url);
	$('#result_convert').text(url);
	$('#paramhelp_another').removeClass('hide');
});
