<?php
/**
 * Шаблон списка элементов, к которым прикреплен тег
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

foreach ($result["rows"] as $module_name => $r)
{
	if(! empty($r["class"]))
	{
		echo $this->get($r["func"], $r["class"], $r);
	}
	else
	{
		echo
		'<section>
			<div class="_list">';
				foreach ($res["rows"] as $row)
				{
					echo
					'<article class="element-d element-d_tags element-d_tags_item">

						<div class="element-d__details details-d">';

							echo
							'<div class="detail-d detail-d_name">
								<a href="'.BASE_PATH_HREF.$row["link"].'">'.$row["name"].'</a>
							</div>';

							if(! empty($row["snippet"]))
							{
								echo '<div class="detail-d detail-d__snippet _text">'.$row["snippet"].'</div>';
							}

							echo
						'</div>

					</article>';
				}
				echo
			'</div>
		</section>';
	}
}

//Кнопка "Показать ещё"
if(! empty($result["show_more"]))
{
	echo $result["show_more"];
}