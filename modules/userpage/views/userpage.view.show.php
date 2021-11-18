<?php
/**
 * Шаблон страницы пользователя
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



echo '<section class="section-d section-d_home section-d_userpage section-d_userpage_home">';

echo
'<div class="account-d account-d_list">
	<div class="account-d__images">';
		if(! empty($result['avatar']))
		{
			echo
			'<figure class="account-d__avatar avatar-d avatar-d_fit">
				<img src="'.BASE_PATH.USERFILES.'/avatar/'.$result["name"].'.png" width="'.$result["avatar_width"].'" height="'.$result["avatar_height"].'" alt="'.$result["fio"].' ('.$result["name"].')">
			</figure>';
		}
		else
		{
			echo
			'<figure class="account-d__avatar avatar-d avatar-d_none">
				<span class="avatar-d__icon icon-d fas fa-user"></span>
			</figure>';
		}
		echo
	'</div>';
	echo
	'<div class="account-d__details details-d">
		<div class="detail-d detail-d_name">
			<span class="account-d__name">'.$result['fio'].'</span>
			<span class="account-d__nikname">('.$result['name'].')</span>
		</div>
		<div class="detail-d detail-d_date">
			<div class="detail-d__name">'.$this->diafan->_('Дата регистрации').'</div>
			<div class="detail-d__content">
				<span class="date-d">'.$result['created'].'</span>
			</div>
		</div>';
		if(isset($result["balance"]))
		{
			echo
			'<div class="detail-d detail-d_balance">
				<div class="detail-d__name">'.$this->diafan->_('Сумма на балансе').'</div>
				<div class="detail-d__content">';
					echo
					'<div class="price-d _block">
						<span class="price-d__num">'.$result['balance']["summ"].'</span>
						<span class="price-d__curr">'.$result['balance']["currency"].'</span>
					</div>';
					if(! empty($result["balance"]["link"]))
					{
						echo
						'<a class="button-d button-d_narrow button-d_dark _mt" href="'.BASE_PATH_HREF.$result["balance"]["link"].'">
							<span class="button-d__name">'.$this->diafan->_('Пополнить баланс').'</span>
						</a>';
					}
					echo
				'</div>
			</div>';
		}
		echo
	'</div>
</div>';

echo '<div class="params-d">';
foreach ($result['param'] as $row)
{
	echo '<div class="param-d">';
	if($row['type'] == 'title')
	{
		echo '<div class="param-d__title">'.$row['name'].'</div>';
		continue;
	}
	if (empty($row['value']))
	{
		if($row['type'] == 'checkbox')
		{
			echo '<strong class="param-d__value">'.$row['name'].'</strong>';
		}
		continue;
	}
	echo '<div class="param-d__name">'.$row['name'].':</div>';
	echo '<strong class="param-d__value">';
	switch ($row['type'])
	{
		case 'select':
			echo $row['value'][0];
			break;
		case 'multiple':
			echo implode(',', $row['value']);
			break;
		case "attachments":
			foreach ($row['value'] as $a)
			{
				if ($a["is_image"])
				{
					if($row["use_animation"])
					{
						echo ' <a href="'.$a["link"].'" data-fancybox="galleryusers"><img src="'.$a["link_preview"].'"></a> <a href="'.$a["link"].'" data-fancybox="galleryusers_link">'.$a["name"].'</a>';
					}
					else
					{
						echo ' <a href="'.$a["link"].'"><img src="'.$a["link_preview"].'"></a> <a href="'.$a["link"].'">'.$a["name"].'</a>';
					}
				}
				else
				{
					echo ' <a href="'.$a["link"].'">'.$a["name"].'</a>';
				}
				echo '<br>';
			}
			break;
		case "images":
			foreach ($row["value"] as $img)
			{
				if($img["source"])
				{
					echo $img["source"];
				}
				else
				{
					echo '<img src="'.$img["src"].'" width="'.$img["width"].'" height="'.$img["height"].'" alt="'.$img["alt"].'" title="'.$img["title"].'">';
				}
			}
			break;
		default:
			echo $row['value'];
	}
	echo '</strong>';
}
echo '</div>';

if (! empty($result['form_messages']))
{
	echo $this->diafan->_tpl->get('form', 'messages', array("to" => $result['id']));
}

echo $this->diafan->_tpl->get('orders', 'userpage', $result);

echo '</section>';
