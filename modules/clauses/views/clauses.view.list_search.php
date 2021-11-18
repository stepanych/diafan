<?php
/**
 * Шаблон списка статей
 * 
 * Шаблон вывода списка статей в том случае, если в настройках модуля отключен параметр «Использовать категории»
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



if(! empty($result['error']))
{
	echo '<p class="_note">'.$result['error'].'</p>';
	return;
}

echo '<section class="block-d block-d_clauses block-d_clauses_item block-d_clauses_item_search">';

//статьи
if(! empty($result['rows']))
{
	$result['search'] = true;

	echo '<div class="block-d__list _list">';
	echo $this->get($result['view_rows'], 'clauses', $result);
	echo '</div>';
}

echo '</section>';
