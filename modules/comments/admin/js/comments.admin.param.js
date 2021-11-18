/**
 * Конструктор комментариев, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$('#select_module').change(function() {
	var path = $(this).attr("rel");
	if ($(this).val()) {
		path = path+'?'+$(this).attr("name")+'='+$(this).val();
	}
	window.location.href = path;
});
