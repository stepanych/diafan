<?php
/**
 * Шаблон формы поиска по сайту
 *
 * Шаблонный тег <insert name="show_search" module="search"
 * [button="надпись на кнопке"] [template="шаблон"]>:
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

echo '<section class="search-d'.($result['ajax'] ? ' ajax' : '').'">';
echo
'<form action="'.$result["action"].'" class="search-d__form search_form'.($result["ajax"] ? ' ajax" method="post"' : '" method="get"').'>
	<input type="hidden" name="module" value="search">
	<div class="search-d__field field-d">
		<input type="text" name="searchword" value="'.($result["value"] ? $result["value"] : '').'" placeholder="'.$this->diafan->_('Поиск по сайту', false).'">
	</div>
	<button class="button-d" type="submit">
		<span class="button-d__name">'.$result["button"].'</span>
	</button>
</form>';
if($result["ajax"])
{
	echo '<section class="search-d__result search_result js_search_result"></section>';
}
echo '</section>';
