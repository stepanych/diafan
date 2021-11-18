<?php
/**
 * Шаблон дополнительных характеристик товара
 *
 * Шаблон вывода дополнительных характеристик товара
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



foreach ($result["rows"] as $param)
{
	echo 
	'<div class="param-d shop_param'.$param["id"].'">

		<span class="param-d__name'.($param["type"] == 'title' ? ' shop_param_title' : '').'">'.$param["name"].($param["value"] ? ':' : '').'</span>';

		if ($param["value"])
		{
			echo '
			<strong class="param-d__value">';

			if($param["type"] == "attachments")
			{
				foreach ($param["value"] as $a)
				{
					if ($a["is_image"])
					{
						if($param["use_animation"])
						{
							echo ' <a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'shop"><img src="'.$a["link_preview"].'"></a> <a href="'.$a["link"].'" data-fancybox="gallery'.$result["id"].'shop_link">'.$a["name"].'</a>';
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
			elseif($param["type"] == "images")
			{
				foreach ($param["value"] as $img)
				{
					if($img["source"])
					{
						echo $img["source"];
					}
					else
					{
						echo '<img src="'.$img["src"].'" width="'.$img["width"].'" height="'.$img["height"].'" alt="'.$img["alt"].'" title="'.$img["title"].'">';
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

			//единицы измерения
			if(! empty($param["measure_unit"]) && $param["type"] == 'numtext')
			{
				echo ' '.$param["measure_unit"];
			}

			echo '</strong>';
		}

		//описание характеристики
		if($param["text"])
		{
			echo '<div class="param-d__text _text">'.$param["text"].'</div>';
		}

		echo
	'</div>';
}
