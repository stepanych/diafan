<?php
/**
 * Шаблон второго и последующих уровней меню, оформленного шаблоном
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



if (empty($result["rows"][$result["parent_id"]]))
{
	return;
}

// начало уровня меню
echo '<ul class="item-d__menu menu-d">';
foreach ($result["rows"][$result["parent_id"]] as $row)
{
	if ($row["active"])
	{
		// начало пункта меню для текущей страницы
		echo '<li class="item-d item-d_child item-d_current item-d_active">';
	}
	elseif ($row["active_child"])
	{
		// начало пункта меню для активного дочернего пункта
		echo '<li class="item-d item-d_child'.(! empty($result["rows"][$row["id"]]) ? ' item-d_parent item-d_active' : '').'">';
	}
	elseif ($row["children"])
	{
		// начало пункта меню для элемента -родителя
		echo ' <li class="item-d item-d_child'.(! empty($result["rows"][$row["id"]]) ? ' item-d_parent' : '').'">';
	}
	else
	{
		// начало любого другого пункта меню
		echo '<li class="item-d item-d_child">';
	}

	if (
		// на текущей странице нет ссылки, если не включена настройка "Текущий пункт как ссылка"
		(!$row["active"] || $result["current_link"])

		// включен пункт "Не отображать ссылку на элемент, если он имеет дочерние пункты"
		&& (!$result["hide_parent_link"] || empty($result["rows"][$row["id"]]))
	)
	{
		if ($row["othurl"])
		{
			echo '<a class="item-d__link item-d__link_child link-d" href="'.$row["othurl"].'"'.$row["attributes"].'>';
		}
		else
		{
			echo '<a class="item-d__link item-d__link_child link-d" href="'.BASE_PATH_HREF.$row["link"].'"'.$row["attributes"].'>';
		}
	}
	else
	{
		echo '<a class="item-d__link item-d__link_child link-d"'.$row["attributes"].'>';
	}

	//вывод изображения
	if (! empty($row["img"]))
	{
		echo
		'<span class="link-d__icon">
			<img src="'.$row["img"]["src"].'" width="'.$row["img"]["width"].'" height="'.$row["img"]["height"].' alt="'.$row["img"]["alt"].'" title="'.$row["img"]["title"].'">
		</span>';
	}

	// название пункта меню
	if (! empty($row["name"]))
	{
	    echo '<span class="link-d__name">'.$row["name"].'</span>';
	}

	if (! empty($result["rows"][$row["id"]]))
	{
		echo
		'<span class="link-d__sign sign-d">
			<span class="sign-d__icon icon-d fas fa-angle-down"></span>
		</span>';
	}

	echo '</a>';

	// описание пункта меню
	if (! empty($row["text"]))
	{
		echo '<div class="item-d__text">'.$row["text"].'</div>';
	}

	if ($result["show_all_level"])
	{
		// вывод вложенного уровня меню
		$menu_data = $result;
		$menu_data["parent_id"] = $row["id"];
		$menu_data["level"]++;

		echo $this->get('show_level_navmenu_2', 'menu', $menu_data);
	}
	if ($row["active"])
	{
		// окончание пункта меню - текущей страницы
		echo '</li>';
	}
	elseif ($row["active_child"])
	{
		// окончание пункта меню для активного дочернего пункта
		echo '</li>';
	}
	else
	{
		// окончание любого другого пункта меню
		echo '</li>';
	}
}
// окончание уровня меню
echo '</ul>';