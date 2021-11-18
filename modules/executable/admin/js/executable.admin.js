/**
 * История фоновых процессов, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$(document).on('click', '.trash_clear', function(){
	if (confirm($(this).attr('confirm'))) {
		$(this).parent('form').submit();
	}
});
