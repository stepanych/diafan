<?php
/**
 * Шаблон облака тегов
 *
 * Шаблонный тег <insert name="show_block" module="tags" [template="шаблон"]>:
 * облако тегов
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



if(! $result["rows"]) return false;

echo '<div class="block-d block-d_tags block-d_tags_item">';

if (! $result["title_no_show"])
{
	echo '<header class="block-d_name">'.$this->diafan->_('Теги').'</header>';
}

echo '<div class="block-d__list _list">';
echo $this->get($result["view_rows"], 'tags', $result);
echo '</div>';

echo '</div>';
