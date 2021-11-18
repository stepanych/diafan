<?php
/**
 * Шаблон блока вопросов и ответов
 * 
 * Шаблонный тег <insert name="show_block" module="faq" [count="количество"]
 * [cat_id="категория"] [site_id="страница_с_прикрепленным_модулем"]
 * [sort="порядок_вывода"] [often="часто_задаваемые_вопросы"]
 * [only_module="only_on_module_page"] [template="шаблон"]>:
 * блок вопросов и ответов
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

//вопросы
foreach ($result['rows'] as $row)
{
	echo
	'<article class="element-d element-d_faq element-d_faq_item _bounded">';

	echo '<div class="element-d__details details-d">';

	//вопрос
	echo '<div class="detail-d detail-d_anons _text">';
	if($row['link'])
	{
		echo '<a href="'.BASE_PATH_HREF.$row['link'].'">'.$row['anons'].'</a>';
	}
	else
	{
		echo $row['anons'];
	}
	echo '</div>';

	//дата вопроса
	if (! empty($row['date']))
	{
		echo
		'<div class="detail-d detail-d_date">
			<span class="date-d">'.$row['date'].'</span>
		</div>';
	}

	//ответ
	if(! empty($row['text']))
	{
		echo
		'<div class="detail-d detail-d_text _text">
			<span class="detail-d__icon icon-d fas fa-chevron-right"></span>';
			echo $row['text'];
			echo
		'</div>';
	}

	//теги вопроса
	if(! empty($row['tags']))
	{
		echo '<div class="detail-d detail-d_tags">'.$row['tags'].'</div>';
	}

	echo '</div>';

	echo '</article>';
}
