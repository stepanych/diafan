<?php
/**
 * Шаблон результатов поиска по сайту
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



echo '<section class="section-d section-d_search">';

if(! $result["ajax"])
{
	echo $this->htmleditor('<insert name="show_search" module="search" button="Найти">');
}

if (! empty($result["value"]))
{
	if(! $result["ajax"])
	{
		echo
		'<div class="section-d__report _text">
			<p>
				'.$this->diafan->_('Всего найдено').': <b>'.$result["value"].': '.$result["count"].'</b>
				<br>
				'.$this->diafan->_('Документы: <strong>%d—%d</strong> из %d найденных', true, $result["count_start"], $result["count_finish"], $result["count"]).'
			</p>
		</div>';
	}
	else
	{
		if(empty($result["rows"]))
		{
			echo '<p class="_note">'.$this->diafan->_('Извините, ничего не найдено.').'</p>';
		}
	}

	echo $this->get($result["view_rows"], 'search', $result);

	//постраничная навигация
	if(! empty($result["paginator"]))
	{
		echo $result["paginator"];
	}
}
else
{
	if(! $result["ajax"])
	{
		echo '<p class="_note">'.$this->diafan->_('Слово для поиска не задано.').'</p>';
	}
}

echo '</section>';
