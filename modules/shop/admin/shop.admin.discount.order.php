<?php
/**
 * Редактирование скидок
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

/**
 * Shop_admin_discount_order
 */
class Shop_admin_discount_order extends Frame_admin
{
	/**
	 * @var string таблица в базе данных
	 */
	public $table = 'shop_discount';

	/**
	* @var string часть SQL-запроса - дополнительные столбцы
	*/
	public $fields = ", c.coupon AS discount_coupon, c.count_use AS discount_coupon_count_use, c.used AS discount_coupon_used";

	/**
	 * @var string часть SQL-запроса - соединение с таблицей
	 */
	public $join = " LEFT JOIN {shop_discount_coupon} AS c ON e.id=c.discount_id AND c.trash='0'";

	/**
	 * @var string SQL-условия для списка
	 */
	// public $where = " AND (threshold>0 OR threshold_goods>0 OR threshold_count>0 OR delivery_id>0 OR payment_id>0)";
	public $where = " AND `variable`='order'";

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'variable' => array(
				'type' => 'select',
				'name' => 'Тип скидки',
				'select' => array(
					// "goods" => "Постоянные скидки на товары",
					// "users" => "Скидки для пользователей",
					"order" => "Скидки на заказы",
				),
				// 'no_save' => true,
				'attr' => 'style="display:none"',
			),
			// 'title1' => array(
			// 	'type' => 'title',
			// 	'name' => 'Размер скидки',
			// ),
			'type' => array(
				'type' => 'select',
				'name' => 'Тип скидки',
				'select' => array(
					"discount" => "% Проценты",
					"deduction" => "Сумма",
				),
				'no_save' => true,
			),
			'discount' => array(
				'type' => 'floattext',
				'name' => 'Скидка в процентах (%)',
				'help' => 'Если заполнено это поле, скидка будет считаться в процентах от исходной цены.',
				'depend' => 'type=discount',
			),
			'deduction' => array(
				'type' => 'floattext',
				'name' => 'Скидка в виде фиксированной суммы',
				'help' => 'Если заполнено это поле, скидка будет вычитаться от исходной цены в виде фиксированной суммы.',
				'depend' => 'type=deduction',
			),
			'date_period' => array(
				'type' => 'datetime',
				'name' => 'Период действия скидки',
				'help' => 'Если выбрать период действия скидки, она будет применяться только в указанное время.',
			),
			'threshold_combine' => array(
				'type' => 'select',
				'name' => 'Комбинировать с другими скидками',
				'help' => 'Скидка будет применяться в корзине поверх других скидок.',
				'select' => array(
					"1" => "Да",
					"0" => "Нет",
				),
				'depend' => 'variable=order',
			),
			'act' => array(
				'type' => 'checkbox',
				'name' => 'Активировать скидку',
				'help' => 'Если отметить, скидка будет опубликована на сайте и примениться ко всем товарам, отвечающим условиям выше.',
				'default' => true,
			),
			'title_if' => array(
				'type' => 'title',
			 	'name' => 'Условия применения скидки',
			),
			// 'title2' => array(
			// 	'type' => 'title',
			// 	'name' => 'На отдельные категории и товары',
			// 	'depend' => 'variable=goods|variable=users',
			// ),
			'amount' => array(
				'type' => 'floattext',
				'name' => 'Cкидка действует на товары дороже',
				'help' => 'Cкидка будет применяться только к тем товарам, которые дороже указанной суммы.',
				'depend' => 'variable=goods|variable=users',
			),
			'object' => array(
				'type' => 'function',
				'name' => 'Объект',
				'help' => 'Товары и категории, на которые распространяется скидка. Если не указаны, то скидка считается общей.',
				'depend' => 'variable=goods|variable=users',
			),
			// 'title3' => array(
			// 	'type' => 'title',
			// 	'name' => 'На весь заказ',
			// 	'depend' => 'variable=order',
			// ),
			'threshold' => array(
				'type' => 'numtext',
				'name' => 'Скидка действует от общей суммы заказа',
				'help' => 'Скидка начнет действовать когда пользователь наберет в корзину товаров на указанную сумму. Если заполнено, то скидка применяется только в корзине товаров на общую сумму.',
				'depend' => 'variable=order',
			),
			'threshold_cumulative' => array(
				'type' => 'numtext',
				'name' => 'Накопительная скидка от суммы ранее оплаченных заказов',
				'help' => 'Скидка начнет действовать когда пользователь оплатит товаров на указанную сумму. Если заполнено, то скидка применяется только в корзине товаров на общую сумму.',
				'depend' => 'variable=users',
			),
			'threshold_goods' => array(
				'type' => 'numtext',
				'name' => 'Скидка действует от общего количества позиций в заказе',
				'help' => 'Скидка начнет действовать, когда пользователь наберет в корзину товаров на указанное количество позиций. Если заполнено, то скидка применяется только в корзине товаров на общее количество позиций.',
				'depend' => 'variable=order',
			),
			'threshold_count' => array(
				'type' => 'numtext',
				'name' => 'Скидка действует от общего количества товаров в заказе',
				'help' => 'Скидка начнет действовать, когда пользователь наберет в корзину товаров на указанное количество. Если заполнено, то скидка применяется только в корзине товаров на общее количество.',
				'depend' => 'variable=order',
			),
			'delivery_id' => array(
				'type' => 'select',
				'name' => 'При выборе службы доставки',
				'help' => 'Скидка будет применяться к определённой службе доставки.',
				'select_db' => array(
					'table' => 'shop_delivery',
					'name' => '[name]',
					'empty' => 'Все',
					'where' => "trash='0'",
					'order' => 'sort ASC',
				),
				'depend' => 'variable=order',
			),
			'payment_id' => array(
				'type' => 'select',
				'name' => 'При выборе метода оплаты',
				'help' => 'Скидка будет применяться к определённому методу оплате.',
				'select_db' => array(
					'table' => 'payment',
					'name' => '[name]',
					'empty' => 'Все',
					'where' => "trash='0'",
					'order' => 'sort ASC',
				),
				'depend' => 'variable=order',
			),

