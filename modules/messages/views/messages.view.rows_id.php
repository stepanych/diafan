<?php
/**
 * Шаблон переписки с пользователем
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



if(empty($result['rows'])) return false;

foreach ($result['rows'] as $row)
{
	echo '<article class="element-d element-d_message element-d_messages element-d_messages_item _bounded'.( empty($row['readed']) ? ' _unread' : '').'">';

	if (! empty($row["name"]))
	{
		echo
		'<div class="element-d__images">
			<div class="element-d__account account-d account-d_row">
				<div class="account-d__images">';
					if (! empty($row['name']['avatar']) && strpos($row['name']['avatar'], 'avatar_none') === false)
					{
						echo
						'<object>
							<a class="account-d__avatar avatar-d avatar-d_fit"'.(! empty($row['name']['user_page']) ? ' href="'.$row['name']['user_page'].'"' : '').' title="'.$this->diafan->_('Пользователь', false).'">
								<img src="'.$row['name']['avatar'].'" width="'.$row['name']['avatar_width'].'" height="'.$row['name']['avatar_height'].'" alt="'.$row['name']['fio'].' ('.$row['name']['name'].')">
							</a>
						</object>';
					}
					else
					{
						echo
						'<div class="account-d__avatar avatar-d avatar-d_none" title="'.$row['name']["fio"].' ('.$row['name']["name"].')">
							<span class="avatar-d__icon icon-d fas fa-user"></span>
						</div>';
					}
					echo
				'</div>
			</div>
		</div>';
	}

	echo
	'<div class="element-d__details details-d">
		<div class="detail-d detail-d_account">
			<div class="element-d__account account-d account-d_row">
				<div class="account-d__details details-d">
					<div class="detail-d detail-d_name">';
						if(! empty($row['name']['user_page']))
						{
							echo '<a href="'.$row['name']['user_page'].'">'.$row['name']['fio'].' ('.$row['name']['name'].')</a>';
						}
						echo
					'</div>
				</div>
			</div>
		</div>';
		echo
		'<div class="detail-d detail-d_date" title="'.$this->diafan->_('Дата', false).'">
			<div class="element-d__date date-d">'.$row['created'].'</div>
		</div>';
		if(! empty($row['text']))
		{
			echo '<div class="detail-d detail-d_text _text" title="'.$this->diafan->_('Текст сообщения', false).'">'.$row['text'].'</div>';
		}
		echo
	'</div>';

	echo '</article>';
}

//Кнопка "Показать ещё"
if(! empty($result["show_more"]))
{
	echo $result["show_more"];
}
