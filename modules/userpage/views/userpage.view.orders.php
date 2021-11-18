<?php
/**
 * Шаблон заказов пользователя
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



if (empty($result['orders']))
	return true;

$color = array(
	0 => "red",
	1 => "blue",
	2 => "gray",
	3 => "darkgreen",
	4 => "darkgreen"
);

echo '<section class="block-d block-d_userpage block-d_userpage_orders">';

echo '<header class="block-d__name">'.$this->diafan->_('Ваши заказы').'</header>';

echo
'<div class="table-d table-d_orders table-d_sm table-d_md table-d_lg">';

	echo
	'<div class="table-d__rows table-d__head">
		<div class="table-d__row">
			<div class="cell-d cell-d_num">№</div>
			<div class="cell-d cell-d_date">'.$this->diafan->_('Дата').'</div>
			<div class="cell-d cell-d_details">'.$this->diafan->_('Товары').'</div>
			<div class="cell-d cell-d_price">'.$this->diafan->_('Стоимость').'</div>
			<div class="cell-d cell-d_status">'.$this->diafan->_('Статус').'</div>
			<div class="cell-d cell-d_sum">'.$this->diafan->_('Сумма').'</div>
		</div>
	</div>';

	echo '<div class="table-d__rows table-d__enum">';
	foreach ($result['orders']['rows'] as $order)
	{
		echo
		'<div class="table-d__row">
			<div class="cell-d cell-d_num" title="№">'.$order['id'].'</div>
			<div class="cell-d cell-d_date" title="'.$this->diafan->_('Дата', false).'">'.$order['created'].'</div>
			<div class="cell-d cell-d_details details-d" title="'.$this->diafan->_('Товары', false).'">';
				if(! empty($order['goods']))
				{
					echo '<div class="detail-d detail-d_ordergoods ordergoods-d">';
					foreach ($order['goods'] as $good)
					{
						echo
						'<div class="ordergood-d">
							<a class="ordergood-d__name _block" href="'.BASE_PATH_HREF.$good["link"].'">'.$good["name"];
								if(! empty($good["params"]))
								{
									foreach ($good["params"] as $p)
									{
										echo  ' '.$p["name"].': '.$p["value"];
									}
								}
								echo
							'</a>';
							if(! empty($good["additional_cost"]))
							{
								echo '<div class="ordergood-d__additions">';
								foreach($good["additional_cost"] as $a)
								{
									echo
									'<div class="addition-d">
										<span class="addition-d__name">'.$a["name"].':</span>';
										if($a["summ"])
										{
											echo '
											<span class="addition-d__content"> + 
												<strong class="price-d">
													<span class="price-d__num">'.$a["format_summ"].'</span>
													<span class="price-d__curr">'.$result['orders']["currency"].'</span>
												</strong>
											</span>';
										}
										echo
									'</div>';
								}
								echo '</div>';
							}
							echo
						'</div>';
					}
					echo '</div>';
				}
				echo 
			'</div>
			<div class="cell-d cell-d_price" title="'.$this->diafan->_('Стоимость', false).'">';
				if(! empty($order['goods']))
				{
					foreach ($order['goods'] as $good)
					{
						echo
						'<strong class="price-d _block">
							<span class="price-d__num">'.$good['price'].'</span>
							<span class="price-d_curr">'.$result['orders']['currency'].'</span>
						</strong>';
					}
				}
				echo
			'</div>
			<div class="cell-d cell-d_status" title="'.$this->diafan->_('Статус', false).'">
				<div style="color:'.$color[$order["status"]].';font-weight: bold;">'.$order['status_name'].'</div>';
				if(! empty($order["link_to_pay"]))
				{
					echo
					'<a class="button-d button-d_short button-d_dark" href="'.BASE_PATH_HREF.$order["link_to_pay"].'">
						<span class="button-d__name">'.$this->diafan->_('Оплатить').'</span>
					</a>';
				}
				echo
			'</div>
			<div class="cell-d cell-d_sum" title="'.$this->diafan->_('Сумма', false).'">
				<strong class="price-d">
					<span class="price-d__num">'.$order['summ'].'</span>
					<span class="price-d_curr">'.$result['orders']["currency"].'</span>
				</strong>
			</div>
		</div>';
	}
	echo '</div>';

	echo
	'<div class="table-d__rows table-d__total">';
		echo	
		'<div class="table-d__row">
			<div class="cell-d cell-d_details cell-d_title">'.$this->diafan->_('Итого выполненных заказов на сумму').':</div>
			<div class="cell-d cell-d_sum">
				<strong class="price-d">
					<span class="price-d__num">'.$result['orders']['total'].'</span>
					<span class="price-d_curr">'.$result['orders']["currency"].'</span>
				</strong>
			</div>
		</div>';
		echo
	'</div>';

	echo
'</section>';
