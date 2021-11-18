<?php
/**
 * Шаблон форма поиска по товарам
 *
 * Шаблонный тег <insert name="show_search" module="shop"
 * [cat_id="категория"] [site_id="страница_с_прикрепленным_модулем"]
 * [ajax="подгружать_результаты"]
 * [only_module="only_on_module_page"] [template="шаблон"]>:
 * форма поиска по товарам
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



echo '<section class="block-d block-d_shop block-d_shop_search filter-d filter-d_shop">';

echo '<header class="block-d__name">'.$this->diafan->_('Поиск по товарам').'</header>';

echo
'<form method="GET" action="'.BASE_PATH_HREF.$result['path'].'" class="js_shop_search_form'.(! empty($result['send_ajax']) ? ' ajax' : '').'">
	<input type="hidden" name="module" value="shop">
	<input type="hidden" name="action" value="search">';

	if (count($result['site_ids']) > 1)
	{
		echo
		'<div class="field-d">
			<label class="field-d__name">'.$this->diafan->_('Раздел').':</label>
			<select class="js_shop_search_site_ids">';
				foreach ($result["site_ids"] as $row)
				{
					echo '<option value="'.$row["id"].'" path="'.BASE_PATH_HREF.$row["path"].'"';
					if($result["site_id"] == $row["id"])
					{
						echo ' selected';
					}
					echo '>'.$row["name"].'</option>';
				}
				echo
			'</select>
		</div>';
	}

	if (count($result["cat_ids"]) > 1)
	{
		echo
		'<div class="field-d">
			<label class="field-d__name">'.$this->diafan->_('Категория').':</label>
			<select name="cat_id" class="js_shop_search_cat_ids">
				<option value="">'.$this->diafan->_('Все').'</option>';
				foreach ($result["cat_ids"] as $row)
				{
					echo '<option value="'.$row["id"].'" site_id="'.$row["site_id"].'"';
					if($result["cat_id"] == $row["id"])
					{
						echo ' selected';
					}
					echo '>';
					if($row["level"])
					{
						echo str_repeat('- ', $row["level"]);
					}
					echo $row["name"].'</option>';
				}
				echo
			'</select>
		</div>';
	}
	else
	{
		echo '<input name="cat_id" type="hidden" value="'.$result["cat_id"].'">';
	}

	if (! empty($result["article"]))
	{
		echo
		'<div class="field-d">
			<label class="field-d__name">'.$this->diafan->_('Артикул').':</label>
			<input type="text" name="a" value="'.$result["article"]["value"].'">
		</div>';
	}

	if (! empty($result["price"]))
	{
		echo
		'<div class="field-d">
			<label class="field-d__name">'.$this->diafan->_('Цена').':</label>
			<div class="runner-d" data-min="'.(! empty($result["price"]["min"]) ? $result["price"]["min"] : '0').'" data-max="'.(! empty($result["price"]["max"]) ? $result["price"]["max"] : '50000').'" data-step="1">
				<div class="runner-d__board">
					<div class="runner-d__from">
						<div class="runner-d__field field-d">
							<input type="text" class="from" name="pr1" value="'.$result["price"]["value1"].'">
						</div>
					</div>
					<div class="runner-d__div">
						<div class="runner-d__field field-d"> — </div>
					</div>
					<div class="runner-d__to">
						<div class="runner-d__field field-d">
							<input type="text" class="to" name="pr2" value="'.$result["price"]["value2"].'">
						</div>
					</div>
				</div>
				<div class="runner-d__track"></div>
			</div>
		</div>';
	}

	if (! empty($result["brands"]))
	{
		echo
		'<div class="field-d">
			<label class="field-d__name">'.$this->diafan->_('Производитель').':</label>
			<div class="field-d__list">';
				foreach ($result["brands"] as $row)
				{
					echo
					'<div class="field-d__item js_shop_search_brand" site_id="'.$row["site_id"].'">
						<input type="checkbox" name="brand[]" value="'.$row["id"].'"';
						if(in_array($row["id"], $result["brand"]))
						{
							echo ' checked';
						}
						echo ' id="shop_search_brand'.$row["id"].'"><label for="shop_search_brand'.$row["id"].'">'.$row["name"].'</label>
					</div>';
				}
			echo
			'</div>
		</div>';
	}

	if (! empty($result["action"]))
	{
		echo
		'<div class="field-d">
			<input type="checkbox" name="ac" id="shop_search_ac" value="1"'.($result["action"]["value"] ? ' checked' : '').'>
			<label for="shop_search_ac">'.$this->diafan->_('Товар по акции').'</label>
		</div>';
	}

	if (! empty($result["new"]))
	{
		echo
		'<div class="field-d">
			<input type="checkbox" name="ne" id="shop_search_ne" value="1"'.($result["new"]["value"] ? ' checked' : '').'>
			<label for="shop_search_ne">'.$this->diafan->_('Новинка').'</label>
		</div>';
	}

	if(! empty($result["hit"]))
	{
		echo
		'<div class="field-d">
			<input type="checkbox" name="hi" id="shop_search_hit" value="1"'.($result["hit"]["value"] ? ' checked' : '').'>
			<label for="shop_search_hit">'.$this->diafan->_('Хит').'</label>
		</div>';
	}

	if(! empty($result["rows"]))
	{
		foreach ($result["rows"] as $row)
		{
			if(! in_array($row["type"], array('title', 'date', 'datetime', 'numtext', 'checkbox', 'select', 'multiple'))) {
				continue;
			}
			echo
			'<div class="field-d js_shop_search_param shop_search_param shop_search_param'.$row["id"].'" cat_ids="'.$row["cat_ids"].'">';
				switch ($row["type"])
				{
					case 'title':
						echo '<label class="field-d__title">'.$row["name"].':</label>';
						break;

					case 'date':
						echo
						'<label class="field-d__name">'.$row["name"].':</label>
						<div class="runner-d">
							<div class="runner-d__board">
								<div class="runner-d__from">
									<div class="runner-d__field field-d">
										<input type="text" name="p'.$row["id"].'_1" value="'.$row["value1"].'" class="from timecalendar" showTime="false">
									</div>
								</div>
								<div class="runner-d__div">
									<div class="runner-d__field field-d"> — </div>
								</div>
								<div class="runner-d__to">
									<div class="runner-d__field field-d">
										<input type="text" name="p'.$row["id"].'_2" value="'.$row["value2"].'" class="to timecalendar" showTime="false">
									</div>
								</div>
							</div>
						</div>';
						break;

					case 'datetime':
						echo '
						<label class="field-d__name">'.$row["name"].':</label>
						<div class="runner-d">
							<div class="runner-d__board">
								<div class="runner-d__from">
									<div class="runner-d__field field-d">
										<input type="text" name="p'.$row["id"].'_1" value="'.$row["value1"].'" class="from timecalendar" showTime="true">
									</div>
								</div>
								<div class="runner-d__div">
									<div class="runner-d__field field-d"> — </div>
								</div>
								<div class="runner-d__to">
									<div class="runner-d__field field-d">
										<input type="text" name="p'.$row["id"].'_2" value="'.$row["value2"].'" class="to timecalendar" showTime="true">
									</div>
								</div>
							</div>
						</div>';
						break;

					case 'numtext':
						echo '
						<label class="field-d__name">'.$row["name"].':</label>
						<div class="runner-d" data-min="0" data-max="500000" data-step="1">
							<div class="runner-d__board">
								<div class="runner-d__from">
									<div class="runner-d__field field-d">
										<input type="text" class="from" name="p'.$row["id"].'_1" value="'. $row["value1"].'">
									</div>
								</div>
								<div class="runner-d__div">
									<div class="runner-d__field field-d"> — </div>
								</div>
								<div class="runner-d__to">
									<div class="runner-d__field field-d">
										<input type="text" class="to"  name="p'.$row["id"].'_2" value="'.$row["value2"].'">
									</div>
								</div>
							</div>
							<div class="runner-d__track"></div>
						</div>';
						break;

					case 'checkbox':
						echo
						'<input type="checkbox" id="shop_search_p'.$row["id"].'" name="p'.$row["id"].'" value="1"'.($row["value"] ? " checked" : '').'>
						<label for="shop_search_p'.$row["id"].'">'.$row["name"].'</label>';
						break;

					case 'select':
					case 'multiple':
						echo
						'<label class="field-d__name">'.$row["name"].':</label>
						<div class="field-d__list">';
							foreach ($row["select_array"] as $key => $value)
							{
								echo
								'<div class="field-d__item">
									<input type="checkbox" id="shop_search_p'.$row["id"].'_'.$key.'" name="p'.$row["id"].'[]" value="'.$key.'"'.(in_array($key, $row["value"]) ? " checked" : '').'>
									<label for="shop_search_p'.$row["id"].'_'.$key.'">'.$value.'</label>
								</div>';
							}
							echo
						'</div>';
				}
				echo
			'</div>';
		}
	}
	echo
	'<button class="button-d" type="submit">
		<span class="button-d__icon icon-d fas fa-search"></span>
		<span class="button-d__name">'.$this->diafan->_('Найти').'</span>
	</button>
	<button class="button-d button-d_narrow'.(empty($_REQUEST["action"]) || $_REQUEST["action"] != 'search' ? ' _hidden js_reload' : '').'" type="button" onclick="window.location.href=\''.BASE_PATH_HREF.$this->diafan->_route->current_link().'\'"'.'>
		<span class="button-d__name">'.$this->diafan->_('Сбросить').'</span>
	</button>
</form>';

echo '</section>';
