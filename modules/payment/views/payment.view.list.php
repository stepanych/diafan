<?php
/**
 * Шаблон списка платежных система при оплате
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

foreach ($result as $i => $row)
{
	echo
	'<label class="paymethod-d" for="payment'.$row['id'].'">
		<div class="paymethod-d__select field-d">
			<input name="payment_id" id="payment'.$row['id'].'" value="'.$row['id'].'" type="radio"'.(! empty($row['selected']) ? ' checked' : '').'>
			<label for="payment'.$row['id'].'"></label>
		</div>
		<div class="paymethod-d__details details-d">
			<div class="paymethod-d__name detail-d detail-d_name">'.$row['name'].'</div>';
			if(! empty($row['text']))
			{
				echo '<div class="detail-d detail-d_desc _text">'.$row['text'].'</div>';
			}
			if(! empty($row["discount_total"]))
			{
				echo '<div class="detail-d detail-d_discount _text">'.$this->diafan->_('Скидка на сумму заказа').' '.$row["discount_total"]["discount_summ"].' '.$row["discount_total"]["currency"].'</div>';
			}
			echo
		'</div>
	</label>';
}
