<?php
/**
 * Шаблон одного отзыва
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



echo '<article class="element-d element-d_message element-d_reviews element-d_reviews_item _bounded">';

if (! empty($result["name"]))
{
	echo
	'<div class="element-d__images">
		<a name="comment'.$result["id"].'"></a>
		<div class="element-d__account account-d account-d_row">
			<div class="account-d__images">';
				if (! empty($result['name']['avatar']) && strpos($result['name']['avatar'], 'avatar_none') === false)
				{
					echo
					'<a class="account-d__avatar avatar-d avatar-d_fit"'.(! empty($result['name']['user_page']) ? ' href="'.$result['name']['user_page'].'"' : '').' title="'.$this->diafan->_('Пользователь', false).'">
						<img src="'.$result['name']['avatar'].'" width="'.$result['name']['avatar_width'].'" height="'.$result['name']['avatar_height'].'" alt="'.$result['name']['fio'].' ('.$result['name']['name'].')">
					</a>';
				}
				else
				{
					echo
					'<figure class="account-d__avatar avatar-d avatar-d_none">
						<span class="avatar-d__icon icon-d fas fa-user"></span>
					</figure>';
				}
				echo
			'</div>
		</div>
	</div>';
}

echo '<div class="element-d__details details-d">';

if (! empty($result['name']))
{
	echo
	'<div class="detail-d detail-d_account">
		<div class="account-d account-d_row element-d__account">
			<div class="account-d__details details-d">
				<div class="detail-d detail-d_name">';
					if(is_array($result['name']))
					{
						echo '<a'.(! empty($result['name']['user_page']) ? ' href="'.$result['name']['user_page'].'"' : '').' title="'.$this->diafan->_('Автор', false).'">';
						if(array_key_exists('fio', $result['name']))
						{
							echo '<span class="account-d__name">'.$result['name']['fio'].'</span>';
						}
						if(array_key_exists('name', $result['name']))
						{
							echo ' <span class="account-d__nikname">('.$result['name']['name'].')</span>';
						}
						echo '</a>';
					}
					else
					{
						echo '<a title="'.$this->diafan->_('Автор', false).'">'.$result['name'].'</a>';
					}
					echo
				'</div>
			</div>
		</div>
	</div>';
}

if ($result['date'])
{
	echo
	'<div class="detail-d detail-d_date" title="'.$this->diafan->_('Дата публикации', false).'">
		<div class="element-d__date date-d">'.$result['date'].'</div>
	</div>';
}

if(! empty($result["params"]))
{
	echo '<div class="detail-d detail-d_params">';
	foreach ($result["params"] as $param)
	{
		echo
		'<div class="param-d reviews_param'.($param["type"] == 'title' ? '_title' : '').'">
			<span class="param-d__name">'.$param['name'].':</span>';
			if (! empty($param["value"]))
			{
				echo '
				<span class="param-d__value reviews_param_value">';
				switch($param["type"])
				{
					case "attachments":
						foreach ($param["value"] as $a)
						{
							if ($a["is_image"])
							{
								if($param["use_animation"])
								{
									echo ' <a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'reviews"><img src="'.$a["link_preview"].'"></a> <a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'reviews_link">'.$a["name"].'</a>';
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
						}
						break;

					case "images":
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
						break;

					case 'url':
						echo '<a href="'.$param["value"].'">'.$param["value"].'</a>';
						break;

					case 'email':
						echo '<a href="mailto:'.$param["value"].'">'.$param["value"].'</a>';
						break;

					default:
						if (is_array($param["value"]))
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
							echo $param["value"];
						}
						break;
				}
				echo '</span>';
			}
			echo
		'</div>';
	}
	echo '</div>';
}

if(! empty($result['text']))
{
	echo '<div class="detail-d detail-d_text _text" title="'.$this->diafan->_('Текст сообщения', false).'">'.$result['text'].'</div>';
}

if(! empty($result["theme_name"]))
{
	echo
	'<div class="detail-d detail-d_theme" title="'.$this->diafan->_('Тема', false).'">
		<strong>'.$this->diafan->_('Тема').':</strong> <a href="'.BASE_PATH_HREF.$result['link'].'#review'.$result["id"].'">'.$result["theme_name"].'</a>
	</div>';
}

echo '</div>';

echo '</article>';
