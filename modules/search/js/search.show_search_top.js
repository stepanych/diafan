/**
 * JS-сценарий формы поиска
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */



$(document).on('click', function (event) {
	if($(event.target).parents("._search-result-show, .js_search_result, .search_result").length) {
		return true;
	}
	$('body').removeClass('_toolbar-search-active _hboard-search-active');
	$('._search-result-show').removeClass('_search-result-show');

	//Прежнее
	//$(".js_search_result, .search_result").fadeOut("slow");
});

$(".js_search_form input[type=text], #search input[type=text]").keyup(function() {
	if($(this).val()) {
		$(this).parents('.js_search_form, #search').addClass('active');
	} else {
		$(this).parents('.js_search_form, #search').removeClass('active');
	}
});

$('.toolbar-d__search .search-d__shield').on('click', function (e) {
	e.preventDefault(); e.stopPropagation();
	$('body').toggleClass('_toolbar-search-active');
	$(this).parent().addClass('_search-result-show');
});

$('.hboard-d__search .search-d__shield').on('click', function (e) {
	e.preventDefault(); e.stopPropagation();
	$('body').toggleClass('_hboard-search-active');
	$(this).parent().addClass('_search-result-show');
});

$(document).on('submit', '.js_search_form.ajax', function (e) {
	$(this).parent().addClass('_search-result-show');
});
