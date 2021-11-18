<?php
/**
 * Шаблон блока баннеров
 * 
 * Шаблонный тег <insert name="show_block" module="bs" [count="all|количество"]
 * [cat_id="категория"] [id="номер_баннера"] [template="шаблон"]>:
 * блок баннеров
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



if (empty($result))
{
	return false;
}               

if(! isset($GLOBALS['include_bs_js']))
{
	$GLOBALS['include_bs_js'] = true;
	//скрытая форма для отправки статистики по кликам
	echo
	'<form method="POST" enctype="multipart/form-data" action="" class="ajax js_bs_form bs_form _hidden">
		<input type="hidden" name="module" value="bs">
		<input type="hidden" name="action" value="click">
		<input type="hidden" name="banner_id" value="0">
	</form>';
}

echo '<div class="slideshow-d">';

echo
'<div class="gall-d swiper-container" data-gall-autoplay="8000" data-gall-simulate-touch="true">
	<div class="gall-d__list swiper-wrapper">';
		foreach ($result as $row)
		{
			echo
			'<article class="slide-d slide-d_cover swiper-slide"'.(! empty($row['image']) ? ' style="background-image: url('.BASE_PATH.USERFILES.'/bs/'.$row['image'].');' : '').'">
				<div class="slide-d__content">';

					//вывод баннера в виде html разметки
					if (! empty($row['html']))
					{
						echo $row['html'];
					}

					//вывод баннера в виде изображения
					// if (! empty($row['image']))
					// {
						// echo '<img src="'.BASE_PATH.USERFILES.'/bs/'.$row['image'].'" alt="'.(! empty($row['alt']) ? $row['alt'] : '').'" title="'.(! empty($row['title']) ? $row['title'] : '').'">';
					// }

					//вывод описания к баннеру
					if(! empty($row['text']))
					{
						echo '<div class="_text">'.$row['text'].'</div>';
					}

					//вывод ссылки на баннер, если задана
					if (! empty($row['link']))
					{
						echo
						'<a href="'.$row['link'].'" class="button-d button-d_narrow js_bs_counter bs_counter" rel="'.$row['id'].'"'.(! empty($row['target_blank']) ? ' target="_blank"' : '').'>
							<span class="button-d__name">'.$this->diafan->_('Заказать').'</span>
						</a>';
					}

					echo
				'</div>
			</article>';

			// Варианты использования:
			// .slide-d
			// .slide-d_cover
			// .slide-d_contain
			// .slide-d_fit
			// .slide-d_fit_contain

			// <a class="slide-d slide-d_fit swiper-slide" href="'.$row['link'].'">
			// 	<div class="slide-d__content"></div>
			// 	<img class="slide-d__object" src="'.BASE_PATH.USERFILES.'/bs/'.$row['image'].'">
			// </a>
		}
		echo
	'</div>';
	echo
	'<div class="gall-d__nav">
		<button class="gall-d__button gall-d__button_prev swiper-button-prev" title="'.$this->diafan->_('Предыдущий', false).'" type="button">
			<span class="icon-d fas fa-chevron-circle-left"></span>
		</button>
		<button class="gall-d__button gall-d__button_next swiper-button-next" title="'.$this->diafan->_('Следующий', false).'" type="button">
			<span class="icon-d fas fa-chevron-circle-right"></span>
		</button>
	</div>';
	echo 
	'<div class="gall-d__pagin swiper-pagination"></div>';
	echo
'</div>';

echo '</div>';
