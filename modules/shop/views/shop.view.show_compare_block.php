<?php
/**
 * Шаблон кнопки «Сравнить» для товаров
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



if(empty($result))
{
	return;
}

if(! empty($result['ajax']))
{
	if(! empty($result['count']))
	{
		echo $result['count'];
	}
}
else
{
	echo
	'<div class="intercap-d intercap-d_compare">
		<a class="tip-d intercap-d__tip" href="'.$result['link'].'">
			<span class="icon-d tip-d__icon fas fa-balance-scale"></span>
			<span class="amount-d amount-d_stick tip-d__amount">
				<strong class="amount-d__num js_show_compare">';
					if(! empty($result['count']))
					{
						echo $result['count'];
					}
					echo
				'</strong>
			</span>
		</a>
	</div>';
}
