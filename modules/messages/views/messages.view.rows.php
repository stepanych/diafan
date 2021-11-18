<?php
/**
 * Шаблон элементов в списке контактов
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
	echo '<a class="element-d element-d_message element-d_messages element-d_messages_item _tile'.( empty($row['readed']) ? ' _unread' : '').'" href="'.$row['link'].'">';

	if (! empty($row["user"]))
	{
		echo
		'<div class="element-d__images">
			<div class="element-d__account account-d account-d_row">
				<div class="account-d__images">';
					if (! empty($row['user']['avatar']) && strpos($row['user']['avatar'], 'avatar_none') === false)
					{
						echo
						'<object>
							<a class="account-d__avatar avatar-d avatar-d_fit"'.(! empty($row['user']['user_page']) ? ' href="'.$row['user']['user_page'].'"' : '').' title="'.$this->diafan->_('Пользователь', false).'">
								<img src="'.$row['user']['avatar'].'" width="'.$row['user']['avatar_width'].'" height="'.$row['user']['avatar_height'].'" alt="'.$row['user']['fio'].' ('.$row['user']['name'].')">
							</a>
						</object>';
					}
					else
					{
						echo
						'<div class="account-d__avatar avatar-d avatar-d_none" title="'.$row['user']["fio"].' ('.$row['user']["name"].')">
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
						if(! empty($row['user']['user_page']))
						{
							echo
							'<object>
								<a href="'.$row['user']['user_page'].'">'.$row['user']['fio'].' ('.$row['user']['name'].')</a>
							</object>';
						}
						echo
					'</div>
				</div>
			</div>
		</div>';
		echo
		'<div class="detail-d detail-d_date" title="'.$this->diafan->_('Дата', false).'">
			<div class="element-d__date date-d">'.$row['last_message']['created'].'</div>
		</div>';
		if(! empty($row['last_message']['text']))
		{
			echo
			'<div class="detail-d detail-d_text _text" title="'.$this->diafan->_('Текст сообщения', false).'">
				<object>'.$row['last_message']['text'].'</object>
			</div>';
		}
		echo
	'</div>';

	echo '</a>';
}

//Кнопка "Показать ещё"
if(! empty($result["show_more"]))
{
	echo $result["show_more"];
}
