/**
 * JS-сценарий меню
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */



$('.toolbar-d__nav .nav-d__burger').on('click', function (e) {
	e.preventDefault();
	$('body').toggleClass('_toolbar-nav-open');
});