<?php
/**
 * Шаблон блока комментариев
 * 
 * Шаблонный тег <insert name="show_block" module="comments" [count="количество"]
 * [element_id="элементы"] [modules="модули"]
 * [sort="порядок_вывода"] [template="шаблон"]>:
 * блок комментариев
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

//комментарии
foreach ($result['rows'] as $row)
{
	echo '<article class="element-d element-d_message element-d_comments element-d_comments_item _tile">';

	echo
	'<div class="element-d__images">
		<div class="element-d__account account-d account-d_row">
			<div class="account-d__images">';
				if (! empty($row['name']))
				{
					if(is_array($row['name']))
					{
						if (! empty($row['name']['avatar']) && strpos($row['name']['avatar'], 'avatar_none') === false)
						{
							echo
							'<a class="account-d__avatar avatar-d avatar-d_fit"'.(! empty($row['name']['user_page']) ? ' href="'.$row['name']['user_page'].'"' : '').' title="'.$this->diafan->_('Пользователь', false).'">
								<img src="'.$row['name']['avatar'].'" width="'.$row['name']['avatar_width'].'" height="'.$row['name']['avatar_height'].'" alt="'.$row['name']['fio'].' ('.$row['name']['name'].')">
							</a>';
						}
						else
						{
							echo
							'<a class="account-d__avatar avatar-d avatar-d_none"'.(! empty($row['name']['user_page']) ? ' href="'.$row['name']['user_page'].'"' : '').' title="'.$this->diafan->_('Пользователь', false).'">
								<span class="avatar-d__icon icon-d fas fa-user"></span>
							</a>';
						}
					}
				}
				else
				{
					echo
					'<a class="account-d__avatar avatar-d avatar-d_none" title="'.$this->diafan->_('Пользователь', false).'">
						<span class="avatar-d__icon icon-d fas fa-user"></span>
					</a>';
				}
				echo
			'</div>
		</div>
	</div>';

	echo '<div class="element-d__details details-d">';

	echo
	'<div class="detail-d detail-d_account">
		<div class="element-d__account account-d account-d_row">
			<div class="account-d__details details-d">
				<div class="detail-d detail-d_name">';
					if (! empty($row['name']))
					{
						if(is_array($row['name']))
						{
							echo '<a'.(! empty($row['name']['user_page']) ? ' href="'.$row['name']['user_page'].'"' : '').' title="'.$this->diafan->_('Пользователь', false).'">';
							if(array_key_exists('fio', $row['name']))
							{
								echo '<span class="account-d__name">'.$row['name']['fio'].'</span>';
							}
							if(array_key_exists('name', $row['name']))
							{
								echo ' <span class="account-d__nikname">('.$row['name']['name'].')</span>';
							}
							echo '</a>';
						}
						else
						{
							echo '<a title="'.$this->diafan->_('Пользователь', false).'">'.$row['name'].'</a>';
						}
					}
					else
					{
						echo '<a title="'.$this->diafan->_('Пользователь', false).'">'.$this->diafan->_('Гость').'</a>';
					}
					echo
				'</div>
			</div>
		</div>
	</div>';

	echo
	'<div class="detail-d detail-d_date" title="'.$this->diafan->_('Дата публикации', false).'">
		<div class="date-d element-d__date">'.$row['date'].'</div>
	</div>';

	if(! empty($row['params']))
	{
		echo '<div class="detail-d detail-d_params">';
		foreach ($row['params'] as $param)
		{
			echo
			'<div class="param-d comments_param'.($param['type'] == 'title' ? '_title' : '').'">
				<span class="param-d__name">'.$param['name'].':</span>';

				if (! empty($param["value"]))
				{
					echo '
					<span class="param-d__value comments_param_value">';
						if($param["type"] == "attachments")
						{
							foreach ($param["value"] as $a)
							{
								if ($a["is_image"])
								{
									if($param["use_animation"])
									{
										echo
										'<a href="'.$a["link"].'" data-fancybox="gallery'.$row["id"].'comments">
											<img src="'.$a["link_preview"].'">
										</a>
										<a href="'.$a["link"].'" data-fancybox="gallery'.$row["id"].'comments_link]">'.$a["name"].'</a>';
									}
									else
									{
										echo
										'<a href="'.$a["link"].'">
											<img src="'.$a["link_preview"].'">
										</a>
										<a href="'.$a["link"].'">'.$a["name"].'</a>';
									}
								}
								else
								{
									echo '<a href="'.$a["link"].'">'.$a["name"].'</a>';
								}
							}
						}
						elseif($param["type"] == "images")
						{
							foreach ($param["value"] as $img)
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
						}
						elseif (is_array($param["value"]))
						{
							foreach ($param["value"] as $p)
							{
								if ($param["value"][0] != $p)
								{
									echo  ', ';
								}
								if (is_array($p))
								{
									echo  $p["name"];
								}
								else
								{
									echo  $p;
								}
							}
						}
						else
						{
							echo  $param["value"];
						}
						echo
					'</span>';
				}
				echo
			'</div>';
		}
		echo '</div>';
	}

	if(! empty($row['text']))
	{
		echo '<div class="detail-d detail-d_text _text" title="'.$this->diafan->_('Текст сообщения', false).'">'.$row['text'].'</div>';
	}

	echo
	'<div class="detail-d detail-d_theme" title="'.$this->diafan->_('Тема', false).'">
		<strong>'.$this->diafan->_('Тема').':</strong> <a href="'.BASE_PATH_HREF.$row['link'].'#comment'.$row["id"].'">'.$row["theme_name"].'</a>
	</div>';

	echo  '</div>';

	echo '</article>';
}

