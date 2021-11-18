<?php
/**
 * Шаблон одного комментария
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



echo '<article class="element-d element-d_message element-d_comments element-d_comments_item _bounded">';

echo
'<div class="element-d__images">
	<a name="comment'.$result["id"].'"></a>
	<div class="account-d account-d_row element-d__account">
		<div class="account-d__images">';
			if (! empty($result["name"]))
			{
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
					'<a class="account-d__avatar avatar-d avatar-d_none"'.(! empty($result['name']['user_page']) ? ' href="'.$result['name']['user_page'].'"' : '').' title="'.$this->diafan->_('Пользователь', false).'">
						<span class="avatar-d__icon icon-d fas fa-user"></span>
					</a>';
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
	<div class="account-d account-d_row element-d__account">
		<div class="account-d__details details-d">
			<div class="detail-d detail-d_name">';
				if (! empty($result['name']))
				{
					if(is_array($result['name']))
					{
						echo '<a'.(! empty($result['name']['user_page']) ? ' href="'.$result['name']['user_page'].'"' : '').' title="'.$this->diafan->_('Пользователь', false).'">';
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
						echo '<a title="'.$this->diafan->_('Пользователь', false).'">'.$result['name'].'</a>';
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
	<div class="element-d__date date-d">'.$result['date'].'</div>
</div>';

if(! empty($result["params"]))
{
	echo '<div class="detail-d detail-d_params">';
	foreach ($result["params"] as $param)
	{
		echo
		'<div class="param-d comments_param'.($param["type"] == 'title' ? '_title' : '').'">
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
								echo ' <a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'comments"><img src="'.$a["link_preview"].'"></a> <a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'comments_link">'.$a["name"].'</a>';
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
				elseif($param["type"] == 'textarea')
				{
					echo  nl2br($param["value"]);
				}
				else
				{
					echo $param["value"];
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

if($result["form"])
{
	echo '<div class="detail-d detail-d_functions">';

	echo
	'<div class="element-d__functions functions-d">
		<div class="functions-d__list">';

			echo
			'<button class="element-d__function function-d function-d_answer button-d button-d_short button-d_dark js_comments_show_form" type="button"
				data-target=".comments'.$result["id"].'_block_form"
				data-focus-node="textarea"
				data-text-show="'.$this->diafan->_('Ответить', false).'"
				data-text-hide="'.$this->diafan->_('Закрыть', false).'"
				data-text-node=".button-d__name"
				>
				<span class="button-d__name">'.$this->diafan->_('Ответить').'</span>
			</button>';

			// echo
			// '<button class="element-d__function function-d function-d_roll button-d button-d_short" type="button">
			// 	<span class="button-d__name">'.$this->diafan->_('Свернуть ветку сообщений').'</span>
			// </button>';

			echo
		'</div>
		<div class="functions-d__contents">';

			echo '<div class="comments'.$result["id"].'_block_form" style="display:none;">';
			echo $this->get('form', 'comments', $result["form"]);
			echo '</div>';

			echo
		'</div>
	</div>';

	echo '</div>';
}

if(! empty($result["children"]))
{
	echo
	'<div class="detail-d detail-d_discuss js_comments_discuss'.$result["id"].'_result">
		<div class="element-d__list _list">';
			echo $this->get('list', 'comments', array("rows" => $result["children"], "result" => $result));
			echo
		'</div>
	</div>';
}
else
{
	echo '<div class="detail-d detail-d_discuss js_comments_discuss'.$result["id"].'_result" style="display:none;"></div>';
}

echo '</div>';

echo '</article>';

