<?php
/**
 * Редактирование страниц характеристик
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
 * Shop_admin_parampage
 */
class Shop_admin_parampage extends Frame_admin
{
	/**
	 * @var string таблица в базе данных
	 */
	public $table = 'shop_param_select';
	
	/**
	 * @var string тип элемантов
	 */
	public $element_type = 'param';

	/**
	 * @var string часть SQL-запроса - соединение с таблицей
	 */
	public $join = ' INNER JOIN {shop_param} AS p ON p.id=e.param_id';

	/**
	* @var string часть SQL-запроса - дополнительные столбцы
	*/
	public $fields = ', e.[name] AS select_name, p.[name] AS param_name, p.site_id';

	/**
	 * @var string SQL-условия для списка
	 */
	public $where = " AND p.type IN ('select', 'multiple')";

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'name' => array(
				'type' => 'text',
				'name' => 'Значение характеристики',
				'multilang' => true,
			),
			'h1' => array(
				'type' => 'text',
				'name' => 'Название страницы',
				'multilang' => true,
			),
			'act' => array(
				'type' => 'checkbox',
				'name' => 'Опубликовать на сайте',
				'help' => 'Если не отмечена, страница характеристики не будет выводиться на сайте.',
				'default' => true,
				'multilang' => true,
			),
			'title_meta' => array(
				'type' => 'text',
				'name' => 'Заголовок окна в браузере, тег Title',
				'help' => 'Если не заполнен, тег *Title* будет автоматически сформирован как «Название страницы характеристики – Название страницы – Название сайта», либо согласно шаблонам автоформирования из настроек модуля (SEO-специалисту).',
				'multilang' => true,
			),
			'keywords' => array(
				'type' => 'textarea',
				'name' => 'Ключевые слова, тег Keywords',
				'help' => 'Если не заполнен, тег *Keywords* будет автоматически сформирован согласно шаблонам автоформирования из настроек модуля (SEO-специалисту).',
				'multilang' => true,
			),
			'descr' => array(
				'type' => 'textarea',
				'name' => 'Описание, тег Description',
				'help' => 'Если не заполнен, тег *Description* будет автоматически сформирован согласно шаблонам автоформирования из настроек модуля (SEO-специалисту).',
				'multilang' => true,
			),
			'canonical' => array(
				'type' => 'text',
				'name' => 'Канонический тег',
				'help' => 'URL канонической страницы вида: *http://site.ru/psewdossylka/*, на которую переносится "ссылочный вес" данной страницы. Используется для страниц с похожим или дублирующимся контентом (SEO-специалисту).',
				'multilang' => true,
			),
			'noindex' => array(
				'type' => 'checkbox',
				'name' => 'Не индексировать',
				'help' => 'Запрет индексации текущей страницы, если отметить, у страницы выведется тег: `<meta name="robots" content="noindex">` (SEO-специалисту).',
			),
			'changefreq'   => array(
				'type' => 'function',
				'name' => 'Changefreq',
				'help' => 'Вероятная частота изменения этой страницы. Это значение используется для генерирования файла sitemap.xml. Подробнее читайте в описании [XML-формата файла Sitemap](http://www.sitemaps.org/ru/protocol.html) (SEO-специалисту).',
			),
			'priority'   => array(
				'type' => 'floattext',
				'name' => 'Priority',
				'help' => 'Приоритетность URL относительно других URL на Вашем сайте. Это значение используется для генерирования файла sitemap.xml. Подробнее читайте в описании [XML-формата файла Sitemap](http://www.sitemaps.org/ru/protocol.html) (SEO-специалисту).',
			),
			'date_period' => array(
				'type' => 'date',
				'name' => 'Период показа',
				'help' => 'Если заполнить, текущая страница будет опубликован на сайте в указанный период. В иное время пользователи сайта страницу не будут видеть, получая ошибку 404 «Страница не найдена» (администратору сайта).',
			),
			'map_no_show' => array(
				'type' => 'checkbox',
				'name' => 'Не показывать на карте сайта',
				'help' => 'Скрывает отображение ссылки на страницу характеристики в файле sitemap.xml и [модуле «Карта сайта»](http://www.diafan.ru/dokument/full-manual/modules/map/).',
			),
			'text' => array(
				'type' => 'editor',
				'name' => 'Описание',
				'help' => 'Полное описание для страницы характеристики. Если отметить «Применить типограф», контент будет отформатирован согласно правилам экранной типографики с помощью [веб-сервиса «Типограф»](http://www.artlebedev.ru/tools/typograf/webservice/). Опция «HTML-код» позволяет отключить визуальный редактор для текущего поля. Значение этой настройки будет учитываться и при последующем редактировании.',
				'multilang' => true,
			),
		),
		'other_rows' => array (
			'admin_id' => array(
				'type' => 'function',
				'name' => 'Редактор',
				'help' => 'Изменяется после первого сохранения. Показывает, кто из администраторов сайта первый правил текущую страницу.',
			),
			'timeedit' => array(
				'type' => 'text',
				'name' => 'Время последнего изменения',
				'help' => 'Изменяется после сохранения элемента. Отдается в заголовке *Last Modify*.',
			),
			'rewrite' => array(
				'type' => 'function',
				'name' => 'Псевдоссылка',
				'help' => 'ЧПУ, т.е. адрес страницы вида: *http://site.ru/psewdossylka/*. Смотрите параметры сайта (SEO-специалисту).',
			),
			'redirect' => array(
				'type' => 'none',
				'name' => 'Редирект на текущую страницу со страницы',
				'help' => 'Позволяет делать редирект с указанной страницы на текущую.',
				'no_save' => true,
			),
			'title_view' => array(
				'type' => 'title',
				'name' => 'Оформление',
			),
			'theme' => array(
				'type' => 'function',
				'name' => 'Шаблон страницы',
				'help' => 'Возможность подключить для страницы шаблон сайта отличный от основного (themes/site.php). Все шаблоны для сайта должны храниться в папке *themes* с расширением *.php* (например, themes/dizain_so_slajdom.php). Подробнее в [разделе «Шаблоны сайта»](http://www.diafan.ru/dokument/full-manual/templates/site/). (веб-мастеру и программисту, не меняйте этот параметр, если не уверены в результате!).',
			),
			'view' => array(
				'type' => 'function',
				'name' => 'Шаблон модуля',
				'help' => 'Шаблон вывода контента модуля на странице отдельной характеристики (веб-мастеру и программисту, не меняйте этот параметр, если не уверены в результате!).',
			),
			'map' => array(
				'type' => 'module',
				'name' => 'Индексирование для карты сайта',
				'help' => 'Характеристика автоматически индексируется для карты сайта sitemap.xml.',
			),
		),
	);

	/**
	 * @var array поля в списка элементов
	 */
	public $variables_list = array (
		'checkbox' => '',
		'name' => array(
			'name' => 'Значение',
			'variable' =>'h1',
			'sql' => true,
		),
		'param_id' => array(
			'sql' => true,
		),
		'actions' => array(
			'view' => true,
			'trash' => true,
			'act' => true,
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
		'name' => array(
			'type' => 'text',
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
		if($this->diafan->is_action('edit'))
		{
			if(! $this->diafan->values('h1'))
			{
				$param_value = DB::query_result("SELECT [name] FROM {shop_param} WHERE id=%d", $this->diafan->values('param_id'));
				
				$this->diafan->values('h1', ($param_value ? $param_value.': ' : '').$this->diafan->values('name'), true);
			}
		}
	}

	/**
	 * Получает значения полей для формы (альтернативный метод)
	 *
	 * @return array
	 */
	public function get_values()
	{
		$values = DB::query_fetch_array("SELECT * FROM {".$this->diafan->table."} WHERE id=%d".($this->diafan->variable_list('actions', 'trash') ? " AND trash='0'" : '' )." LIMIT 1", $this->diafan->id
		);
		if(! $values['h1'._LANG])
		{
			$param_value = DB::query_result("SELECT [name] FROM {shop_param} WHERE id=%d", $values['param_id']);
			
			$values['h1'._LANG] = ($param_value ? $param_value.': ' : '').$values['name'._LANG];
		}
		return $values;
	}

	/**
	 * Выводит список всех возможных страниц характеристик
	 * @return void
	 */
	public function show()
	{
		echo '<div class="commentary">'.$this->diafan->_('Страницы создаются автоматически на базе характеристик товаров с типом &quot;Список&quot; и группируют соответствующие товары, позволяя создать SEO-параметры (url, title, keywords и описание) для таких группировок.').'</div>';
		$this->diafan->list_row();
	}

	/**
	 * Поиск по полю "Раздел"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_site_id($row)
	{
		if (! $this->diafan->_route->site)
		{
			return;
		}
		$this->diafan->where .= " AND p.site_id=".$this->diafan->_route->site;
		return $this->diafan->_route->site;
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
		$this->diafan->join .= " INNER JOIN {shop_param_category_rel} AS c ON p.id=c.element_id AND (c.cat_id='".$cat_id."' OR c.cat_id=0)";
		return $cat_id;
	}

	/**
	 * Поиск по полю "Название"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_name($row)
	{
		$res = $this->diafan->filter($_GET, 'string', "filter_name");
		if($res)
		{
			$this->diafan->where .= " AND (e.[name] LIKE '%%".$this->diafan->filter($_GET, 'sql', "filter_name")."%%' OR p.[name] LIKE '%%".$this->diafan->filter($_GET, 'sql', "filter_name")."%%')";
			$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?') . 'filter_name='.$this->diafan->filter($_GET, 'url', "filter_name");
		}
		return $res;
	}

	/**
	 * Выводит название характеристики и значение в списке элементов
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_name($row, $var)
	{
		$text = '<div class="name'.($var["class"] ? ' '.$var["class"] : '').'">';
		$text .= '<a name="'.$row['id'].'" href="'.$this->diafan->get_base_link($row).'">';
		if($row["select_name"])
		{
			$text .= ($row["param_name"] ? $row["param_name"].': ' : '').$row["select_name"];
		}
		else
		{
			$text .= $row["id"];
		}
		$text .= '</a>';
		$text .= '</div>';
		return $text;
	}

	/**
	 * Удаляет элемент
	 *
	 * @return void
	 */
	public function del()
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->diafan->redirect(getenv('HTTP_REFERER'));
		}

		//проверка прав пользователя на удаление
		if (! $this->diafan->_users->roles('del', $this->diafan->_admin->rewrite))
		{
			$this->diafan->redirect(getenv('HTTP_REFERER'));
		}

		if(DB::query_result("SELECT COUNT(*) FROM {trash}") > 1000)
		{
			$this->diafan->redirect(URL.'error10/'.$this->diafan->get_nav);
			return;
		}

		if (! empty($_POST["id"]))
		{
			$ids = array($_POST["id"]);
		}
		else
		{
			$ids = $_POST["ids"];
		}
		$del_ids = $this->diafan->filter($ids, "integer");
		if(! empty($del_ids))
		{
			$module_name = $this->diafan->_admin->module;

			foreach($del_ids as $del_id)
			{
				$this->diafan->current_trash = 0;
				$this->diafan->current_trash = $this->del_or_trash($this->diafan->table, $del_id);
				$this->del_or_trash_where("rewrite", "element_id=".$del_id." AND module_name='".$module_name."' AND element_type='param'");

				$this->del_or_trash_where("redirect", "element_id=".$del_id." AND module_name='".$module_name."' AND element_type='param'");
				
				$this->include_modules('delete', array(array($del_id), $module_name, 'param'));
			}
			$this->diafan->recalc();
		}
		$this->diafan->_cache->delete("", $module_name);
		$this->diafan->redirect(URL.$this->diafan->get_nav);
	}
}