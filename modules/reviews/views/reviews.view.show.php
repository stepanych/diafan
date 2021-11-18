<?php
/**
 * Шаблон вывода отзывов
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



echo '<section class="block-d block-d_reviews block-d_reviews_item">';

echo '<header class="block-d__name">'.$this->diafan->_('Отзывы').'</header>';

if(! empty($result["average_rating"]))
{
	echo '<div class="reviews_average_rating">'.$this->diafan->_('Средняя оценка').': '.$result["average_rating"].'</div>';
}

echo '<div class="block-d__list _list">';
echo $this->get($result["view_rows"], 'reviews', $result);
echo '</div>';

echo '</section>';

//постраничная навигация
if(! empty($result["paginator"]))
{
	echo $result["paginator"];
}

if($result["form"])
{
	echo $this->get('form', 'reviews', $result["form"]);
}

if($result["register_to_review"])
{
	echo '<p class="_note">'.$this->diafan->_('Чтобы оставить отзыв, зарегистрируйтесь или авторизуйтесь.').'</p>';
}
