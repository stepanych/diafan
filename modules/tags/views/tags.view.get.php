<?php
/**
 * Шаблон списка прикрепленных к элементу тегов
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



if (! empty($result))
{
	$k = 0;

	echo
	'<div class="tags-d">
		<span class="tags-d__label">'.$this->diafan->_('Теги').':</span>';
		foreach ($result as $row)
		{
			echo ($k ? ',' : '').' <a class="tag-d" href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>
			';
			$k++;
		}
		echo
	'</div>';
}