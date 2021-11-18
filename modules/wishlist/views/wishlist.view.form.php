<?php
/**
 * Шаблон формы редактирования списка желаний
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

if (! defined('DIAFAN')) {
    $path = __FILE__;
	while(! file_exists($path.'/includes/404.php'))
	{
		$parent = dirname($path);
		if($parent == $path) exit;
		$path = $parent;
	}
	include $path.'/includes/404.php';
}



if (empty($result["rows"]))
{
	echo '<p class="_note">'.$this->diafan->_('Список отложенных товаров пуст.').'</p>';
	return;
}

echo '<section class="section-d section-d_home section-d_wishlist section-d_wishlist_home">';

echo
'<form action="" method="POST" enctype="multipart/form-data" class="js_wishlist_form wishlist_form ajax">
	<input type="hidden" name="module" value="wishlist">
	<input type="hidden" name="action" value="recalc">';

	//вывод таблицы с товарами
	echo '<div class="wishlist_table">';
	echo $this->get('table', 'wishlist', $result);
	echo '</div>';
	echo '<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>';

	// кнопка пересчитать
	echo '<div class="wishlist_recalc"><input type="submit" value="'.$this->diafan->_('Пересчитать', false).'"></div>';
echo '</form>';

echo '</section>';
