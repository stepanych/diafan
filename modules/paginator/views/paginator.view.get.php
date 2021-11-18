<?php
/**
 * Шаблон постраничной навигации для пользовательской части
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

if ($result)
{
	echo
	'<div class="paginat-d paginator"'.(! empty($result["more"]) && ! empty($result["more"]["uid"]) ? ' uid="'.$result["more"]["uid"].'"' : '').'>';

		foreach ($result as $l)
		{
			switch($l["type"])
			{
				case "more":
					break;
	
				case "first":
					echo '<a class="paginat-d__item paginat-d__item_first" href="'.BASE_PATH_HREF.$l["link"].'" title="'.$this->diafan->_('В начало', false).'">&#171;</a>';
					break;
	
				case "current":
					echo '<span class="paginat-d__item paginat-d__item_current">'.$l["name"].'</span>';
					break;
	
				case "previous":
					echo
					'<a class="paginat-d__item paginat-d__item_prev button-d button-d_narrow button-d_dark" href="'.BASE_PATH_HREF.$l["link"].'" title="'.$this->diafan->_('На предыдущую страницу', false).'">
						<span class="button-d__icon icon-d fas fa-chevron-circle-left"></span>
						<span class="button-d__name">'.$this->diafan->_('Назад').'</span>
					</a>';
					break;
	
				case "next":
					echo
					'<a class="paginat-d__item paginat-d__item_next button-d button-d_narrow button-d_dark" href="'.BASE_PATH_HREF.$l["link"].'" title="'.$this->diafan->_('На следующую страницу', false).' '.$this->diafan->_('Всего %d', false, $l["nen"]).'">
						<span class="button-d__name">'.$this->diafan->_('Вперёд').'</span>
						<span class="button-d__icon icon-d fas fa-chevron-circle-right"></span>
					</a>';
					break;
	
				case "last":
					echo '<a class="paginat-d__item paginat-d__item_end" href="'.BASE_PATH_HREF.$l["link"].'" title="'.$this->diafan->_('В конец', false).'">&#187;</a>';
					break;
	
				default:
					echo '<a class="paginat-d__item" href="'.BASE_PATH_HREF.$l["link"].'">'.$l["name"].'</a>';
					break;
			}
		}

		echo
	'</div>';
}  