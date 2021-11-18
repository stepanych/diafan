<?php
/**
 * Шаблон формы поиска по сайту, template=top
 *
 * Шаблонный тег <insert name="show_search" module="search" template="top"
 * [button="надпись на кнопке"]>:
 * форма поиска по сайту
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

if (! defined('DIAFAN'))
{
    $path = __FILE__;
	while(! file_exists($path.'/includes/404.php'))
	{
		$parent = dirname($path);
		if($parent == $path) exit;
		$path = $parent;
	}
	include $path.'/includes/404.php';
}



echo
'<div class="search-d'.($result['ajax'] ? ' ajax' : '').'">
	<button class="search-d__shield shield-d" type="button" title="'.$this->diafan->_('Что ищем?', false).'">
		<span class="shield-d__icon icon-d fas fa-search"></span>
		<span class="shield-d__name">'.$this->diafan->_('Поиск').'</span>
	</button>
	<form class="search-d__form js_search_form search_form'.($result['ajax'] ? ' ajax" method="post"' : '" method="get"').' action="'.$result['action'].'">
		<input type="hidden" name="module" value="search">
		<div class="search-d__field field-d">
			<input type="text" name="searchword" placeholder="'.$this->diafan->_('Что ищем?', false).'">
		</div>
		<button class="search-d__button button-d" type="submit" title="'.$this->diafan->_('Найти', false).'">
			<span class="button-d__icon icon-d fas fa-search"></span>
		</button>
	</form>';
	if($result['ajax'])
	{
		echo '<section class="search-d__result search_result js_search_result _scroll"></section>';
	}
	echo
'</div>';