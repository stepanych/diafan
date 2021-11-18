<?php
/**
 * Шаблон блока похожих вопросов и ответов
 * 
 * Шаблонный тег <insert name="show_block_rel" module="faq" [count="количество"] [template="шаблон"]>:
 * блок похожих вопросов и ответов
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



if(empty($result["rows"])) return false;

echo '<section class="block-d block-d_faq block-d_faq_item block-d_faq_item_rel">';

echo '<header class="block-d__name">'.$this->diafan->_('Похожие вопросы').'</header>';

//заголовок блока
if (! empty($result["name"]))
{
	echo '<header class="block-d__name">'.$result['name'].'</header>';
}

//вопросы
echo '<div class="block-d__list _list">';
echo $this->get($result["view_rows"], 'faq', $result);
echo '</div>';

echo '</section>';