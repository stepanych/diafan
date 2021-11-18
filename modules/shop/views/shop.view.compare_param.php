<?php
/**
 * Шаблон дополнительных характеристик товара на странице сравнения
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



foreach ($result["existed_params"] as $r)
{
	$param = ! empty($result["params"][$r["id"]]) ? $result["params"][$r["id"]] : '';

	echo '<div class="param-d param-d_compare'.(in_array($r['id'], $result['param_differences']) ? ' _diff' : '').'" data-param_id="'.$r['id'].'">';
	echo '<span class="param-d__value">';

	if (! empty($param["value"]))
	{
		if($param["type"] == "attachments")
		{
			foreach ($param["value"] as $a)
			{
				if ($a["is_image"])
				{
					if($param["use_animation"])
					{
						echo '<a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'shop"><img src="'.$a["link_preview"].'"></a> <a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'shop_link">'.$a["name"].'</a>';
					}
					else
					{
						echo ' <a href="'.$a["link"].'"><img src="'.$a["link_preview"].'"></a> <a href="'.$a["link"].'">'.$a["name"].'</a>';
					}
				}
				else
				{
					echo ' <a href="'.$a["link"].'">'.$a["name"].'</a>';
				}
			}
		}
		elseif(! empty($param["link"]))
		{
			echo '<a href="'.BASE_PATH_HREF.$param["link"].'">'.$param["value"].'</a>';
		}
		elseif (is_array($param["value"]))
		{
			foreach ($param["value"] as $p)
			{
				if ($param["value"][0] != $p)
				{
					echo ', ';
				}
				if (is_array($p))
				{
					if ($p["link"])
					{
						echo '<a href="'.BASE_PATH_HREF.$p["link"].'">'.$p["name"].'</a>';
					}
					else
					{
						echo $p["name"];
					}
				}
				else
				{
					echo $p;
				}
			}
		}
		else
		{
			echo $param["value"];
		}
	}
	else
	{
		echo '-';
	}

	echo '</span>';
	echo '</div>';
}
