<?php
/**
 * Шаблон списка контактов
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



if (empty($result['rows']))
{
	echo '<div class="_note">'.$this->diafan->_('У Вас нет ни одного контакта. Чтобы вести приватную переписку с пользователем нужно на странице пользователя выбрать «Напишите сообщение».').'</div>';
	return false;
}

echo '<section class="section-d section-d_list section-d_messages section-d_messages_contacts js_messages">';

if(! empty($result["rows"]))
{
	//вывод списка контактов
	echo '<div class="section-d__list _list">';
	echo $this->get($result["view_rows"], 'messages', $result);
	echo '</div>';
}

//постраничная навигация
if(! empty($result["paginator"]))
{
	echo $result["paginator"];
}

echo '</section>';