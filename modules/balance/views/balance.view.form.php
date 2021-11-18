<?php
/**
 * Шаблон формы пополнения баланса пользователя
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



echo '<p class="_note">'.$this->diafan->_('Сумма на балансе').': '.$result['balance']["summ"].' '.$result['balance']["currency"].'</p>';

if(empty($result["payments"]))
{
	return;
}

echo '<section class="block-d block-d_form block-d_balance">';

echo '<header class="block-d__name">'.$this->diafan->_('Выберите способ пополнения баланса').':</header>';

echo
'<form action="" method="POST" class="balance_form ajax">
	<input type="hidden" name="module" value="balance">
	<input type="hidden" name="form_tag" value="'.$result["form_tag"].'">
	<input type="hidden" name="action" value="recharge">';

	echo '<div class="block-d__list _list payments field-d">';
	echo $this->get('list', 'payment', $result["payments"]);
	echo '</div>';

	echo
	'<div class="field-d _mw300">
		<div class="field-d__name">'.$this->diafan->_('Сумма').':</div>
		<input type="number" min="0" value="0" name="summ" value="0">
	</div>';

	echo
	'<button class="button-d" type="submit">
		<span class="button-d__name">'.$this->diafan->_('Пополнить').'</span>
	</button>';

	echo '<div class="errors error_summ"'.($result["error_summ"] ? '>'.$result["error_summ"] : ' style="display:none">').'</div>';

	echo
'</form>';

echo '</section>';
