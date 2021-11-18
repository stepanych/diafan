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
	if(! empty($result["more"]))
	{
		echo
		'<form method="POST" action="'.BASE_PATH_HREF.$result["more"]["link"].'" class="showmore-d paginator_more_form js_paginator_more_form ajax">
			<input type="hidden" name="more" value="1">
			<input type="hidden" name="uid" value="'.$result["more"]["uid"].'">
			<input type="hidden" name="mode" value="'.$result["more"]["mode"].'">
			<input type="hidden" name="module" value="'.$result["more"]["module"].'">
			<input type="hidden" name="action" value="'.(! empty($result["more"]["action"]) ? $result["more"]["action"] : '').'">';
			if(! empty($result["more"]["attributes"]))
			{
				foreach($result["more"]["attributes"] as $key => $value)
				{
					echo '<input type="hidden" name="attributes['.$key.']" value="'.$value.'">';
				}
			}
			echo
			'<button class="button-d paginator_more_button js_paginator_more_button">
				<span class="button-d__icon icon-d fas fa-redo"></span>
				<span class="button-d__name">'.$result["more"]["name"].'</span>
			</button>';
			echo
		'</form>';
	}
}
