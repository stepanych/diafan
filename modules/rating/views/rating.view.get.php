<?php
/**
 * Шаблон рейтинга элемента
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



//rating-d_fa — значки шрифта «Font Awesome»
//rating-d_img — значки-изображения

echo
'<form class="rating-d rating-d_fa js_rating_form ajax" action="" method="POST">
	<input type="hidden" name="module" value="rating">
	<input type="hidden" name="action" value="add">
	<input type="hidden" name="module_name" value="'.$result["module_name"].'">
	<input type="hidden" name="element_type" value="'.$result["element_type"].'">
	<input type="hidden" name="element_id" value="'.$result["element_id"].'">
	<fieldset'.($result["disabled"] ? ' disabled="disabled"' : '').'>
		<legend>'.$this->diafan->_('Рейтинг').'</legend>';

		$hash = $result["module_name"].'_'.$result["element_type"].'_'.$result["element_id"];

		for ($k = 0; $k < 5; $k++)
		{
			echo
			'<input type="radio" name="rating" value="'.($k + 1).'" id="'.$hash.'_'.$k.'"'.( $result["rating"] == $k + 1 ? ' checked' : '').'>
			<label for="'.$hash.'_'.$k.'"></label>';
		}

		echo
	'</fieldset>';
	if($result["full"] && $result["count_votes"])
	{
		echo
		'<div class="rating-d__details details-d">
			<div class="rating-d__full detail-d">'.$this->diafan->_('Общий рейтинг').': '.$result["average_rating"].'</div>
			<div class="rating-d__count detail-d">'.$this->diafan->_('Проголосовало').': '.$result["count_votes"].'</div>
		</div>';
	}
	echo
	'<div class="errors error"'.(!empty($result["error"]) ? '>'.$result["error"] : ' style="display:none">').'</div>
</form>';
