/**
 * JS-сценарий модуля
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */



$(document).on('click', '.js_shop_setting_view', function (e) {
	e.preventDefault();
	
	var $setting = $(this);

	var view = $setting.data('view');
	if (!view) return;

	var del_view = '';
	var add_view = '';

	switch (view)
	{
		case 'rows':
			del_view = '_viewgrid';
			add_view = '_viewrows';
			setcookie_view('rows');
			break;
		case 'grid':
		default:
			del_view = '_viewrows';
			add_view = '_viewgrid';
			setcookie_view('grid');
			break;
	}

	$('.section-d__list.' + del_view).removeClass(del_view).addClass(add_view);

	$('.js_shop_setting_view._active').removeClass('_active');
	$('.js_shop_setting_view[data-view=' + view).addClass('_active');
});
function setcookie_view(view)
{
	document.cookie = '_diafan_shop_view=' + encodeURIComponent(view) + '; path=/; max-age=7200';
}
function getcookie_view()
{
	var view = document.cookie.match(new RegExp("(?:^|; )_diafan_shop_view=([^;]*)"));
	return view ? decodeURIComponent(view[1]) : undefined;
}
function delcookie_view() {
	document.cookie = '_diafan_shop_view=; path=/; max-age=0';
}
