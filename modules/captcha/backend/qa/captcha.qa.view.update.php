<?php
/**
 * Шаблон формы для капчи «Вопрос-Ответ»
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
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



$codeint = rand(1111, 9999);

echo
'<div class="captcha-d">
	<div class="captcha-d__name field-d__name">'.$this->diafan->_('Выберите правильный ответ').'</div>';

	echo '<div class="captcha-d__question _text">'.$result["text"].'</div>';

	echo '<div class="field-d">';
	if($result["answers"])
	{
		echo '<div class="field-d__list">';
		foreach ($result["answers"] as $row)
		{
			$rand = rand(0, 999);
			echo
			'<div class="field-d__item">
				<input name="captcha_answer_id" type="radio" value="'.$row["id"].'" id="captcha_radio'.$row["id"].'_'.$rand.'"'.($row == $result["answers"][0] ? " checked" : '').'>
				<label for="captcha_radio'.$row["id"].'_'.$rand.'">'.$row["text"].'</label>
			</div>';
		}
		echo '</div>';
	}
	else
	{
		echo '<input name="captcha_answer" type="text" value="">';
	}
	echo '</div>';

	echo
	'<input type="hidden" name="captcha_update" value="">
	<span class="button-d button-d_short js_captcha_update">
		<span class="button-d__name">'.$this->diafan->_('Обновить вопрос').'</span>
	</span>
	<div class="errors error_captcha"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>
</div>';
