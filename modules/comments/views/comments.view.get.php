<?php
/**
 * Шаблон вывода комментариев
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



echo '<section class="block-d block-d_comments block-d_comments_item'.(empty($result["rows"]) ? ' _hidden" style="display: none;"' : '"').'>';

echo '<header class="block-d__name">'.$this->diafan->_('Комментарии').'</header>';

echo '<div class="block-d__list _list js_comments_discuss">';
if(! empty($result["rows"]))
{
	echo $this->get('list', 'comments', array("rows" => $result["rows"], "result" => $result));
}
echo '</div>';

//постраничная навигация
if(! empty($result["paginator"]))
{
	echo $result["paginator"];
}

echo '</section>';

if(! empty($result["unsubscribe"]))
{
	echo '<a name="comment0"></a><div class="errors _note">'.$this->diafan->_('Вы отписаны от уведомлений на новые комментарии.').'</div>';
}

if($result["form"])
{
	echo $this->get('form', 'comments', $result["form"]);
}

if($result["register_to_comment"])
{
	echo '<p class="_note">'.$this->diafan->_('Чтобы комментировать, зарегистрируйтесь или авторизуйтесь').'</p>';
}
