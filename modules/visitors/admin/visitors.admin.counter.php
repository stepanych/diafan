<?php
/**
 * Редактирование почтовых отправлений
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
 * Postman_admin
 */
class Visitors_admin_counter extends Frame_admin
{
	/**
	 * @var string таблица в базе данных
	 */
	public $table = 'visitors_session';

	/**
	 * @var integer задержка, при которой допускается, что посетитель, запросивший контент страницы, активен на сайте
	 */
	static private $delay_activity_user; // значение в секундах

	/**
	 * @var integer задержка, при которой допускается, что посетитель, запросивший контент страницы, активен на сайте
	 */
	static private $delay_activity_bot; // значение в секундах

	/**
	 * @var string часть SQL-запроса - дополнительные столбцы
	 */
	public $fields = ", IF(`status` = '0', (`timeedit`+30), (`timeedit`+900)) as timeedit_sort";

	/**
	 * @var string часть SQL-запроса - соединение с таблицей
	 */
	public $join = "";

	/**
	 * @var string SQL-условия для списка
	 */
	//public $where = " AND 1=1";

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'user' => array(
				'type' => 'text',
				'name' => 'Посетитель',
				'no_save' => true,
				'disabled' => true,
				'help' => 'Свободное информационное текстовое поле.',
			),
			'role' => array(
				'type' => 'select',
				'name' => 'Тип пользователя',
				'help' => 'Тип прав пользователя. Уровень доступа настраивается в модуле «Права доступа».',
				'select_db' => array(
					'table' => 'users_role',
					'name' => 'nameLANG',
					'where' => "trash='0'",
					'order' => "sort ASC",
				),
				'no_save' => true,
				'disabled' => true,
			),
			'hr1' => array(
				'type' => 'title',
				'name' => 'Дополнительно',
			),
			'timeedit' => array(
				'type' => 'datetime',
				'name' => 'Дата и время посещения',
				'no_save' => true,
				'disabled' => true,
				'help' => 'Дата и время последнего обращения к страницам сайта.',
			),
			'hostname' => array(
				'type' => 'text',
				'name' => 'IP адрес пользователя',
				'no_save' => true,
				'disabled' => true,
				'help' => 'Последний использованный пользователем ip при обращении к сайту.',
			),
			'user_agent' => array(
				'type' => 'textarea',
				'name' => 'Браузер пользователя',
				'no_save' => true,
				'disabled' => true,
				'help' => 'Браузер пользователя.',
			),
			'hr2' => array(
				'type' => 'title',
				'name' => 'История обращений',
			),
			'history' => array(
				'type' => 'function',
				'name' => 'Хронология',
				'no_save' => true,
				'disabled' => true,
				'help' => 'Хронология обращений пользователя к страницам сайта за последние две недели.',
				'height' => 450,
			),
		),
	);

	/**
	 * @var array поля в списка элементов
	 */
	public $variables_list = array (
		'user' => array(
			'name' => 'Посетитель',
			'type' => 'function',
			'class' => 'name',
		),
		'role' => array(
			'name' => 'Тип',
			'type' => 'function',
			'class' => 'no_important',
		),
		'session' => array(
			'name' => 'Был на сайте',
			'type' => 'function',
			'class' => 'text no_important',
		),
		'ip' => array(
			'name' => 'IP',
			'type' => 'function',
			'class' => 'text no_important',
		),
		'url' => array(
			'name' => 'url',
			'type' => 'function',
			'class' => 'text',
		),
		'adapt' => array(
			'class_th' => 'item__th_adapt',
		),
		'separator' => array(
			'class_th' => 'item__th_seporator',
		),
		'user_id' => array(
			'sql' => true,
			'type' => 'none',
		),
		'role_id' => array(
			'sql' => true,
			'type' => 'none',
		),
		'session_id' => array(
			'sql' => true,
			'type' => 'none',
		),
		'timestamp' => array(
			'sql' => true,
			'type' => 'none',
		),
		'hostname' => array(
			'sql' => true,
			'type' => 'none',
		),
		'status' => array(
			'sql' => true,
			'type' => 'none',
		),
		'search_bot' => array(
			'sql' => true,
			'type' => 'none',
		),
		'user_agent' => array(
			'sql' => true,
			'type' => 'none',
		),
	);

	/**
	 * @var array поля для фильтра
	 */
	public $variables_filter = array (
		'user' => array(
			'type' => 'checkbox',
			'name' => 'Все авторизованные',
		),
		'visitor' => array(
			'type' => 'checkbox',
			'name' => 'Все гости',
		),
		'search_bot' => array(
			'type' => 'checkbox',
			'name' => 'Все поисковые боты',
		),
		'bot' => array(
			'type' => 'checkbox',
			'name' => 'Все спам-боты',
		),
		'hr1' => array(
			'type' => 'hr',
		),
		'role_id' => array(
			'type' => 'select',
			'name' => 'Искать по типу пользователя',
		),
		'hr2' => array(
			'type' => 'hr',
		),
		'user_name' => array(
			'type' => 'text',
			'name' => 'Искать по названию пользователя',
		),
		'hostname' => array(
			'type' => 'text',
			'name' => 'Искать по IP-адресату',
		),
	);

	/**
	 * @var array настройки модуля
	 */
	public $config = array (
		'db_ex', // составные идентификаторы 
	);

	/**
	 * @var string информационное сообщение
	 */
	private $important_title = '';

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		Custom::inc('modules/visitors/inc/visitors.inc.counter.php');

		self::$delay_activity_user = $this->diafan->filter($this->diafan->configmodules('counter_delay_activity_user', 'visitors'), "integer");
		self::$delay_activity_user = ! empty(self::$delay_activity_user) ? self::$delay_activity_user : Visitors_inc_counter::DELAY_ACTIVITY_USER; // по умолчанию 15 минут
		self::$delay_activity_bot = $this->diafan->filter($this->diafan->configmodules('counter_delay_activity_bot', 'visitors'), "integer");
		self::$delay_activity_bot = ! empty(self::$delay_activity_bot) ? self::$delay_activity_bot : Visitors_inc_counter::DELAY_ACTIVITY_BOT; // по умолчанию 30 секунд
	}

	/**
	 * Формирует часть SQL-запрос для списка элементов, отвечающую за сортировку
	 *
	 * @return string
	 */
	public function sql_query_order()
	{
		$order = parent::sql_query_order();
		$order = preg_replace('/^[ ]*ORDER BY[ ]+/i', '', $order, 1);
		return " ORDER BY "
		."`timeedit_sort` DESC, e.user_id DESC"
		.(! empty($order) ? ", ".$order : "");
	}

	/**
	 * Подготавливает конфигурацию модуля
	 * @return void
	 */
	public function prepare_config()
	{
		// определение значений фильтра
		$this->variables_filter["role_id"]["select"] = array();
		$rows = DB::query_fetch_all("SELECT `role_id` AS id, `role_id` AS name FROM {%s} WHERE `role_id`<>0 GROUP BY `role_id`", $this->diafan->table);
		$role_ids = $this->diafan->array_column($rows, "id");
		if(! isset($this->cache["prepare"]["users_role"]))
		{
			$this->cache["prepare"]["users_role"] = array();
			if(! empty($role_ids))
			{
				$this->cache["prepare"]["users_role"] = DB::query_fetch_key("SELECT id, [name] FROM {users_role} WHERE id IN (%s)", $role_ids, "id");
			}
		}
		foreach($rows as $row)
		{
			if(empty($this->cache["prepare"]["users_role"][$row["id"]]))
			{
				continue;
			}
			$this->variables_filter["role_id"]["select"][$row["id"]] = $this->cache["prepare"]["users_role"][$row["id"]]["name"];
		}
		if(! empty($_GET["filter_user_name"]))
		{
			$this->diafan->join .= " LEFT JOIN `diafan_users` AS u ON e.user_id=u.id";
		}
	}

	/**
	 * Выводит контент модуля
	 * @return void
	 */
	public function show()
	{
		if(_LANG != $this->diafan->_languages->admin)
		{
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/visitors/');
		}
		echo '<br>';
		echo '<div class="commentary">'.sprintf($this->diafan->_('Бета-версия модуля. Замечания отправляйте в %sТехническую поддержку%s DIAFAN.CMS.'), '<a href="https://user.diafan.ru/support/">', '</a>').'</div>';
		echo '<br>';

		if(! $this->diafan->_visitors->counter_is_enable())
		{
			echo '<br>';
			echo '<div class="error">'.sprintf($this->diafan->_('Требуется активировать ведение Статистики cms в %sнастройках%s модуля.'), '<a href="'.BASE_PATH_HREF.'visitors/config/">', '</a>').'</div>';
		}

		echo $this->important_title;

		echo '<span class="shop_stat">';

		$typestat = "status='%d' AND SUBSTRING(session_id, 1, 1)<>'_' AND";

		$delay = self::$delay_activity_user;
		$statusers = DB::query_result("SELECT COUNT(*) FROM {%s} WHERE ".$typestat." user_id>0 AND timeedit>=%d", $this->diafan->table, 1, time() - $delay);
		$delay = self::$delay_activity_user;
		$statguest = DB::query_result("SELECT COUNT(*) FROM {%s} WHERE ".$typestat." user_id=0 AND timeedit>=%d", $this->diafan->table, 1, time() - $delay);
		$delay = self::$delay_activity_bot;
		$statsearchbot = DB::query_result("SELECT COUNT(*) FROM {%s} WHERE ".$typestat." search_bot<>'' AND timeedit>=%d", $this->diafan->table, 0, time() - $delay);
		$delay = self::$delay_activity_bot;
		$statbot = DB::query_result("SELECT COUNT(*) FROM {%s} WHERE ".$typestat." search_bot='' AND timeedit>=%d", $this->diafan->table, 0, time() - $delay);

		echo $this->diafan->_('По состоянию на').' '.date('d.m.Y H:i', time()).'<br>'
		.$this->diafan->_('на сайте пользователей <b>%s</b> / гостей <b>%s</b> / поисковых ботов <b>%s</b> / ботов <b>%s</b>', $statusers, $statguest, $statsearchbot, $statbot);

		echo '</span>';

		$this->diafan->list_row();
	}

	/**
	 * Выводит ссылку на добавление
	 *
	 * @return void
	 */
	public function show_update()
	{
		$this->diafan->update_init();
	}

	/**
	 * Формирует дополнительные классы для строк списока элементов
	 *
	 * @param array $row информация о текущем элементе списка
	 * @return string
	 */
	public function list_row_class($row)
	{
		$class = '';
		if($row["status"] == 0)
		{
			if($row["search_bot"] <> '')
			{
				$class .= ' row_search_bot';
			}
			else
			{
				$class .= ' row_bot';
			}
		}
		else
		{
			if($row["user_id"] > 0)
			{
				$class .= ' row_user';
			}
			else
			{
				$class .= ' row_guest';
			}
		}
		return $class;
	}

	/**
	 * Выводит Посетитель пользователя в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_user($row, $var)
	{
		if(! isset($this->cache["prepare"]["visitors_session"]))
		{
			$this->cache["prepare"]["visitors_session"] = array();
			if(! empty($this->diafan->rows_id))
			{
				$this->cache["prepare"]["visitors_session"] = DB::query_fetch_key("SELECT * FROM {visitors_session} WHERE id IN ("
					.substr(str_repeat(",'%s'", count($this->diafan->rows_id)), 1)
					.") ORDER BY master_id ASC, slave_id ASC", $this->diafan->rows_id, "id");
			}
		}
		if(! isset($this->cache["prepare"]["users"]))
		{
			$this->cache["prepare"]["users"] = array();
			$user_ids = $this->diafan->array_column($this->cache["prepare"]["visitors_session"], "user_id");
			if(! empty($user_ids))
			{
				$this->cache["prepare"]["users"] = DB::query_fetch_key("SELECT * FROM {users} WHERE id IN (%s)", implode(', ', $user_ids), "id");
			}
		}

		$text = '<div'.(! empty($var["class"]) ? ' class="'.$var["class"].'"' : '').'>';
		$text .= '<a href="'.$this->diafan->get_base_link($row).'" title="'.$this->diafan->_("Редактировать").' ('.$row["id"].')">';
		$row["user_id"] = (int) $row["user_id"];
		if($row["user_id"])
		{
			if(! empty($this->cache["prepare"]["users"][$row["user_id"]]))
			{
				$text .= $this->cache["prepare"]["users"][$row["user_id"]]["fio"]; // авторизованный пользователь
			}
			else
			{
				$text .= $this->diafan->_("Посетитель")." (".$row["user_id"].")"; // авторизованный пользователь
			}
		}
		elseif($row["status"] == 1)
		{
			$text .= $this->diafan->_('гость'); // неавторизованный гость
		}
		else
		{
			if($row["search_bot"])
			{
				$text .= $row["search_bot"]; // поисковый бот
			}
			else $text .= $this->diafan->_('бот'); // неизвестный бот
		}
		$text .= '</a>';
		if(! empty($row["user_agent"]))
		{
				$text .= '<div class="categories" title="'.str_replace('"', '&quot;', $row["user_agent"]).'">'.$this->diafan->short_text($row["user_agent"], 30).'</div>';
		}
		$text .= '</div>';
		return $text;
	}

	/**
	 * Выводит тип пользователя в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_role($row, $var)
	{
		if(! isset($this->cache["prepare"]["visitors_session"]))
		{
			$this->cache["prepare"]["visitors_session"] = array();
			if(! empty($this->diafan->rows_id))
			{
				$this->cache["prepare"]["visitors_session"] = DB::query_fetch_key("SELECT * FROM {visitors_session} WHERE id IN ("
					.substr(str_repeat(",'%s'", count($this->diafan->rows_id)), 1)
					.") ORDER BY master_id ASC, slave_id ASC", $this->diafan->rows_id, "id");
			}
		}
		if(! isset($this->cache["prepare"]["users_role"]))
		{
			$this->cache["prepare"]["users_role"] = array();
			$role_ids = $this->diafan->array_column($this->cache["prepare"]["visitors_session"], "role_id");
			if(! empty($role_ids))
			{
				$this->cache["prepare"]["users_role"] = DB::query_fetch_key("SELECT id, [name] FROM {users_role} WHERE id IN (%s)", implode(', ', $role_ids), "id");
			}
		}

		$text = '<div'.(! empty($var["class"]) ? ' class="'.$var["class"].'"' : '').'>';
		if($row["status"] == 0)
		{
			if($row["search_bot"] != "")
			{
				$text .= ' <span style="color: green;">'.$this->diafan->_("поисковый бот").'</span>'; // поисковый бот
			}
			else
			{
				$text .= ' <span style="color: gray;">'.$this->diafan->_("неизвестный бот").'</span>'; // бот
			}
		}
		elseif($row["user_id"] == 0)
		{
			$text .= $this->diafan->_("неавторизованный"); // неавторизованный посетитель
		}
		else
		{
			if(! empty($this->cache["prepare"]["users_role"][$row["role_id"]]))
			{
				$text .= ' <span style="color: green;">'.$this->cache["prepare"]["users_role"][$row["role_id"]]["name"].'</span>';
			}
			else
			{
				$text .= $this->diafan->_("неопределенный"); // неопределенный посетитель
			}
		}
		$text .= '</div>';
		return $text;
	}

	/**
	 * Выводит статус пользователя (на сайте) в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_session($row, $var)
	{
		if(! isset($this->cache["prepare"]["visitors_url_count"]))
		{
			$this->cache["prepare"]["visitors_url_count"] = array();
			if(! empty($this->diafan->rows_id))
			{
				$this->cache["prepare"]["visitors_url_count"] = DB::query_fetch_key("SELECT visitors_session_id, COUNT(*) AS `count` FROM {visitors_url} WHERE visitors_session_id IN ("
					.substr(str_repeat(",'%s'", count($this->diafan->rows_id)), 1)
					.") AND is_admin='0' GROUP BY visitors_session_id", $this->diafan->rows_id, "visitors_session_id");
			}
		}

		$views_count = 0;
		if(! empty($this->cache["prepare"]["visitors_url_count"][$row["id"]]['count']))
		{
			$views_count = $this->cache["prepare"]["visitors_url_count"][$row["id"]]['count'];
		}

		$text = '<div'.(! empty($var["class"]) ? ' class="'.$var["class"].'"' : '').'>';
		$delay = $row["status"] == 1 ? self::$delay_activity_user : self::$delay_activity_bot;
		if($row["timestamp"] >= time() - $delay && substr($row["session_id"], 0, 1) != '_')
		{
			$text .= ' <span style="color: #ffffff; background-color: red; padding: 0px 5px; ">'.$this->diafan->_('на сайте').'</span>';
		}
		else
		{
			$text .= ' <span>'.date('d.m.Y H:i', $row["timestamp"]).'</span>';
		}
		if($views_count > 0)
		{
			$text .= '<div class="categories">'.$this->diafan->_('Просмотры').': '.$views_count.'</div>';
		}
		else
		{
			$text .= '<div class="categories">'.$this->diafan->_('Просмотры').': '.$this->diafan->_('нет').'</div>';
		}
		$text .= '</div>';
		return $text;
	}

	/**
	 * Выводит IP пользователя в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_ip($row, $var)
	{
		$text = '<div'.(! empty($var["class"]) ? ' class="'.$var["class"].'"' : '').' style="color: gray;"><nobr>';
		$text .= $row["hostname"];
		$text .= '</nobr></div>';
		return $text;
	}

	/**
	 * Выводит URL страницы в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_url($row, $var)
	{
		if(! isset($this->cache["prepare"]["visitors_url"]))
		{
			$this->cache["prepare"]["visitors_url"] = array();
			if(! empty($this->diafan->rows_id))
			{
				$this->cache["prepare"]["visitors_url"] = DB::query_fetch_key("SELECT * FROM {visitors_url} WHERE visitors_session_id IN ("
					.substr(str_repeat(",'%s'", count($this->diafan->rows_id)), 1)
					.") ORDER BY timeedit ASC, master_id ASC, slave_id ASC", $this->diafan->rows_id, "visitors_session_id");
			}
		}
		if(! isset($this->cache["prepare"]["site"]))
		{
			$this->cache["prepare"]["site"] = array();
			if(! empty($this->cache["prepare"]["visitors_url"]))
			{
				$site_ids = array();
				foreach($this->cache["prepare"]["visitors_url"] as $value)
				{
					if($value["is_admin"] == 1 || $value["site_id"] == 0) continue;
					$site_ids[] = $value["site_id"];

				}
				if(! empty($site_ids))
				{
					$this->cache["prepare"]["site"] = DB::query_fetch_key("SELECT id, [name] FROM {site} WHERE id IN (%s)", implode(",", $site_ids), "id");
				}
			}
		}

		$text = '<div'.(! empty($var["class"]) ? ' class="'.$var["class"].'"' : '').'>';
		if(! empty($this->cache["prepare"]["visitors_url"][$row["id"]]))
		{
			$name = '';
			if($this->cache["prepare"]["visitors_url"][$row["id"]]["is_admin"] == 0)
			{
				if(! empty($this->cache["prepare"]["site"][$this->cache["prepare"]["visitors_url"][$row["id"]]["site_id"]]))
				{
					$name = $this->cache["prepare"]["site"][$this->cache["prepare"]["visitors_url"][$row["id"]]["site_id"]]["name"];
				}
			}
			else
			{
				if(! empty($this->cache["prepare"]["visitors_url"][$row["id"]]["module_name"]))
				{
					$name = $this->diafan->_('Модуль') .': '. $this->cache["prepare"]["visitors_url"][$row["id"]]["module_name"];
				}
			}

			$title = $url = ''; $rewrite = $this->cache["prepare"]["visitors_url"][$row["id"]]["rewrite"];
			if($this->cache["prepare"]["visitors_url"][$row["id"]]["is_admin"])
			{
				$title = $this->diafan->_('Административная часть сайта');
				$url = BASE_PATH_HREF.($rewrite == '/' ? '' : $rewrite);
			}
			else
			{
				$title = $this->diafan->_('Общая часть сайта');
				$url = BASE_PATH.($rewrite == '/' ? '' : $rewrite);
			}
			$text .= '<a href="'.$url.'" title="'.$title.'">'.(! empty($name) ? $name : $url).'</a>';
		}
		$text .= '</div>';
		return $text;
	}

	/**
	 * Поиск по полю "Все авторизованные"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_user($row)
	{
		$where = $query = '';
		if(! empty($_GET["filter_user"]) || ! empty($_GET["filter_visitor"]))
		{
			$query .= "`status`='1'";
		}
		if(! empty($_GET["filter_user"]) && empty($_GET["filter_visitor"]))
		{
			$query .= " AND e.user_id>0";
		}
		if(empty($_GET["filter_user"]) && ! empty($_GET["filter_visitor"]))
		{
			$query .= " AND e.user_id=0";
		}
		if(! empty($query))
		{
			$where .= "(".$query.")";
			$query = '';
		}

		if(! empty($_GET["filter_search_bot"]) || ! empty($_GET["filter_bot"]))
		{
			$query .= "e.user_id=0 AND `status`='0'";
		}
		if(! empty($_GET["filter_search_bot"]) && empty($_GET["filter_bot"]))
		{
			$query .= " AND search_bot<>''";
		}
		if(empty($_GET["filter_search_bot"]) && ! empty($_GET["filter_bot"]))
		{
			$query .= " AND search_bot=''";
		}
		if(! empty($query))
		{
			$where = (! empty($where) ? "(" : "").$where.(! empty($where) ? " OR " : "")."(".$query.")".(! empty($where) ? ")" : "");
			$query = '';
		}

		if(! empty($where))
		{
			$this->diafan->where .= " AND ".$where;
			$where = '';
		}

		if(empty($_GET["filter_user"]))
		{
			return;
		}
		$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_user=1';
		return 1;
	}

	/**
	 * Поиск по полю "Все гости"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_visitor($row)
	{
		if(empty($_GET["filter_visitor"]))
		{
			return;
		}
		$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_visitor=1';
		return 1;
	}

	/**
	 * Поиск по полю "Все поисковые боты"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_search_bot($row)
	{
		if(empty($_GET["filter_search_bot"]))
		{
			return;
		}
		$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_search_bot=1';
		return 1;
	}

	/**
	 * Поиск по полю "Все спам-боты"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_bot($row)
	{
		if(empty($_GET["filter_bot"]))
		{
			return;
		}
		$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_bot=1';
		return 1;
	}

	/**
	 * Поиск по полю "Искать по названию пользователя"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_user_name($row)
	{
		if(empty($_GET["filter_user_name"]))
		{
			return;
		}
		$user_name = $this->diafan->filter($_GET, "sql", "filter_user_name");
		$this->diafan->where .= " AND (IF(e.user_id>0, u.fio, e.search_bot)) LIKE '%".$user_name."%'";
		$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_user_name=1';
		return $_GET["filter_user_name"];
	}

	/**
	 * Генерирует форму редактирования/добавления элемента
	 *
	 * @return void
	 */
	public function edit()
	{
		if(_LANG != $this->diafan->_languages->admin)
		{
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/visitor/');
		}
		echo '<div class="commentary">'.sprintf($this->diafan->_('Бета-версия модуля. Замечания отправляйте в %sТехническую поддержку%s DIAFAN.CMS.'), '<a href="https://user.diafan.ru/support/">', '</a>').'</div>';

		if(! $this->diafan->_visitors->counter_is_enable())
		{
			echo '<div class="error">'.sprintf($this->diafan->_('Требуется активировать ведение Статистики cms в %sнастройках%s модуля.'), '<a href="'.BASE_PATH_HREF.'visitors/config/">', '</a>').'</div>';
		}

		echo $this->important_title;

		echo parent::edit();
	}

	/**
	 * Редактирование поля "Посетитель"
	 * @return void
	 */
	public function edit_variable_user()
	{
		$user_id = $this->diafan->values('user_id');
		if(! isset($this->cache["prepare"]["users"]))
		{
			$this->cache["prepare"]["users"] = array();
			if(! empty($user_id))
			{
				$this->cache["prepare"]["users"] = DB::query_fetch_key("SELECT * FROM {users} WHERE id=%d", $user_id, "id");
			}
		}

		$this->diafan->value = '';
		if(! empty($user_id))
		{
			if(! empty($this->cache["prepare"]["users"][$user_id]))
			{
				$this->diafan->value = $this->cache["prepare"]["users"][$user_id]["fio"].' ('.$this->cache["prepare"]["users"][$user_id]["name"].')'; // авторизованный пользователь
				/*'<a href="'.BASE_PATH_HREF.'users/edit'.$user_id.'/">'.'</a>'*/

			}
			else
			{
				$this->diafan->value = $this->diafan->_("Посетитель")." (".$row["user_id"].")"; // авторизованный пользователь
			}
		}
		elseif($this->diafan->values('status') == 1)
		{
			$this->diafan->value = $this->diafan->_('гость'); // неавторизованный гость
		}
		else
		{
			$search_bot = $this->diafan->values('search_bot');
			if($search_bot)
			{
				$this->diafan->value = $search_bot; // поисковый бот
			}
			else $this->diafan->value = $this->diafan->_('бот'); // неизвестный бот
		}

		$this->diafan->show_table_tr(
			'text',
			$this->diafan->key,
			strip_tags($this->diafan->value),
			$this->diafan->variable_name(),
			$this->diafan->help(),
			$this->diafan->variable_disabled(),
			$this->diafan->variable('', 'maxlength'),
			$this->diafan->variable('', 'select'),
			$this->diafan->variable('', 'select_db'),
			$this->diafan->variable('', 'depend'),
			$this->diafan->variable('', 'attr')
		);
	}

	/**
	 * Редактирование поля "Тип пользователя"
	 * @return void
	 */
	public function edit_variable_role()
	{
		$user_id = $this->diafan->values('user_id');
		$role_id = $this->diafan->values('role_id');
		if(! isset($this->cache["prepare"]["users_role"]))
		{
			$this->cache["prepare"]["users_role"] = array();
			if(! empty($role_id))
			{
				$this->cache["prepare"]["users_role"] = DB::query_fetch_key("SELECT id, [name] FROM {users_role} WHERE id=%d", $role_id, "id");
			}
		}

		$this->diafan->value = '';
		if($this->diafan->values("status") == 0)
		{
			if($this->diafan->values("search_bot") != "")
			{
				$this->diafan->value = $this->diafan->_("поисковый бот"); // поисковый бот
			}
			else
			{
				$this->diafan->value = $this->diafan->_("неизвестный бот"); // бот
			}
		}
		elseif($user_id == 0)
		{
			$this->diafan->value = $this->diafan->_("неавторизованный"); // неавторизованный посетитель
		}
		else
		{
			if(! empty($this->cache["prepare"]["users_role"][$role_id]))
			{
				$this->diafan->value = $this->cache["prepare"]["users_role"][$role_id]["name"];
			}
			else
			{
				$this->diafan->value = $this->diafan->_("неопределенный"); // неопределенный посетитель
			}
		}

		$this->diafan->show_table_tr(
			'text',
			$this->diafan->key,
			strip_tags($this->diafan->value),
			$this->diafan->variable_name(),
			$this->diafan->help(),
			$this->diafan->variable_disabled(),
			$this->diafan->variable('', 'maxlength'),
			$this->diafan->variable('', 'select'),
			$this->diafan->variable('', 'select_db'),
			$this->diafan->variable('', 'depend'),
			$this->diafan->variable('', 'attr')
		);
	}

	/**
	 * Редактирование поля "Дата и время посещения"
	 * @return void
	 */
	public function edit_variable_timeedit()
	{
		$date = $this->diafan->values('user_id');
		$delay = $this->diafan->values('status') == 1 ? self::$delay_activity_user : self::$delay_activity_bot;
		if($this->diafan->values('timestamp') >= time() - $delay && substr($this->diafan->values('session_id'), 0, 1) != '_')
		{
			$this->diafan->value = $this->diafan->_('на сайте');
		}
		else
		{
			$this->diafan->value = date('d.m.Y H:i', $this->diafan->values('timestamp'));
		}

		$this->diafan->show_table_tr(
			'text',
			$this->diafan->key,
			strip_tags($this->diafan->value),
			$this->diafan->variable_name(),
			$this->diafan->help(),
			$this->diafan->variable_disabled(),
			$this->diafan->variable('', 'maxlength'),
			$this->diafan->variable('', 'select'),
			$this->diafan->variable('', 'select_db'),
			$this->diafan->variable('', 'depend'),
			$this->diafan->variable('', 'attr')
		);
	}

	/**
	 * Редактирование поля "Хронология"
	 * @return void
	 */
	public function edit_variable_history()
	{
		$id = $this->diafan->values('id');
		if(! isset($this->cache["prepare"]["visitors_url"]))
		{
			$this->cache["prepare"]["visitors_url"] = array();
			if(! empty($id))
			{
				$this->cache["prepare"]["visitors_url"] = DB::query_fetch_key("SELECT * FROM {visitors_url} WHERE visitors_session_id ='%h' ORDER BY timeedit DESC, master_id DESC, slave_id DESC LIMIT 1000", $id, "id");
			}
		}
		if(! isset($this->cache["prepare"]["site"]))
		{
			$this->cache["prepare"]["site"] = array();
			if(! empty($this->cache["prepare"]["visitors_url"]))
			{
				$site_ids = array();
				foreach($this->cache["prepare"]["visitors_url"] as $value)
				{
					if($value["is_admin"] == 1 || $value["site_id"] == 0) continue;
					$site_ids[] = $value["site_id"];

				}
				if(! empty($site_ids))
				{
					$this->cache["prepare"]["site"] = DB::query_fetch_key("SELECT id, [name] FROM {site} WHERE id IN (%s)", implode(",", $site_ids), "id");
				}
			}
		}

		$this->diafan->value = '';
		foreach ($this->cache["prepare"]["visitors_url"] as $key => $value)
		{
			$date = date("Y-m-d H:i:s", $value["timeedit"]);

			$url = ''; $rewrite = $value["rewrite"];
			if($value["is_admin"])
			{
				$url = BASE_PATH_HREF.($rewrite == '/' ? '' : $rewrite).$value["query"];
			}
			else
			{
				$url = BASE_PATH.($rewrite == '/' ? '' : $rewrite).$value["query"];
			}

			$this->diafan->value .= $date.'&#8195;'.'&#8195;'.$url."\n";
		}

		$this->diafan->show_table_tr(
			'textarea',
			$this->diafan->key,
			strip_tags($this->diafan->value),
			$this->diafan->variable_name(),
			$this->diafan->help(),
			$this->diafan->variable_disabled(),
			$this->diafan->variable('', 'maxlength'),
			$this->diafan->variable('', 'select'),
			$this->diafan->variable('', 'select_db'),
			$this->diafan->variable('', 'depend'),
			$this->diafan->variable('', 'attr')
		);
	}
}