			// 'title4' => array(
			// 	'type' => 'title',
			// 	'name' => 'Купоны',
			// 	'depend' => 'variable=order',
			// ),
			'coupon' => array(
				'type' => 'function',
				'name' => 'При примененинии кода купона',
				'help' => 'Пользователь должен активировать на сайте этот код, чтобы получить скидку.',
				'depend' => 'variable=users|variable=order',
			),
			// 'title5' => array(
			// 	'type' => 'title',
			// 	'name' => 'Для отдельных пользователей',
			// 	'depend' => 'variable=users',
			// ),
			'no_auth' => array(
				'type' => 'select',
				'name' => 'Скидка только для неавторизованных пользователей',
				'help' => 'Скидка будет применяться только для неавторизованных пользователей.',
				'select' => array(
					"0" => "Нет",
					"1" => "Да",
				),
				'depend' => 'variable=users',
			),
			'role_id' => array(
				'type' => 'select',
				'name' => 'Группы покупателей',
				'help' => 'Скидка будет применяться ко всей группе пользователей.',
				'select_db' => array(
					'table' => 'users_role',
					'name' => '[name]',
					'empty' => 'Все',
					'where' => "trash='0'",
					'order' => 'sort ASC',
				),
				'depend' => 'variable=users,no_auth=0',
			),
			'person' => array(
				'type' => 'function',
				'name' => 'ID',
				'help' => 'Если есть пользователи, использующие скидку, то скидка считается персонализированной и другим пользователям не применяется.',
				'depend' => 'variable=users,no_auth=0',
			),
			// 'title1' => array(
			// 	'type' => 'title',
			// 	'name' => 'Создать скидку на заказы',
			// ),
			'text' => array(
				'type' => 'textarea',
				'name' => 'Комментарий для администратора',
				'help' => 'Поле видно только администратору.',
			),
		),
	);

	/**
	 * @var array названия табов
	 */
	public $tabs_name = array(
		'main' => 'Размер скидки',
		'goods' => 'На отдельные категории и товары',
		'order' => 'На весь заказ',
		'coupon' => 'Купоны',
		'person' => 'Для отдельных пользователей',
		'actdis' => 'Активировать скидку',
	);

	/**
	 * @var array поля в списка элементов
	 */
	public $variables_list = array (
		'checkbox' => '',
		'name' => array(
			'variable' => 'discount',
		),
		'deduction' => array(
			'sql' => true,
		),
		'text' => array(
			'type' => 'text',
			'sql' => true,
		),
		'actions' => array(
			'act' => true,
			'trash' => true,
		),
	);

	/**
	 * @var array поля для фильтра
	 */
	public $variables_filter = array (
		'date_start' => array(
			'name' => 'Искать по дате',
			'type' => 'datetime',
		),
		'date_finish' => array(
			'type' => 'datetime',
		),
	);

	/**
	 * Подготавливает конфигурацию модуля
	 * @return void
	 */
	public function prepare_config()
	{
		if(! in_array('payment', $this->diafan->installed_modules)) {
			$this->diafan->variable_unset("payment_id");
		}
	}

	/**
	 * Выводит ссылку на добавление
	 * @return void
	 */
	public function show_add()
	{
		$this->diafan->addnew_init('Добавить скидку');
	}

	/**
	 * Выводит список заказов
	 * @return void
	 */
	public function show()
	{
		$this->show_content_h1();
		$this->show_variable_discount();

		$this->diafan->list_row();
	}

	/**
	 * Выводит заголовок контента
	 *
	 * @return void
	 */
	private function show_content_h1()
	{
		if($this->diafan->_admin->name != $this->diafan->_admin->title_module)
		{
			echo '<span class="head-box__unit">'.$this->diafan->_("Скидки").'</span>';
		}
		$this->diafan->_admin->name = 'Скидки на заказы';
	}

	/**
	 * Выводит выбор вариации скидок
	 *
	 * @return void
	 */
	private function show_variable_discount()
	{
		$url = BASE_PATH_HREF.'shop/discount/';
		$site = $this->diafan->_route->site ? 'site'.$this->diafan->_route->site.'/' : '';
		echo '
		<div class="tabs" id="mode_express">
			<a href="'.$url.$site.'" class="tabs__item">'.$this->diafan->_('На товары').'</a>
			<a href="'.$url.'users/'.$site.'" class="tabs__item">'.$this->diafan->_('Для покупателей').'</a>
			<a href="'.$url.'order/'.$site.'" class="tabs__item tabs__item_active">'.$this->diafan->_('На заказы').'</a>
		</div>';
	}

	/**
	 * Формирует основную ссылку для элемента в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_name($row, $var)
	{
		if(! empty($row["deduction"]))
		{
			$name = $row["deduction"].' '.$this->diafan->configmodules("currency", "shop");
		}
		else
		{
			$name = $row["discount"].' %';
		}

		$link = '<div class="name"><a href="';
		if ($this->diafan->_users->roles('init', $this->diafan->_admin->rewrite))
		{

			$link .= $this->diafan->_route->current_admin_link().'edit'.$row["id"].'/'.$this->diafan->get_nav.'" title="'.$this->diafan->_('Редактировать').' ('.$row["id"].')';
		}
		else
		{
			$link .= '#';
		}
		$link .= '" name="'.$row["id"].'">'.$name.'</a>';
		$link .= $this->diafan->list_variable_date_period($row, array());
		$link .= '</div>';
		return $link;
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
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/shop/discount/');
		}
		$this->show_content_h1();
		$this->show_variable_discount();
		echo parent::edit();
		$this->diafan->_admin->js_view[] = 'modules/'.$this->diafan->_admin->module.'/admin/js/'.$this->diafan->_admin->module.'.admin.discount.edit.js';
	}

	/**
	 * Редактирование поля "Тип скидки"
	 * @return void
	 */
	public function edit_variable_type()
	{
		if($this->diafan->values("deduction"))
		{
			$this->diafan->value = "deduction";
		}
		else
		{
			$this->diafan->value = "discount";
		}
		$this->diafan->show_table_tr(
				$this->diafan->type,
				$this->diafan->key,
				$this->diafan->value,
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
	 * Редактирование поля "Блок условий"
	 * @return void
	 */
	public function edit_variable_title_if()
	{
		echo '<div class="unit">
			<h2>
				'.$this->diafan->_('Условия применения скидки').'
			</h2>';
			echo '<p style="background-color: #f5f3f3; padding: 1em;">'.$this->diafan->_('Все условия, заполняемые ниже применяются в совокупности, как логический оператор "И". Например, если выставить категории, производителей и характеристику, должны совпасть все три условия, чтобы скидка применилась. Чтобы сделать условие "ИЛИ", создавайте отдельные скидки.').'</p>';
		echo '</div>';

	}

	/**
	 * Редактирование поля "Объект"
	 * @return void
	 */
	public function edit_variable_object()
	{
		$marker = "&nbsp;&nbsp;";

		// категории
		$cs = DB::query_fetch_all("SELECT id, [name], parent_id FROM {shop_category} WHERE trash='0' ORDER BY sort ASC, id ASC LIMIT 1000");
		if(count($cs) < 1000)
		{
			foreach($cs as $c)
			{
				$cats[$c["parent_id"]][] = $c;
			}
		}
		$cat_values = array();
		if (! $this->diafan->is_new)
		{
			$cat_values = DB::query_fetch_value("SELECT cat_id FROM {shop_discount_object} WHERE discount_id='%d' AND cat_id>0", $this->diafan->id, "cat_id");
		}

		// производители
		$cs = DB::query_fetch_all("SELECT id, [name] FROM {shop_brand} WHERE trash='0' ORDER BY sort ASC, id ASC LIMIT 1000");
		if(count($cs) < 1000)
		{
			$brands[0] = $cs;
		}
		$brand_values = array();
		if (! $this->diafan->is_new)
		{
			$brand_values = DB::query_fetch_value("SELECT brand_id FROM {shop_discount_object} WHERE discount_id='%d' AND brand_id>0", $this->diafan->id, "brand_id");
		}

		// характеристи - multiple, select, checkbox
		$cs = DB::query_fetch_all("SELECT id, [name], '%d' AS parent_id FROM {shop_param} WHERE `type` IN('multiple', 'select', 'checkbox') AND required='1' AND trash='0' ORDER BY sort ASC, id ASC LIMIT 1000", 0);
		if($cs && count($cs) < 1000)
		{
			foreach($cs as $c)
			{
				$csv = DB::query_fetch_all("SELECT id, [name], '%d' AS parent_id FROM {shop_param_select} WHERE param_id=%d AND trash='0' ORDER BY sort ASC, id ASC LIMIT 1000", $c["id"], $c["id"]);
				if($csv && count($csv) < 1000)
				{
					$c["id"] = 'param'.$c["id"];
					$params['param'.$c["parent_id"]][] = $c;
					foreach($csv as $cv)
					{
						$params['param'.$cv["parent_id"]][] = $cv;
					}
				}
			}
		}
		$param_values = array();
		if (! $this->diafan->is_new)
		{
			$param_values = DB::query_fetch_value("SELECT param_value FROM {shop_discount_object} WHERE discount_id='%d' AND param_value>0", $this->diafan->id, "param_value");
		}

		Custom::inc('modules/shop/admin/shop.admin.view.php');

		$class = '';
		$attr = $this->diafan->variable('', 'attr');
		$depend = $this->diafan->variable('', 'depend');
		if($depend)
		{
			$attr .= ' depend="'.$depend.'"';
			$class = "depend_field";
		}

		if(! empty($cats))
		{
			echo '
			<div class="unit'.($class ? ' '.$class : '').'"'.($attr ? ' '.$attr : '').'>
				<div class="infofield">
					'.$this->diafan->_('Категории').'
				</div>
				<select name="cat_ids[]" multiple="multiple" size="11">
				<option value="all"'.(empty($cat_values) ? ' selected' : '').'>'.$this->diafan->_('Все').'</option>'
				.$this->diafan->get_options($cats, $cats[0], $cat_values).'
				</select>
			</div>';
		}
		if(! empty($brands))
		{
			echo '
			<div class="unit'.($class ? ' '.$class : '').'"'.($attr ? ' '.$attr : '').'>
				<div class="infofield">
					'.$this->diafan->_('Производители').'
				</div>
				<select name="brand_ids[]" multiple="multiple" size="11">
				<option value="all"'.(empty($brand_values) ? ' selected' : '').'>'.$this->diafan->_('Все').'</option>'
				.$this->diafan->get_options($brands, $brands[0], $brand_values).'
				</select>
			</div>';
		}
		echo '<div class="unit'.($class ? ' '.$class : '').'"'.($attr ? ' '.$attr : '').'>
			<div class="infofield">
				'.$this->diafan->_('Отдельные товары').'
				'.$this->diafan->help('Вы можете назначить скидку только на некоторые конкретные товары.').'
			</div>';
			echo '<div class="rel_elements">';
			if (! $this->diafan->is_new)
			{
				$view = new Shop_admin_view($this->diafan);
				echo $view->discount_goods($this->diafan->id);
			}
			echo '</div>
			<a href="javascript:void(0)" class="rel_module_plus" title="'.$this->diafan->_('Добавить').'"><i class="fa fa-plus-square"></i> '.$this->diafan->_('Добавить').'</a>';
			echo '<p>'.$this->diafan->_('Чтобы назначить скидку отдельному товару, Вы также можете просто заполнить поле старая цена при его редактировании').'</p>';
		echo '</div>';
		if(! empty($params))
		{
			echo '
			<div class="unit'.($class ? ' '.$class : '').'"'.($attr ? ' '.$attr : '').'>
				<div class="infofield">
					'.$this->diafan->_('Значения характеристик').'
				</div>
				<p>
					'.$this->diafan->_('Обратите внимание, что скидка, зависящая от значения характеристики, действует только в том случае, если эта характеристика доступна к выбору при заказе и определена в карточке товара, как влияющая на цену товара').'
				</p>
				<select name="param_ids[]" multiple="multiple" size="11">
				<option value="all"'.(empty($param_values) ? ' selected' : '').'>'.$this->diafan->_('Все').'</option>'
				.$this->diafan->get_options($params, $params['param' . '0'], $param_values).'
				</select>
			</div>';
		}
	}

	/**
	 * Редактирование поля "Пользователь"
	 * @return void
	 */
	public function edit_variable_person()
	{
		$persons = array();
		if(! $this->diafan->is_new)
		{
			$persons = DB::query_fetch_all("SELECT p.*, CONCAT(u.fio,' (', u.name, ')') AS user FROM {shop_discount_person} AS p"
			." LEFT JOIN {users} AS u ON u.id=p.user_id"
			." WHERE p.discount_id=%d", $this->diafan->id);
		}
		$coupon = $this->diafan->values("coupon");
		$class = '';
		$attr = $this->diafan->variable('', 'attr');
		$depend = $this->diafan->variable('', 'depend');
		if($depend)
		{
			$attr .= ' depend="'.$depend.'"';
			$class = "depend_field";
		}
		echo '
		<div class="unit param_container'.($class ? ' '.$class : '').'"'.($attr ? ' '.$attr : '').'>';
		foreach ($persons as $row)
		{
			echo '<div class="param">
				<input type="hidden" name="person_id[]" value="'.$row["id"].'">
				<div class="infofield">ID'.$this->diafan->help().'</div>';
			if($row["user"])
			{
				echo '<a href="'.BASE_PATH_HREF.'users/edit'.$row["user_id"].'/">'.$row["user"].'</a> ';
			}
				echo '<input type="text" name="person_user_id[]" size="4" value="'.($row["user_id"] ? $row["user_id"] : '').'">
				'.($row["session_id"] ? 'session_id: '.$row["session_id"] : '').'
				'.($row["coupon_id"] && ! empty($coupon[$row["coupon_id"]]) ? ' '.$this->diafan->_('Добавлен по купону').' '.$coupon[$row["coupon_id"]] : '').'
				<input type="hidden" name="person_session_id[]" value="'.$row["session_id"].'">
				<span class="param_actions">
					<a href="javascript:void(0)" action="delete_param" class="delete" confirm="'.$this->diafan->_('Вы действительно хотите удалить запись?').'"><i class="fa fa-close" title="'.$this->diafan->_('Удалить').'"></i></a>
				</span>
			</div>';

		}
		echo '<div class="param">
				<div class="infofield">ID'.$this->diafan->help().'</div>
				<input type="text" name="person_user_id[]" size="4" value="">
				<span class="param_actions">
					<a href="javascript:void(0)" action="delete_param" confirm="'.$this->diafan->_('Вы действительно хотите удалить запись?').'" style="display:none" class="delete"><i class="fa fa-close" title="'.$this->diafan->_('Удалить').'"></i></a>
				</span>
			</div>
			<p><a href="javascript:void(0)" class="param_plus" title="'.$this->diafan->_('Добавить').'"><i class="fa fa-plus-square"></i> '.$this->diafan->_('Добавить').'</a></p>
		</div>';
	}

	/**
	 * Редактирование поля "Код купона"
	 * @return void
	 */
	public function edit_variable_coupon()
	{
		$coupons = array();
		$rs = array();
		if(! $this->diafan->is_new)
		{
			$coupons = DB::query_fetch_all("SELECT * FROM {shop_discount_coupon} WHERE discount_id=%d", $this->diafan->id);
			foreach ($coupons as $row)
			{
				$rs[$row["id"]] = $row["coupon"];
			}
			$this->diafan->values("coupon", $rs, true);
		}
		$class = '';
		$attr = $this->diafan->variable('', 'attr');
		$depend = $this->diafan->variable('', 'depend');
		if($depend)
		{
			$attr .= ' depend="'.$depend.'"';
			$class = "depend_field";
		}
		echo '
		<div class="unit param_container'.($class ? ' '.$class : '').'" id="coupon"'.($attr ? ' '.$attr : '').'>';
		foreach ($coupons as $row)
		{
			echo '<div class="unit param">
				<div class="infofield">'.$this->diafan->_('Код купона').$this->diafan->help().'</div>
				<input type="hidden" name="coupon_id[]" value="'.$row["id"].'">
				<input type="text" name="coupon[]" value="'.$row["coupon"].'">
				<a href="javascript:void(0)" class="coupon_generate">'.$this->diafan->_('сгенерировать').'</a><br>
				<div class="infofield">'.$this->diafan->_('Сколько раз можно использовать купон').'
				'.$this->diafan->help('Скидка становится неактивной, если она исчерпала этот лимит. Если поле не заполнено, ограничение по количеству раз не действует.').'</div>
				<input type="number" name="coupon_count_use[]" size="4" value="'.($row["count_use"] ? $row["count_use"] : '').'">
				'.($row["used"] ? ' '.$this->diafan->_('Использован').': '.$row["used"] : '').'
				<span class="param_actions">
					<a href="javascript:void(0)" action="delete_param" class="delete"  confirm="'.$this->diafan->_('Вы действительно хотите удалить запись?').'"><i class="fa fa-close" title="'.$this->diafan->_('Удалить').'"></i></a>
				</span>
				<h2></h2>
				</div>';

		}
		echo '<div class="unit param">
				<div class="infofield">'.$this->diafan->_('Код купона').$this->diafan->help().'</div>
				<input type="text" name="coupon[]" value="">
				<a href="javascript:void(0)" class="coupon_generate">'.$this->diafan->_('сгенерировать').'</a>
				<div class="infofield">'.$this->diafan->_('Сколько раз можно использовать купон').'
				'.$this->diafan->help('Скидка становится неактивной, если она исчерпала этот лимит. Если поле не заполнено, ограничение по количеству раз не действует.').'</div>
				<input type="text" name="coupon_count_use[]" size="4" value="">
				<span class="param_actions">
					<a href="javascript:void(0)" action="delete_param" class="delete"  confirm="'.$this->diafan->_('Вы действительно хотите удалить запись?').'" style="display:none"><i class="fa fa-close" title="'.$this->diafan->_('Удалить').'"></i></a>
				</span>

			</div>
			<a href="javascript:void(0)" class="param_plus" title="'.$this->diafan->_('Добавить').'"><i class="fa fa-plus-square"></i> '.$this->diafan->_('Добавить купон').'</a>
			<h2></h2>
		</div>';
	}

	/**
	 * Проверка поля "Купон"
	 * @return void
	 */
	public function validate_variable_coupon()
	{
		if(! empty($_POST["coupon"]) && ! $this->diafan->configmodules("discount_code", "shop", $this->diafan->_route->site))
		{
			$coupon_codes = array();
			foreach($_POST["coupon"] as $key => $coupon)
			{
				if(empty($coupon)) continue;
				$coupon_id = ! empty($_POST["coupon_id"][$key]) ? $_POST["coupon_id"][$key] : 0;
				if(DB::query_result("SELECT COUNT(*) FROM {shop_discount_coupon} WHERE coupon='%s' AND id NOT IN(%d, 0)", $coupon, $coupon_id))
				{
					$coupon_codes[] = $coupon;
				}

			}
			if(! empty($coupon_codes))
			{
				$error = $this->diafan->_('Для купонов уже используются следующие коды: %s. Если необходимо использовать несколько купонов с одинаковым кодом активации, то измените настройки модуля.', implode(", ", $coupon_codes));
				$this->diafan->set_error("coupon", $error);
			}
		}
	}

	/**
	 * Сохранение поля "Тип скидки"
	 * @return void
	 */
	public function save_variable_variable()
	{
		$this->diafan->set_query("`variable`='%s'");
		$this->diafan->set_value('order');
	}

	/**
	 * Сохранение поля "Объект"
	 * @return void
	 */
	public function save_variable_object()
	{
		DB::query("DELETE FROM {shop_discount_object} WHERE discount_id=%d AND good_id=0", $this->diafan->id);
		if(! empty($_POST["brand_ids"]) && in_array("all", $_POST["brand_ids"]))
		{
			$_POST["brand_ids"] = array();
		}
		if(! empty($_POST["cat_ids"]) && in_array("all", $_POST["cat_ids"]))
		{
			$_POST["cat_ids"] = array();
		}
		if(! empty($_POST["param_ids"]) && in_array("all", $_POST["param_ids"]))
		{
			$_POST["param_ids"] = array();
		}

		if(! empty($_POST["brand_ids"]))
		{
			foreach ($_POST["brand_ids"] as $id)
			{
				if($id)
				{
					DB::query("INSERT INTO {shop_discount_object} (discount_id, brand_id) VALUES (%d, %d)", $this->diafan->id, $id);
				}
			}
		}
		if(! empty($_POST["cat_ids"]))
		{
			foreach ($_POST["cat_ids"] as $id)
			{
				if($id)
				{
					DB::query("INSERT INTO {shop_discount_object} (discount_id, cat_id) VALUES (%d, %d)", $this->diafan->id, $id);
				}
			}
		}
		if(! empty($_POST["param_ids"]))
		{
			foreach ($_POST["param_ids"] as $id)
			{
				if($id && $id == $this->diafan->filter($id, 'int'))
				{
					DB::query("INSERT INTO {shop_discount_object} (discount_id, param_value) VALUES (%d, %d)", $this->diafan->id, $id);
				}
			}
		}
		if(! DB::query_result("SELECT id FROM {shop_discount_object} WHERE discount_id=%d LIMIT 1", $this->diafan->id))
		{
			DB::query("INSERT INTO {shop_discount_object} (discount_id) VALUES (%d)", $this->diafan->id);
		}
		DB::query("UPDATE {shop_discount} SET act='%d' WHERE id=%d", ! empty($_POST["act"]) ? '1' : '0', $this->diafan->id);
	}

	/**
	 * Сохранение поля "Пользователь"
	 * @return void
	 */
	public function save_variable_person()
	{
		$person = 0;
		if(! empty($_POST["person_user_id"]))
		{
			foreach ($_POST["person_user_id"] as $i => $user_id)
			{
				$user_id = intval($user_id);
				if(! empty($_POST["person_id"][$i]))
				{
					if(! empty($user_id) || ! empty($_POST["person_session_id"][$i]))
					{
						DB::query("UPDATE {shop_discount_person} SET user_id=%d, session_id='%s' WHERE id=%d AND discount_id=%d", $user_id,$_POST["person_session_id"][$i], $_POST["person_id"][$i], $this->diafan->id);
						$id = intval($_POST["person_id"][$i]);
						if($id)
						{
							$ids[] = $id;
						}
					}
				}
				else
				{
					if($user_id)
					{
						$ids[] = DB::query("INSERT INTO {shop_discount_person} (user_id, discount_id) VALUES (%d, %d)", $user_id, $this->diafan->id);
					}
				}
			}
			if(! empty($ids))
			{
				$person = 1;
			}
		}
		DB::query("DELETE FROM {shop_discount_person} WHERE ".(! empty($ids) ? "id NOT IN(".implode(",", $ids).") AND" : "")." discount_id=%d", $this->diafan->id);
		if(! $person && ! empty($_POST["coupon"]))
		{
			foreach ($_POST["coupon"] as $c)
			{
				if(! empty($c))
				{
					$person = 1;
				}
			}
		}
		$this->diafan->set_query("person='%d'");
		$this->diafan->set_value($person);
	}

	/**
	 * Сохранение поля "Группы покупателей"
	 * @return void
	 */
	public function save_variable_role_id()
	{
		$this->diafan->set_query("role_id=%d");
		$this->diafan->set_value($_POST["role_id"]);
	}

	/**
	 * Сохранение поля "Купон"
	 * @return void
	 */
	public function save_variable_coupon()
	{
		if(! empty($_POST["coupon"]))
		{
			foreach ($_POST["coupon"] as $i => $coupon)
			{
				if(! empty($_POST["coupon_id"][$i]))
				{
					if(! empty($coupon))
					{
						DB::query("UPDATE {shop_discount_coupon} SET coupon='%h', count_use=%d WHERE id=%d AND discount_id=%d", $coupon, $_POST["coupon_count_use"][$i], $_POST["coupon_id"][$i], $this->diafan->id);
						$id = intval($_POST["coupon_id"][$i]);
						if($id)
						{
							$ids[] = $id;
						}
					}
				}
				else
				{
					if($coupon)
					{
						$ids[] = DB::query("INSERT INTO {shop_discount_coupon} (coupon, count_use, discount_id) VALUES ('%h', %d, %d)", $coupon, $_POST["coupon_count_use"][$i], $this->diafan->id);
					}
				}
			}
		}
		DB::query("DELETE FROM {shop_discount_coupon} WHERE ".(! empty($ids) ? "id NOT IN(".implode(",", $ids).") AND" : "")." discount_id=%d", $this->diafan->id);
	}

	/**
	 * Пользовательская функция, выполняемая перед редиректом при сохранении скидки
	 *
	 * @return void
	 */
	public function save_redirect()
	{
		// $this->diafan->set_time_limit();
		// TO_DO: $this->diafan->_shop->price_calc(0, $this->diafan->id);
		$this->diafan->_executable->execute(array(
			"module" => "shop",
			"method" => "price_calc",
			"params" => array("good_id" => 0, "discount_id" => $this->diafan->id),
			"text"   => $this->diafan->_('Пересчет цен')
		));
		$this->diafan->set_one_shot(
			'<div class="commentary">'
			.$this->diafan->_('Инициирован %sфоновый процесс пересчёта цен%s.%sПосле его окончания изменения будут отображены на сайте.', '<a href="'.BASE_PATH_HREF.'executable/'.'" target="_blank">', '</a>', '<br>')
			.'</div>'
		);
		parent::__call('save_redirect', array());
	}

	/**
	 * Сопутствующие действия при удалении элемента модуля
	 * @return void
	 */
	public function delete($del_ids)
	{
		$this->diafan->set_time_limit();
		foreach($del_ids as $del_id)
		{
			$this->diafan->_shop->price_calc(0, $del_id);
		}
		$this->diafan->del_or_trash_where("shop_discount_object", "discount_id IN (".implode(",", $del_ids).")");
		$this->diafan->del_or_trash_where("shop_discount_person", "discount_id IN (".implode(",", $del_ids).")");
		$this->diafan->del_or_trash_where("shop_discount_coupon", "discount_id IN (".implode(",", $del_ids).")");
		$this->diafan->del_or_trash_where("shop_price", "discount_id IN (".implode(",", $del_ids).")");
	}
}
