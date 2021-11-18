<?php
/**
 * Шаблон календаря архива новостей
 * 
 * Шаблонный тег <insert name="show_calendar" module="news"
 * [cat_id="категория_новостей"] [site_id="страница_с_прикрепленным_модулем"]
 * [detail="детализация:month|year"]
 * [only_module="only_on_module_page"] [template="шаблон"]>:
 * календарь архива новостей
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




echo '<section class="block-d block-d_news block-d_news_calendar js_news_block">';

echo '<header class="block-d__name">'.$this->diafan->_('Архив').'</header>';

echo '<div class="block-d__list _list">';

foreach ($result["rows"] as $key => $values)
{
	echo '<article class="element-d">';
	if ($result["year"] == $key && ! $result["month"])
	{
		echo '<div class="news_year_current">'.$values["year"]["name"].'</div>';
	}
	else
	{
		echo '<div class="news_year"><a href="'.BASE_PATH_HREF.$values["year"]["link"].'">'.$values["year"]["name"].'</a></div>';
	}
	if (($result["year"] == $key || ! $result["year"] && date("Y") == $key) && ! empty($values["months"]))
	{
		foreach ($values["months"] as $keym => $month)
		{
			if ($result["month"] == $keym && ! $result["day"])
			{
				echo '<div class="news_month_current">'.$month["name"].'</div>';
			}
			elseif (! $month["link"])
			{
				echo '<div class="news_month">'.$month["name"].'</div>';
			}
			else
			{
				echo '<div class="news_month"><a href="'.BASE_PATH_HREF.$month["link"].'">'.$month["name"].'</a></div>';
			}
		}
	}
	echo '</article>';
}

echo '</div>';

echo '</section>';