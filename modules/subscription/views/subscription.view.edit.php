<?php
/**
 * Шаблон форма редактирование подписки на новости
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



echo '<section class="subscription subscription_edit">';
if(empty($result["id"]))
{
	echo $this->htmleditor('<insert name="show_form" module="subscription">');
}
else
{
	if(! empty($result["act"]))
	{
		echo '<p class="_ok">'.$this->diafan->_('Вы подписаны на рассылку.').'</p>';
	}
	else
	{
		if(! empty($result["activated"]))
		{
			echo '<p class="_ok">'.$this->diafan->_('Рассылка активирована.').'</p>';
		}
		else
		{
			echo '<p class="_note">'.$this->diafan->_('Вы не подписаны на рассылку.').'</p>';
		}
	}
	if(! empty($result['cats']) || empty($result["act"]))
	{
		if(! empty($result['cats']))
		{
			echo '<h2>'.$this->diafan->_('Категории рассылки').'</h2>';
		}
		echo
		'<form method="POST" enctype="multipart/form-data" action="" class="ajax subscription_form">
			<input type="hidden" name="module" value="subscription">
			<input type="hidden" name="action" value="edit">
			<input type="hidden" name="code" value="'.$result['code'].'">
			<input type="hidden" name="mail" value="'.$result['mail'].'">';
			if(! empty($result['cats']))
			{
				foreach ($result['cats'] as $cat)
				{
					echo
					'<div class="field-d">';
						echo str_repeat('&nbsp;', 4*$cat["level"]).'<input type="checkbox" name="cat_ids[]" value="'.$cat["id"].'" id="subscription_p'.$cat["id"].'" '.(! in_array($cat["id"], $result["cats_unrel"]) ? ' checked' : '').'>
						<label for="subscription_p'.$cat["id"].'">'.$cat["name"].'</label>
					</div>';
				}
			}
			echo
			'<button class="button-d" type="submit">
				<span class="button-d__name">'.$this->diafan->_('Подписаться', false).'</span>
			</button>
			<div class="errors error" style="display:none"></div>
		</form>';
	}
	if(! empty($result["act"]))
	{
		echo '<p class="_note">'.$this->diafan->_('Чтобы отписаться пройдите по ссылке %s.', true, '<a href="'.$result['link'].'">'.$result['link'].'</a>').'</p>';
	}
}
echo '</section>';
