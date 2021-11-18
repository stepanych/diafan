<?php
/**
 * Шаблон формы стандартной капчи
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
	<input type="text" name="cfio" value="" style="display:none">
	<input type="hidden" name="captchapin" value="'.(time() - mktime(0,0, 0)).'">
	<input type="hidden" name="captchaint" value="'.$codeint.'">
	<input type="hidden" name="captcha_update" value="">
	<img src="'.BASE_PATH.(IS_ADMIN ? ADMIN_FOLDER.'/' : '').'captcha/get/kcaptcha/'.$result["modules"].$codeint.'" width="159" height="80" class="captcha-d__image">
	<span class="button-d button-d_short js_captcha_update">
		<span class="button-d__icon icon-d fas fa-redo"></span>
	</span>
	<div class="field-d">
		<div class="field-d__name">'.$this->diafan->_('Введите код с картинки').':</div>
		<input type="text" name="captcha" value="" autocomplete="off">
	</div>
</div>
<div class="errors error_captcha"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>';
