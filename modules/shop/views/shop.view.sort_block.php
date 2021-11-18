<?php
/**
 * Шаблон блока «Сортировать» с ссылками на направление сортировки
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



$link_sort   = $result["link_sort"];
$sort_config = $result['sort_config'];

echo
'<div class="settings-d">';

	echo 
	'<div class="setting-d setting-d_sort">
		<ul class="setting-d__list">';
			foreach($sort_config['sort_fields_names'] as $key => $name)
			{
				$asc  = isset($link_sort[$key])     ? $link_sort[$key]     : '';
				$desc = isset($link_sort[$key + 1]) ? $link_sort[$key + 1] : '';

				echo
				'<li class="setting-d__item">
					<span class="setting-d__name">'.$name.':</span>
					<span class="setting-d__directions">';
						echo	
						'<a class="setting-d__direction setting-d__direction_asc'.(! $asc ? ' _active' : '').'" href="'.BASE_PATH_HREF.$asc.'" title="'.$this->diafan->_('По убыванию', false).'">
							<span class="setting-d__icon icon-d fas fa-arrow-circle-down"></span>
						</a>';
						echo
						'<a class="setting-d__direction setting-d__direction_desc'.(! $desc ? ' _active' : '').'" href="'.BASE_PATH_HREF.$desc.'" title="'.$this->diafan->_('По возрастанию', false).'">
							<span class="setting-d__icon icon-d fas fa-arrow-circle-up"></span>
						</a>';
						echo
					'</span>
				</li>';
			}
			echo
		'</ul>
	</div>';

	$view = 'grid';
	if(! empty($_COOKIE['_diafan_shop_view']))
	{
		switch($_COOKIE['_diafan_shop_view'])
		{
			case 'rows':
				$view = 'rows';
			break;
		}
	}

	echo 
	'<div class="setting-d setting-d_views">
		<ul class="setting-d__list">
			<li class="setting-d__item">
				<span class="setting-d__name">'.$this->diafan->_('Вид').': </span>
				<span class="setting-d__views">';
					echo
					'<a class="setting-d__view setting-d__view_grid'.($view == 'grid' ? ' _active' : '').' js_shop_setting_view" title="'.$this->diafan->_('Сетка', false).'" data-view="grid">
						<span class="setting-d__icon icon-d fas fa-th"></span>
					</a>';
					echo
					'<a class="setting-d__view setting-d__view_rows'.($view == 'rows' ? ' _active' : '').' js_shop_setting_view" title="'.$this->diafan->_('Ряд', false).'" data-view="rows">
						<span class="setting-d__icon icon-d fas fa-th-list"></span>
					</a>';
					echo
				'</span>
			</li>
		</ul>
	</div>';

	echo
'</div>';
