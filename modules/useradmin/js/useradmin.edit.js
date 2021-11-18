/**
 * Форма редактирования контента из пользовательской части, JS-сценарий
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$(document).on('submit', '.useradmin_form', function(){
	$(this).ajaxSubmit({
		dataType: 'json',
		success: function()
		{
			window.parent.location.href = window.parent.location.href + '?ua=1';
		}
	});
	return false;
});