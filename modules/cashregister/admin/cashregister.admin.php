<?php
/**
 * История отправления чеков
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
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

class Cashregister_admin extends Frame_admin
{
	/**
	 * @var string таблица в базе данных
	 */
	public $table = 'shop_cashregister';

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'type' => array(
				'type' => 'select',
				'name' => 'Тип чека',
				'select' => array(
					"presell" => "100% предоплата",
					"sell" => "полная оплата",
					"refund" => "возврат",
				),
			),
			'order_id' => array(
				'type' => 'numtext',
				'name' => 'Номер заказа',
			),
			'external_id' => array(
				'type' => 'text',
				'name' => 'Идентификатор в системе',
				'disabled' => true,
			),
			'master_id' => array(
				'type' => 'datetime',
				'name' => 'Дата и время',
				'no_save' => true,
				'disabled' => true,
				'help' => 'Дата и время создания чека.',
			),
			'status' => array(
				'type' => 'select',
				'name' => 'Статус отправки уведомления',
				'select' => array(
					"0" => '<span class="orange_text" title="Чек ожидает инициализации отправки">Подготовлен</span>',
					"1" => '<span class="green_text" title="Чек отправлен">Отправлен</span>',
					"2" => '<span class="red_text" title="Зарегистрирована ошибка при отправлении чека">Ошибка</span>',
				),
				'help' => 'Состояние и время отправки чека.',
				'no_save' => true,
				'disabled' => true,
			),
			'important' => array(
				'type' => 'checkbox',
				'name' => 'Защищает от второго чека',
				'help' => 'Если отмечено, то при смене сответствующего статуса заказа, не будет повторно выписан чек такого же типа.',
			),
			'error' => array(
				'type' => 'text',
				'name' => 'Отчет об ошибке',
				'no_save' => true,
				'disabled' => true,
				'help' => 'Содержит сведения об ошибке при отправлении.',
			),
			'trace' => array(
				'type' => 'textarea',
				'name' => 'Трассировка отправления',
				'no_save' => true,
				'disabled' => true,
				'height' => 250,
				'help' => 'Содержит сведения о трассировке отправления.',
			),
			'timesent' => array(
				'type' => 'none',
				'disabled' => true,
			),
			'auto' => array(
				'type' => 'none',
			),
		),
		'other_rows' => array (
			'send' => array(
				'type' => 'function',
				'name' => 'Отправить чек',
				'help' => 'Если отмечена, чек будет отправлен.',
				'no_save' => true,
			),
			'timeedit' => array(
				'type' => 'text',
				'name' => 'Время последнего изменения',
				'help' => 'Изменяется после сохранения элемента.',
			),
		),
	);

	/**
	 * @var array поля в списка элементов
	 */
	public $variables_list = array (
		'checkbox' => '',
		'master_id' => array(
			'type' => 'datetime',
			'name' => 'Дата и время',
			'sql' => true,
			'help' => 'Время создания чека.',
		),
		'name' => array(
			'type' => 'select',
			'name' => 'Тип',
			'variable' => 'type',
			'class' => 'postman',
			'select' => array(
				"presell" => "100% предоплата",
				"sell" => "полная оплата",
				"refund" => "возврат",
			),
		),
		'order_id' => array(
			'type' => 'select',
			'name' => 'Заказ',
			'variable' => 'type',
			'sql' => true,
		),
		'adapt' => array(
			'class_th' => 'item__th_adapt',
		),
		'separator' => array(
			'class_th' => 'item__th_seporator',
		),
		'status' => array(
			'type' => 'select',
			'name' => 'статус',
			'sql' => true,
			'select' => array(
					"0" => '<span class="orange_text" title="Чек ожидает инициализации отправки">Подготовлен</span>',
					"1" => '<span class="green_text" title="Чек отправлен">Отправлен</span>',
					"2" => '<span class="red_text" title="Зарегистрирована ошибка при отправлении чека">Ошибка</span>',
			),
			'no_important' => true,
		),
		'timesent' => array(
			'sql' => true,
			'type' => 'none',
		),
		'actions' => array(
			'send' => true,
			'del' => true,
		),
	);

	/**
	 * @var array дополнительные групповые операции
	 */
	public $group_action = array(
		"group_send" => array(
			'name' => "Отправить",
			'module' => 'cashregister',
		),
	);

	/**
	 * @var array поля для фильтра
	 */
	public $variables_filter = array (
		'presell' => array(
			'type' => 'checkbox',
			'name' => '100% предоплата',
		),
		'sell' => array(
			'type' => 'checkbox',
			'name' => 'Полная оплата',
		),
		'refund' => array(
			'type' => 'checkbox',
			'name' => 'Возврат',
		),
		'hr1' => array(
			'type' => 'hr',
		),
		'no_send' => array(
			'type' => 'checkbox',
			'name' => 'Все <span class="orange_text" title="Чеки ожидающие инициализации отправки">подготовленные</span>',
		),
		'error_send' => array(
			'type' => 'checkbox',
			'name' => 'Все <span class="red_text" title="Зарегистрированные ошибки при отправлении чеков">ошибки</span> отправлений',
		),
		'send' => array(
			'type' => 'checkbox',
			'name' => 'Все <span class="green_text" title="Отправленные чеки">отправленные</span>',
		),
	);

	/**
	 * @var array настройки модуля
	 */
	public $config = array (
		'db_ex', // составные идентификаторы
	);

	/**
	 * Выводит ссылку на добавление
	 * @return void
	 */
	public function show_add()
	{
		$this->diafan->addnew_init('Добавить чек', 'fa-cart-plus');
	}

	/**
	 * Выводит контент модуля
	 * @return void
	 */
	public function show()
	{
		if(_LANG != $this->diafan->_languages->admin)
		{
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/cashregister/');
		}
		if(IS_DEMO)
		{
			echo '<div class="error">'.$this->diafan->_('не доступно в демонстрационном режиме').'</div>';
		}
		else
		{
			echo '<span class="shop_stat">';

			$stat = DB::query_fetch_key_value("SELECT COUNT(*) AS c, status FROM {shop_cashregister} WHERE status IN ('0', '2') GROUP BY status", "status", "c");

			echo $this->diafan->_('Всего <b>чеков</b> ожидает отправки: <b>%s</b>', (! empty($stat["0"]) ? $stat["0"] : 0));

			if(! empty($stat["2"]))
			{
				echo $this->diafan->_(', ошибки отправлений: <b>%s</b>', $stat["2"]);
			}

			echo '</span>';

			$this->diafan->list_row();
		}
	}

	/**
	 * Формирует поле "Статус" в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_status($row, $var)
	{
		$html = '<div class="'.($var["class"] ? ' '.$var["class"] : '').'">';
		$html .= $this->diafan->_($var["select"][$row["status"]]);
		if($row["timesent"] != 0)
		{
			$html .= '<br>'.date("d.m.Y H:i", $row["timesent"]);
		}
		$html .= '</div>';
		return $html;
	}

	/**
	 * Формирует поле "Номер заказа" в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_order_id($row, $var)
	{
		$html = '<div class="'.($var["class"] ? ' '.$var["class"] : '').'">';
		$html .= $this->diafan->_('Заказ № %d', $row["order_id"]);
		$html .= '</div>';
		return $html;
	}

	/**
	 * Выводит кнопки действий над элементом
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_actions($row, $var)
	{
		$text = '<div class="item__unit">';

		// send
		if ($view = $this->diafan->variable_list('actions', 'send')/* && (in_array($row["status"], array('0', '2'), true))*/)
		{
			$text .= '<a href="javascript:void(0)" title="'.$this->diafan->_('Отправить чек').'" action="send" module="cashregister" class="action item__ui send">
				<i class="fa fa-envelope"></i>
			</a>';
		}

		//del
		if ($this->diafan->variable_list('actions', 'del')
			&& $this->diafan->_users->roles('del', $this->diafan->_admin->rewrite)
			&& $this->diafan->check_action($row, 'del'))
		{
			$text .= '
			<a href="javascript:void(0)" title="'.$this->diafan->_('Удалить').'"'.' confirm="'
			.$this->diafan->_('Внимание! Чеки будут безвозвратно удалены без возможности восстановления.\n\r\n\rПродолжить?')
			. '" action="delete" class="action item__ui remove">
				<i class="fa fa-times-circle"></i>
			</a>';
		}

		$text .= '</div>';

		return $text;
	}

	/**
	 * Поиск по полю "100% предоплата"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_presell($row)
	{
		if(! empty($_GET["filter_presell"]) || ! empty($_GET["filter_sell"]) || ! empty($_GET["filter_refund"]))
		{
			$types = array();
			if (! empty($_GET["filter_presell"]))
			{
				$types[] = 'presell';
			}
			if (! empty($_GET["filter_sell"]))
			{
				$types[] = 'sell';
			}
			if (! empty($_GET["filter_refund"]))
			{
				$types[] = 'refund';
			}
			$this->diafan->where .= " AND e.type IN('".implode("','", $types)."')";
		}
		if (empty($_GET["filter_presell"]))
		{
			return;
		}
		$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_presell=1';
		return 1;
	}

	/**
	 * Поиск по полю "Полная оплата"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_sell($row)
	{
		if (empty($_GET["filter_sell"]))
		{
			return;
		}
		$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_sell=1';
		return 1;
	}

	/**
	 * Поиск по полю "Возврат"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_refund($row)
	{
		if (empty($_GET["filter_refund"]))
		{
			return;
		}
		$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_refund=1';
		return 1;
	}

	/**
	 * Поиск по полю "Все подготовленные"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_no_send($row)
	{
		if (empty($_GET["filter_no_send"]))
		{
			return;
		}

		$this->save_filter_variable_status($row);
		return 1;
	}

	/**
	 * Поиск по полю "Все ошибки"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_error_send($row)
	{
		if (empty($_GET["filter_error_send"]))
		{
			return;
		}

		$this->save_filter_variable_status($row);
		return 1;
	}

	/**
	 * Поиск по полю "Все отправленные"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	public function save_filter_variable_send($row)
	{
		if (empty($_GET["filter_send"]))
		{
			return;
		}

		$this->save_filter_variable_status($row);
		return 1;
	}

	/**
	 * Формирует запрос для поиска по полю "Статус"
	 *
	 * @param array $row информация о текущем поле
	 * @return mixed
	 */
	private function save_filter_variable_status($row)
	{
		if(isset($this->cache["prepare"]["save_filter_variable_status"]))
		{
			return;
		}
		else $this->cache["prepare"]["save_filter_variable_status"] = true;

		if (! empty($_GET["filter_no_send"]) && ! empty($_GET["filter_error_send"]) && ! empty($_GET["filter_send"]))
		{
			return 1;
		}

		if (empty($_GET["filter_no_send"]) && empty($_GET["filter_error_send"]) && empty($_GET["filter_send"]))
		{
			return;
		}

		$where = '';
		if (! empty($_GET["filter_no_send"]))
		{
			$where .= ",'0'";
			$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_no_send=1';
		}
		if (! empty($_GET["filter_error_send"]))
		{
			$where .= ",'2'";
			$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_error_send=1';
		}
		if (! empty($_GET["filter_send"]))
		{
			$where .= ",'1'";
			$this->diafan->get_nav .= ($this->diafan->get_nav ? '&amp;' : '?' ).'filter_send=1';
		}
		$this->diafan->where .= " AND e.status IN (".substr($where, 1).")";
		return 1;
	}

	/**
	 * Редактирование поля "Дата и время"
	 * @return void
	 */
	public function edit_variable_master_id()
	{
		if ($this->diafan->is_new)
		{
			return;
		}

		$type = $this->diafan->variable('', 'type');
		$this->diafan->show_table_tr(
			$type,
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
	 * Редактирование поля "Cтатус отправки"
	 * @return void
	 */
	public function edit_variable_status()
	{
		if ($this->diafan->is_new)
		{
			return;
		}

		$class = $attr = '';
		$depend = $this->diafan->variable('', 'depend');
		if($depend)
		{
			$attr .= ' depend="'.$depend.'"';
			$class = "depend_field";
		}
		$key = $this->diafan->key;
		$name = $this->diafan->variable_name();
		$select = $this->diafan->variable('', 'select');
		$value = ! empty($select[$this->diafan->value]) ? $select[$this->diafan->value] : '';
		$help = $this->diafan->help();
		$disabled = $this->diafan->variable_disabled();
		$maxlength = $this->diafan->variable('', 'maxlength');

		$timesent = '';
		if($this->diafan->is_variable('timesent'))
		{
			$timesent = $this->diafan->values('timesent');
			if($timesent != 0)
			{
				$timesent = ' '.date("d.m.Y H:i", $timesent);
			}
			else $timesent = '';
		}

		echo '
		<div class="unit'.($class ? ' '.$class : '').'" id="'.$key.'"'.$attr.'>
			<div class="infofield">'.$name.$help.'</div>';
			echo '<div name="'.$key.'"'.($disabled ? ' disabled' : '').($maxlength ? ' maxlength="'.$maxlength.'"' : '').' class="text">'
				.$value
				.$timesent;
			echo '</div>';
		echo '
		</div>';
	}

	/**
	 * Редактирование поля "Отчет об ошибке отправления"
	 * @return void
	 */
	public function edit_variable_error()
	{
		if ($this->diafan->is_new || empty($this->diafan->value))
		{
			return;
		}

		$class = $attr = '';
		$depend = $this->diafan->variable('', 'depend');
		if($depend)
		{
			$attr .= ' depend="'.$depend.'"';
			$class = "depend_field";
		}
		$key = $this->diafan->key;
		$name = $this->diafan->variable_name();
		$value = ! empty($this->diafan->value) ? '<span class="red_text" title="Зарегистрирована ошибка при отправлении чека">'.$this->diafan->value.'</span>' : '';
		$help = $this->diafan->help();
		$disabled = $this->diafan->variable_disabled();
		$maxlength = $this->diafan->variable('', 'maxlength');

		echo '
		<div class="unit'.($class ? ' '.$class : '').'" id="'.$key.'"'.$attr.'>
			<div class="infofield">'.$name.$help.'</div>';
			echo '<div name="'.$key.'"'.($disabled ? ' disabled' : '').($maxlength ? ' maxlength="'.$maxlength.'"' : '').' class="text">'
				.$value;
			echo '</div>';
		echo '
		</div>';
	}

	/**
	 * Редактирование поля "Трассировка отправления"
	 * @return void
	 */
	public function edit_variable_trace()
	{
		if ($this->diafan->is_new || empty($this->diafan->value))
		{
			return;
		}
		$error = $this->diafan->values('error');
		if (empty($error))
		{
			return;
		}

		$type = $this->diafan->variable('', 'type');
		$this->diafan->show_table_tr(
			$type,
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
	 * Редактирование поля "Отправить чек"
	 *
	 * @return void
	 */
	public function edit_variable_send()
	{
		$this->diafan->value = false;
		if($this->diafan->value === false)
		{
			$this->diafan->value = '';
		}

		$this->diafan->show_table_tr(
			'checkbox',
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
	 * Сохранение поля "Метод отправки"
	 * @return void
	 */
	public function save_variable_auto()
	{
		if ($this->diafan->is_new)
		{
			$this->diafan->set_query("auto='%h'");
			$this->diafan->set_value('0');
		}
	}

	/**
	 * Функция, выполняющаяся после сохранения перед редиректом
	 *
	 * @return void
	 */
	public function save_after()
	{
		if(! empty($_POST["send"]))
		{
			if($this->diafan->_cashregister->receipt_send($this->diafan->id))
			{
				$this->diafan->set_one_shot(
				'<div class="ok">'.$this->diafan->_('Чек отправлен.').'</div>'
				);
			}
		}
	}
}
