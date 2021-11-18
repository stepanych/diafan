<?php
/**
 * Шаблон формы добавления личного сообщения
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



echo '<section class="block-d block-d_form block-d_messages block-d_messages_add">';

echo '<header class="block-d__name">'.$this->diafan->_('Добавить сообщение').'</header>';

echo '<form method="POST" action="" id="messages" class="ajax messages_form" enctype="multipart/form-data">
	<input type="hidden" name="module" value="messages">
	<input type="hidden" name="action" value="add">
	<input type="hidden" name="to" value="'.$result["to"].'">
	<input type="hidden" name="redirect" value="'.(! empty($result["redirect"]) ? $result["redirect"] : '').'">';

	echo '<div class="field-d">';
	echo $this->get('get', 'bbcode', array("name" => "message", "tag" => "message", "value" => ""));
	echo '</div>';

	echo
	'<button class="button-d">
		<span class="button-d__name">'.$this->diafan->_('Отправить', false).'</span>
	</button>

	<div class="errors error"'.(! empty($result["error"]) ? '>'.$result["error"] : ' style="display:none">').'</div>
</form>';

echo '</section>';
