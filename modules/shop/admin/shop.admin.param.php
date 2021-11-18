<?php
/**
 * Редактирование дополнительных характеристик товаров
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */
if ( ! defined('DIAFAN'))
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

/**
 * Shop_admin_param
 */
class Shop_admin_param extends Frame_admin
{
	/**
	 * @var string таблица в базе данных
	 */
	public $table = 'shop_param';

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'name' => array(
				'type' => 'text',
				'name' => 'Название',
				'help' => 'Имя дополнительной характеристики товара, будет доступно для заполнения при редактировании товара',
				'multilang' => true,
			),
			'type' => array(
				'type' => 'select',
				'name' => 'Тип',
				'help' => 'Тип дополнительной характеристики. Чтобы назначить характеристику, от которой зависит цена, выберите тип «Список с выбором нескольких значений». Нажмите на ссылку ниже, чтобы почитать подробнее о типах характеристик.',
				'select' => array(
					'text' => 'строка',
					'numtext' => 'число',
					'date' => 'дата',
					'datetime' => 'дата и время',
					'textarea' => 'текстовое поле',
					'checkbox' => 'галочка',
					'select' => 'выпадающий список',
					'multiple' => 'список с выбором нескольких значений',
					'email' => 'электронный ящик',
					'phone' => 'телефон',
					'editor' => 'поле с визуальным редактором',
					'title' => 'заголовок группы характеристик',
					'attachments' => 'файлы',
					'images' => 'изображения',
				),
				'attr' => 'req_self=true', // отменяем дефолтное поведение, описанное в admin.edit.param_select.js: show_param(obj)
			),
			'max_count_attachments' => array(
				'type' => 'none',
				'name' => 'Максимальное количество добавляемых файлов',
				'help' => 'Количество добавляемых файлов. Если значение равно нулю, то форма добавления файлов не выводится. Параметр выводится, если тип характеристики задан как «файлы».',
				'no_save' => true,
			),
			'attachment_extensions' => array(
				'type' => 'none',
				'name' => 'Доступные типы файлов (через запятую)',
				'help' => 'Параметр выводится, если тип характеристики задан как «файлы».',
				'no_save' => true,
			),
			'recognize_image' => array(
				'type' => 'none',
				'name' => 'Распознавать изображения',
				'help' => 'Позволяет прикрепленные файлы в формате JPEG, GIF, PNG отображать как изображения. Параметр выводится, если тип характеристики задан как «файлы».',
				'no_save' => true,
			),
			'attach_big' => array(
				'type' => 'none',
				'name' => 'Размер для большого изображения',
				'help' => 'Размер изображения, отображаемый в пользовательской части сайта при увеличении изображения предпросмотра. Параметр выводится, если тип характеристики задан как «файлы» и отмечена опция «Распознавать изображения».',
				'no_save' => true,
			),
			'attach_medium' => array(
				'type' => 'none',
				'name' => 'Размер для маленького изображения',
				'help' => 'Размер изображения предпросмотра. Параметр выводится, если тип характеристики задан как «файлы» и отмечена опция «Распознавать изображения».',
				'no_save' => true,
			),
			'attach_use_animation' => array(
				'type' => 'none',
				'name' => 'Использовать анимацию при увеличении изображений',
				'help' => 'Параметр добавляет JavaScript код, позволяющий включить анимацию при увеличении изображений. Параметр выводится, если отмечена опция «Распознавать изображения». Параметр выводится, если тип характеристики задан как «файлы» и отмечена опция «Распознавать изображения».',
				'no_save' => true,
			),
			'upload_max_filesize' => array(
				'type' => 'none',
				'name' => 'Максимальный размер загружаемых файлов',
				'help' => 'Параметр показывает максимально допустимый размер загружаемых файлов, установленный в настройках хостинга. Параметр выводится, если тип характеристики задан как «файлы».',
				'no_save' => true,
			),
			'images_variations' => array(
				'type' => 'none',
				'name' => 'Генерировать размеры изображений',
				'help' => 'Размеры изображений, заданные в модуле «Изображения». Параметр выводится, если тип характеристики задан как «изображение».',
				'no_save' => true,
			),
			'required' => array(
				'type' => 'checkbox',
				'name' => 'Доступен к выбору при заказе',
				'help' => 'Если отметить, значения этой характеристики  пользователь сможет выбрать при покупке товара, например, цвет или размер одежды. Обязательно отметьте данный параметр, для назначения товару зависимой цены или зависимых изображений.',
				'depend' => 'type=multiple',
			),
			'measure_unit' => array(
				'type' => 'text',
				'name' => 'Единица измерения',
				'help' => 'Параметр выводится, если тип характеристики задан как «число».',
				'multilang' => true,
				'depend' => 'type=numtext',
			),
			'param_select' => array(
				'type' => 'function',
				'name' => 'Значения',
				'help' => 'Поле появляется для характеристик с типом «галочка», «выпадающий список» и «список с выбором нескольких значений».',
				'parampage' => true,
			),
			'site_id' => array(
				'type' => 'none',
				'name' => 'Раздел сайта',
				'help' => 'Раздел сайта, к которому применяется характеристика.',
				'no_save' => true,
			),
			'category' => array(
				'type' => 'function',
				'name' => 'Категории',
				'help' => 'Категории, к которым применяется характеристика. Чтобы выбрать несколько категорий, удерживайте CTRL.',
			),
			'hr1' => 'hr',
			'search' => array(
				'type' => 'checkbox',
				'name' => 'Использовать в форме поиска',
				'help' => 'Если отмечено, данная характеристика товара будет использована при поиске, выводимом тегом show_search.',
				'depend' => 'type=numtext|type=date|type=datetime|type=checkbox|type=select|type=multiple|type=title',
			),
			'list' => array(
				'type' => 'checkbox',
				'name' => 'Показывать в списке',
				'help' => 'Если отмечено, данная характеристика будет отображаться в списке товаров.',
			),
			'block' => array(
				'type' => 'checkbox',
				'name' => 'Показывать в блоке товаров',
				'help' => 'Если отмечено, данная характеристика будет отображаться в блоках товаров, выводимом тегом show_block.',
			),
			'id_page' => array(
				'type' => 'checkbox',
				'name' => 'Показывать на странице товара',
				'help' => 'Если отмечено, данная характеристика будет отображаться на странице товара.',
				'default' => true,
			),
			'display_in_sort' => array(
				'type' => 'checkbox',
				'name' => 'Отображать параметры в блоке для сортировки товаров',
				'help' => 'Позволяет выводить характеристику в виде ссылки для сортировки товаров по значению характеристики.',
				'depend' => 'type=text|type=numtext|type=date|type=datetime|type=checkbox|type=email|type=phone|type=attachments|type=images',
			),
			'hr2' => 'hr',
			'yandex_use' => array(
				'type' => 'checkbox',
				'name' => 'Выгружать в файле YML (Яндекс.Маркет)',
				'help' => 'Характеристика будет выгружена в [элемент param](https://yandex.ru/support/partnermarket/param.xml).',
			),
			'yandex_name' => array(
				'type' => 'text',
				'name' => 'Название для файла YML (Яндекс.Маркет)',
			),
			'yandex_unit' => array(
				'type' => 'text',
				'name' => 'Единица измерения для файла YML (Яндекс.Маркет)',
			),
			'hr3' => 'hr',
			'text' => array(
				'type' => 'textarea',
				'name' => 'Описание характеристики',
				'multilang' => true,
			),
			'sort' => array(
				'type' => 'function',
				'name' => 'Сортировка: установить перед',
				'help' => 'Изменить положение текущей характеристики среди других характеристик. Например, в фильтре товаров (Администратору сайта).',
			),
		),
	);

	/**
	 * @var array поля в списка элементов
	 */
	public $variables_list = array (
		'checkbox' => '',
		'sort' => array(
			'name' => 'Сортировка',
			'type' => 'numtext',
			'sql' => true,
			'fast_edit' => true,
		),
		'name' => array(
			'name' => 'Название'
		),
		'type' => array(
			'name' => 'Тип',
			'type' => 'select',
			'sql' => true,
			'no_important' => true,
		),
		'actions' => array(
			'trash' => true,
		),
	);

	/**
	 * @var array поля для фильтра
	 */
	public $variables_filter = array (
		'site_id' => array(
			'type' => 'select',
		),
		'cat_id' => array(
			'type' => 'select',
		),
	);

	/**
	 * Подготавливает конфигурацию модуля
	 * @return void
	 */
	public function prepare_config()
	{
		$sites = DB::query_fetch_all("SELECT id, [name], parent_id FROM {site} WHERE trash='0' AND module_name='%s' ORDER BY sort ASC", $this->diafan->_admin->module);
		if(count($sites))
		{
			$this->diafan->not_empty_site = true;
		}
		foreach($sites as $site)
		{
			$this->cache["parent_site"][$site["id"]] = $site["name"];
		}
		if(count($sites) == 1)
		{
			if (DB::query_result("SELECT id FROM {shop} WHERE trash='0' AND site_id<>%d LIMIT 1", $sites[0]["id"]))
			{
				$sites[] = 0;
			}
			else
			{
				$this->diafan->_route->site = $sites[0]["id"];
			}
		}
		$this->diafan->sites = $sites;
		
		if (! $this->diafan->configmodules("cat", "shop", $this->diafan->_route->site))
		{
			$this->diafan->variable_unset("cat_id");
		}
		else
		{
			$cats = DB::query_fetch_all(
				"SELECT id, [name], parent_id, site_id FROM {shop_category} WHERE trash='0'"
				.($this->diafan->_route->site ? " AND site_id='".$this->diafan->_route->site."'" : "")
				." ORDER BY sort ASC LIMIT 1000"
			);
			if(count($cats))
			{
				$this->diafan->not_empty_categories = true;
			}
			if(count($cats) == 1000)
			{
				$this->diafan->categories = array();
			}
			else
			{
				$this->diafan->categories = $cats;
			}
		}
	}

	/**
	 * Выводит ссылку на добавление
	 * @return void
	 */
	public function show_add()
	{
		$this->diafan->addnew_init('Добавить характеристику');
	}

	/**
	 * Выводит список дополнительных характеристик товара
	 * @return void
	 */
	public function show()
	{
		$this->diafan->list_row();
	}

	/**
	 * Выводит категории характеристики в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_parent($row, $var)
	{
		if(! isset($this->cache["prepare"]["parent_cats"]))
		{
			$this->cache["prepare"]["parent_cats"] = DB::query_fetch_key_array(
				"SELECT s.[name], c.element_id, s.id FROM {shop_param_category_rel} as c"
				." INNER JOIN {shop_category} as s ON s.id=c.cat_id"
				." WHERE element_id IN (%s)",
				implode(",", $this->diafan->rows_id),
				"element_id"
			);
		}
		$cats = array();
		if(! empty($this->cache["prepare"]["parent_cats"][$row["id"]]))
		{
			foreach($this->cache["prepare"]["parent_cats"][$row["id"]] as $cat)
			{
				$cats[] = '<a href="'.BASE_PATH_HREF.'shop/category/edit'.$cat["id"].'/">'.$cat["name"].'</a>';
			}
		}
		if ( ! $cats)
		{
			$cats[] = $this->diafan->_('Общие');
		}
		$title = '';
		if(count($cats) > 3)
		{
			$title = ' title="'.strip_tags(implode(', ', $cats)).'"';
			$cats = array_slice($cats, 0, 3);
			$cats[] = ' ...';
		}
		//$title = '';
		return '<div class="categories"'.$title.'>'.implode(', ', $cats).'</div>';
	}

	/**
	 * Поиск по полю "Раздел сайта"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_site_id($row)
	{
		$site_id = $this->diafan->_route->site;
		if (! $site_id)
		{
			return;
		}
		$this->diafan->where .= " AND e.site_id IN (0, ".$site_id.")";
		return $site_id;
	}

	/**
	 * Поиск по полю "Категория"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_cat_id($row)
	{
		$cat_id = $this->diafan->_route->cat;
		if (! $cat_id)
		{
			return;
		}
		$this->diafan->join .= " INNER JOIN {shop_param_category_rel} AS c ON e.id=c.element_id AND (c.cat_id='".$cat_id."' OR c.cat_id=0)";
		return $cat_id;
	}

	/**
	 * Редактирование поля "Раздел сайта"
	 *
	 * @return void
	 */
	public function edit_variable_site_id(){}

	/**
	 * Редактирование поля "Категория и Раздел сайта"
	 *
	 * @return void
	 */
	public function edit_variable_category()
	{
		echo '<div class="unit" id="name">
				↑ <a href="http'.(IS_HTTPS ? "s" : '').'://www.diafan.ru/dokument/full-manual/modules/shop/#KHarakteristiki" target="_blank">'.$this->diafan->_('О типах характеристик').'</a>
		</div>
		<h2></h2>';

		$value = $this->diafan->values('site_id');
		if ($this->diafan->is_new)
		{
			$value = $this->diafan->_route->site;
		}
		$sites = DB::query_fetch_all("SELECT id, [name] FROM {site} WHERE trash='0' AND module_name='%s' ORDER BY sort ASC, id DESC", $this->diafan->_admin->module);

		echo '
		<div class="unit" id="site_id">
			<div class="infofield">'.$this->diafan->variable_name('site_id').$this->diafan->help('site_id').'</div>
			<select name="site_id">
			<option value="0">'.$this->diafan->_('Все').'</option>';
			echo $this->diafan->get_options(array(0 => $sites), $sites, array($value)).'
			</select>
		</div>';

		if(! $this->diafan->configmodules("cat", "shop", 0))
		{
			return;
		}

		$rows = DB::query_fetch_all("SELECT id, [name], parent_id, site_id FROM {shop_category} WHERE trash='0' ORDER BY sort ASC LIMIT 1000");
		foreach ($rows as $row)
		{
			$cats[$row["site_id"]][$row["parent_id"]][] = $row;
		}

		$values = array();
		if ( ! $this->diafan->is_new)
		{
			$values = DB::query_fetch_value("SELECT cat_id FROM {shop_param_category_rel} WHERE element_id=%d AND cat_id>0", $this->diafan->id, "cat_id");
		}
		elseif($this->diafan->_route->cat)
		{
			$values[] = $this->diafan->_route->cat;
		}
		$max = 1000;
		if(count($rows) == $max)
		{
			// TO_DO: foreach($values as $value) { echo '<input type="hidden" name="cat_ids[]" value="'.$value.'">'; } return;
			echo '
			<div class="unit" id="cat_ids">
				<div class="infofield">'.$this->diafan->_('Категория').$this->diafan->help()
			.' <a href="javascript:void(0)" class="cat_id_edit"><i class="fa fa-pencil" title="'.$this->diafan->_('Редактировать').'"></i></a>'
			.'</div>';
					$cats = array();
					if($values)
					{
						$cats = DB::query_fetch_all("SELECT id, [name] FROM {%s_category} WHERE id IN (%s) ORDER BY sort ASC", $this->diafan->_admin->module, implode(',', $values));
					}
			echo '
				<div class="additional_cat_ids">
					<div class="cat_id_edit_container" style="display:none">'
					.$this->diafan->_('Добавить категорию').': <input type="text" name="cat_search" value="" size="30">'
				.'</div>';
			foreach($cats as $cat)
			{
				echo '<div><input type="checkbox" name="cat_ids[]" value="'.$cat["id"].'" id="input_user_additional_cat_id_'.$cat["id"].'" checked> <label for="input_user_additional_cat_id_'.$cat["id"].'">'.$cat["name"].'</label></div>';
			}
			echo '
				</div>';
			echo '
			</div>';
			$path = 'adm/js/edit/admin.edit.cat_id'.'.js';
			if(Custom::exists($path))
			{
				$this->diafan->_admin->js_view[] = Custom::path($path);
			}
			return;
		}

		echo '
		<div class="unit" id="cat_ids">
			<div class="infofield">'.$this->diafan->_('Категория').$this->diafan->help().'</div>';

		echo ' <select name="cat_ids[]" multiple="multiple" size="11">
		<option value="all"'.(empty($values) ? ' selected' : '').'>'.$this->diafan->_('Все').'</option>';
		foreach ($sites as $site)
		{
			if(! empty($cats[$site["id"]]))
			{
				if(count($sites) > 1)
				{
					echo '<optgroup label="'.$site["name"].'" data-site_id="'.$site["id"].'">';
				}
				echo $this->diafan->get_options($cats[$site["id"]], $cats[$site["id"]][0], $values);
				if(count($sites) > 1)
				{
					echo '</optgroup>';
				}
			}
		}
		echo '</select>';

		echo '
		</div>';
	}

	/**
	 * Сохранение поля "Категория"
	 *
	 * @return void
	 */
	public function save_variable_category()
	{
		$site_id = $this->diafan->filter($_POST, "integer", "site_id");
		
		DB::query("DELETE FROM {shop_param_category_rel} WHERE element_id=%d", $this->diafan->id);
		if(! empty($_POST["cat_ids"]) && in_array("all", $_POST["cat_ids"]))
		{
			$_POST["cat_ids"] = array();
		}
		$cat_ids = array();
		if(! empty($_POST["cat_ids"]))
		{
			foreach ($_POST["cat_ids"] as $cat_id)
			{
				$cat_id = $this->diafan->filter($cat_id, "integer");
				$cat_ids[] = $cat_id;
			}
		}
		if(! $site_id && $cat_ids)
		{
			$site_id = DB::query_result("SELECT site_id FROM {shop_category} WHERE trash='0' AND id=%d", $cat_ids[0]);
		}
		if($cat_ids)
		{
			$cat_ids = DB::query_fetch_value("SELECT id FROM {shop_category} WHERE trash='0' AND site_id=%d AND id IN (%s)", $site_id, implode(",", $cat_ids), "id");
		}
		if($cat_ids)
		{
			foreach ($cat_ids as $cat_id)
			{
				DB::query("INSERT INTO {shop_param_category_rel} (element_id, cat_id) VALUES(%d, %d)", $this->diafan->id, $cat_id);
			}
		}
		else
		{
			DB::query("INSERT INTO {shop_param_category_rel} (element_id) VALUES(%d)", $this->diafan->id);
		}

		$this->diafan->set_query("site_id=%d");
		$this->diafan->set_value($site_id);
	}

	/**
	 * Сохранение кнопки "Доступен к выбору при заказе"
	 * @return void
	 */
	public function save_variable_required()
	{
		$this->diafan->set_query("required='%d'");
		$this->diafan->set_value(! empty($_POST["required"]) && $_POST["type"] == 'multiple' ? '1' : '0');

		if(! empty($_POST["required"]) && $_POST["type"] == 'multiple' && ! $this->diafan->values("required"))
		{
			if(! DB::query_result("SELECT id FROM {shop_price_param} WHERE param_id=%d LIMIT 1", $this->diafan->id))
			{
				$cats = array();
				if(! empty($_POST["cat_ids"]))
				{
					foreach ($_POST["cat_ids"] as $id)
					{
						if(intval($id))
						{
							$cats[] = intval($id);
						}
					}
				}
				$q = ''; $i = 0;
				$rows = DB::query_fetch_all(
					"SELECT DISTINCT(p.price_id) FROM {shop_price} AS p"
					.(! empty($cats) ? " INNER JOIN {shop_category_rel} AS c ON c.element_id=p.good_id AND cat_id IN (".implode(',', $cats).")" : '')
					." WHERE p.trash='0'");
				foreach ($rows as $row)
				{
					if(! $q)
					{
						$q = "INSERT INTO {shop_price_param} (price_id, param_id, param_value) VALUES (".$row["price_id"].", ".$this->diafan->id.", 0)";
					}
					else
					{
						$q .= ", (".$row["price_id"].", ".$this->diafan->id.", 0)";
					}
					$i++;
					if($i == 200)
					{
						DB::query($q);
						$q = '';
					}
				}
				if($q)
				{
					DB::query($q);
				}
			}
		}
	}

	/**
	 * Сопутствующие действия при удалении элемента модуля
	 * @return void
	 */
	public function delete($del_ids)
	{
		$this->diafan->del_or_trash_where("shop_price_param", "param_id IN (".implode(",", $del_ids).")");
	}
}
