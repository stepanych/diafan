<?php
/**
 * История фоновых процессов
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

class Executable_admin extends Frame_admin
{
	/**
	 * @var string таблица в базе данных
	 */
	public $table = 'executable';

	/**
	 * @var string условие для отбора
	 */
	public $where = "";

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'master_id' => array(
				'type' => 'string',
				'name' => 'Дата создания',
				'help' => 'Время создания фонового процесса. Вводится в формате дд.мм.гггг чч:мм.',
			),
			'module_name' => array(
				'type' => 'select',
				'name' => 'Модуль',
				'select_db' => array(
					"table" => "modules",
					"id" => "name",
					"name" => "title",
					"where" => "1=1",
					"empty" => "",
				),
				'help' => 'Имя модуля.',
				'no_save' => true,
				'disabled' => true,
			),
			'method' => array(
				'type' => 'text',
				'name' => 'Метод',
				'help' => 'Имя метода.',
				'no_save' => true,
				'disabled' => true,
			),
			'is_admin' => array(
				'sql' => true,
				'type' => 'none',
			),
			'rewrite' => array(
				'type' => 'string',
				'name' => 'Псевдоссылка',
				'help' => 'Псевдоссылка инициализации фонового процесса.',
			),
			'params' => array(
				'type' => 'string',
				'name' => 'Параметры',
				'help' => 'Параметры инициализации фонового процесса.',
			),
			'init_backtrace' => array(
				'type' => 'string',
				'name' => 'Стек инициализации',
				'help' => 'Стек вызовов функций инициализации фонового процесса.',
			),
			'forced' => array(
				'type' => 'select',
				'name' => 'Инициализация',
				'select' => array(
					 "0" => "последовательная",
					 "1" => "незамедлительная",
				 ),
				'help' => 'Незамедлительная инициализация - принудительное исполнение фонового процесса вне зависимости общего лимита количества одновременных процессов. Последовательная инициализация - исполнение фонового процесса в порядке очередности аналогичных процессов.',
				'disabled' => true,
			),
			'prior' => array(
				'type' => 'select',
				'name' => 'Очередность',
				'select' => array(
					 "0" => "неприоритетная",
					 "1" => "приоритетная",
				 ),
				'help' => 'Приоритетное исполнение фонового процесса по отношению к аналогичным процессам, ожидающих исполнения.',
				'no_save' => true,
				'disabled' => true,
				'depend' => 'forced=0',
			),
			'text' => array(
				'sql' => true,
				'type' => 'none',
			),
			'iteration' => array(
				'type' => 'string',
				'name' => 'Прогресс',
			),
			'max_iteration' => array(
				'sql' => true,
				'type' => 'none',
			),
			'break' => array(
				'sql' => true,
				'type' => 'none',
			),
			'status' => array(
				'type' => 'string',
				'name' => 'Статус',
				'help' => 'Состояние и время отправки уведомления.',
			),
			'result' => array(
				'type' => 'string',
				'name' => 'Результат',
				'help' => 'Результат исполнения фонового процесса.',
			),
		),
		'other_rows' => array (
			'timeedit' => array(
				'type' => 'datetime',
				'name' => 'Время изменения',
				'help' => 'Изменяется после обновления элемента.',
			),
		),
	);

	/**
	 * @var array поля в списка элементов
	 */
	public $variables_list = array (
		'checkbox' => '',
		'name' => array(
			'type' => 'datetime',
			'name' => 'Дата создания',
			'variable' => 'master_id',
			'help' => 'Время создания фонового процесса. Вводится в формате дд.мм.гггг чч:мм.',
		),
		'adapt' => array(
			'class_th' => 'item__th_adapt',
		),
		'separator' => array(
			'class_th' => 'item__th_seporator',
		),
		'timeedit' => array(
			'type' => 'datetime',
			'name' => 'Дата и время',
			'sql' => true,
			'help' => 'Время последнего обновления записи.',
			'no_important' => true,
		),
		'module_name' => array(
			'type' => 'select',
			'name' => 'Модуль',
			'sql' => true,
			'select_db' => array(
				"table" => "modules",
				"id" => "name",
				"name" => "title",
				"where" => "1=1",
				"empty" => "",
			),
			'help' => 'Имя модуля.',
			'no_important' => true,
		),
		'method' => array(
			'type' => 'text',
			'name' => 'Метод',
			'sql' => true,
			'help' => 'Имя метода.',
			'no_important' => true,
		),
		'is_admin' => array(
			'sql' => true,
			'type' => 'none',
		),
		'rewrite' => array(
			'type' => 'text',
			'name' => 'Ссылка',
			'sql' => true,
			'help' => 'Псевдоссылка инициализации фонового процесса.',
			'no_important' => true,
		),
		'text' => array(
			'sql' => true,
			'type' => 'none',
		),
		'iteration' => array(
			'type' => 'numtext',
			'name' => 'Прогресс',
			'sql' => true,
			'no_important' => true,
		),
		'max_iteration' => array(
			'sql' => true,
			'type' => 'none',
		),
		'break' => array(
			'sql' => true,
			'type' => 'none',
		),
		'status' => array(
			'type' => 'select',
			'name' => 'Статус',
			'sql' => true,
			'select' => array(
				"-3" => '<span class="red_text" title="Фоновый процесс выполняется">Прервано</span>',
				"-2" => '<span class="brown_text" title="Фоновый процесс выполняется">Прервано</span>',
				"-1" => '<span class="orange_text" title="Фоновый процесс выполняется">Прерывается</span>',
				"0" => '<span class="blue_text" title="Фоновый процесс ожидает инициализации">Ожидает</span>',
				"1" => '<span class="orange_text" title="Фоновый процесс выполняется">Выполняется</span>',
				"2" => '<span class="green_text" title="Фоновый процесс завершен">Завершено</span>',
				"3" => '<span class="red_text" title="Зарегистрирована ошибка при выполнении фонового процесса">Ошибка</span>',
			),
			'help' => 'Состояние и время отправки уведомления.',
		),
		'actions' => array(
			'break' => true,
			'reexecute' => true,
			'del' => true,
		),
	);

	/**
	 * @var array дополнительные групповые операции
	 */
	public $group_action = array(
		"group_break" => array(
			'name' => "Прервать",
			'module' => 'executable',
		),
		"group_reexecute" => array(
			'name' => "Возобновить",
			'module' => 'executable',
		),
	);

	/**
	 * @var array поля для фильтра
	 */
	public $variables_filter = array (
		'status' => array(
			'type' => 'multiselect',
			'name' => 'Искать по статусу',
			'select' => array(
				'0' => 'ожидает инициализации',
				'1' => 'процесс выполнения',
				'2' => 'процесс завершен',
				'3' => 'ошибка во время выполнения',
			),
		),
		'forced' => array(
			'type' => 'multiselect',
			'name' => 'Инициализация',
			'select' => array(
				'0' => 'последовательная',
				'1' => 'незамедлительная',
			),
		),
		'prior' => array(
			'type' => 'multiselect',
			'name' => 'Очередность',
			'select' => array(
				'0' => 'неприоритетная',
				'1' => 'приоритетная',
			),
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
	 * Подготавливает конфигурацию модуля
	 *
	 * @return void
	 */
	public function prepare_config()
	{
		if(! $this->diafan->configmodules("sys_visible"))
		{
			if(empty($this->diafan->where)) $this->diafan->where = '';
			$this->diafan->where .= "AND e.module_name<>'executable' AND e.module_name<>'execute' AND e.module_name<>'tick'";
		}
	}

	/**
	 * Выводит контент модуля
	 *
	 * @return void
	 */
	public function show()
	{
		if(_LANG != $this->diafan->_languages->admin)
		{
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/executable/');
		}
		if(IS_DEMO)
		{
			echo '<div class="error">'.$this->diafan->_('не доступно в демонстрационном режиме').'</div>';
		}
		else
		{
			echo $this->important_title;

			echo '<span class="executable_stat">';
			$defer = (int) DB::query_result("SELECT COUNT(*) FROM {executable} WHERE status='0'");
			// $execute = (int) DB::query_result("SELECT COUNT(*) FROM {executable} WHERE `status`='1' OR (`max_iteration`>0 AND `iteration`<`max_iteration` AND `break`='0')");
			$execute = $this->diafan->_executable->count();
			$errors = (int) DB::query_result("SELECT COUNT(*) FROM {executable} WHERE `status`='3'");
			echo $this->diafan->_('Всего <b>ожидают</b> / <b>выполняются</b> процессов: <b>%s</b> / <b>%s</b>', $defer, $execute);
			echo ($errors ? $this->diafan->_(', ошибки: <b>%s</b>', $errors) : '');
			echo '</span>';

			$this->diafan->list_row();

			// нет элементов
			if(empty($this->diafan->rows))
			{
				echo '<span>'.$this->diafan->_('Нет фоновых задач').'</span>';
			}
		}
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
	 * Фильтр вывода
	 *
	 * @return void
	 */
	public function show_module_contents()
	{
		if($this->diafan->count)
		{
			echo '<p>
			<form action="" method="post">
			<input name="module" type="hidden" value="executable">
			<input name="action" type="hidden" value="clear">
			<input name="all" type="hidden" value="true">
			<input name="check_hash_user" type="hidden" value="'.$this->diafan->_users->get_hash().'">
			<input type="button" class="trash_clear button" value="'.$this->diafan->_('Очистить завершённые процессы').'" confirm="'.$this->diafan->_('Вы действительно хотите удалить все элементы?').'">
			</form>
			</p>';
		}
	}

	/**
	 * Формирует SQL-запрос для списка элементов
	 *
	 * @param integer $id родитель
	 * @return array
	 */
	public function sql_query($id)
	{
		$rows = parent::__call('sql_query', array($id));
		if(! empty($rows))
		{
			foreach($rows as &$row)
			{
				if($row["iteration"] < $row["max_iteration"] && $row["status"] == 2 && ! $row["break"])
				{
					$row["status"] = 1;
				}
				if($row["break"])
				{
					$row["status"] = ! in_array($row["status"], array(2, 3)) ? -1 : ($row["status"] * (-1));
				}
			}
		}
		return $rows;
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
		." FIELD(e.status, '1', '0', '3', '2') ASC, e.timeedit DESC"
		.(! empty($order) ? ", ".$order : "");
	}

	/**
	 * Формирует поле "Псевдоссылка" в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_rewrite($row, $var)
	{
		return ! empty($row["rewrite"])
		? '<span><a href="'.BASE_PATH.(! empty($row["is_admin"]) ? ADMIN_FOLDER.'/': '').$row["rewrite"].'">'.$row["rewrite"].'</a></span>'
		: '<span>&nbsp;</span>';
	}

	/**
	 * Формирует поле "Прогресс" в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_iteration($row, $var)
	{
		$pos = ! empty($row["iteration"]) ? (int) $row["iteration"] : 0;
		$max = ! empty($row["max_iteration"]) ? (int) $row["max_iteration"] : 0;
		$max = $max < $pos ? $pos : $max;
		$progress = '&nbsp;';
		if($max > 0 && $pos <= $max)
		{
			if($pos < $max)
			{
				$progress = sprintf('%1.2f', ($pos*100/$max)); $p = explode('.', $progress);
				$progress = (count($p) == 2 ? array_shift($p).'.<span style="zoom: .85;">'.array_shift($p).'</span>' : $progress);
			}
			else $progress = 100;
			$progress = $progress.' %';
			if(! empty($row["text"]))
			{
				$progress = sprintf('%s ... %s', $this->diafan->short_text(strip_tags($row["text"])), $progress);
			}
		}
		elseif(! empty($row["text"]))
		{
			$progress = sprintf('%s', $this->diafan->short_text(strip_tags($row["text"])));
		}
		return '<span>'.$progress.'</span>';
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

		// break
		if ($view = $this->diafan->variable_list('actions', 'break') && (in_array($row["status"], array(0, 1))))
		{
			$text .= '<a href="javascript:void(0)" title="'.$this->diafan->_('Прервать').'" action="group_break" module="executable" class="action item__ui break">
				<i class="fa fa-ban"></i>
			</a>';
		}

		// reexecute
		if ($view = $this->diafan->variable_list('actions', 'reexecute') && (in_array($row["status"], array(-2, -1, 2, 3))))
		{
			$text .= '<a href="javascript:void(0)" title="'.$this->diafan->_('Возобновить').'" action="group_reexecute" module="executable" class="action item__ui reexecute">
				<i class="fa fa-repeat"></i>
			</a>';
		}

		//del
		if ($this->diafan->variable_list('actions', 'del')
			&& $this->diafan->_users->roles('del', $this->diafan->_admin->rewrite)
			&& $this->diafan->check_action($row, 'del'))
		{
			$text .= '
			<a href="javascript:void(0)" title="'.$this->diafan->_('Удалить').'"'.' confirm="'
			.(!empty( $row["count_children"] ) ? $this->diafan->_('ВНИМАНИЕ! Пункт содержит вложенность! ') : '')
			.($this->diafan->config("category") ? $this->diafan->_('При удалении категории удаляются все принадлежащие ей элементы. ') : '')
			.$this->diafan->_('Внимание! Фоновые процессы будут безвозвратно удалены без возможности восстановления.\n\r\n\rПродолжить?')
			. '" action="delete" class="action item__ui remove">
				<i class="fa fa-times-circle"></i>
			</a>';
		}

		$text .= '</div>';

		return $text;
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
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/executable/');
		}
		if(IS_DEMO)
		{
			echo '<div class="error">'.$this->diafan->_('не доступно в демонстрационном режиме').'</div>';
		}
		else
		{
			echo parent::__call('edit', array()); // parent::edit();
		}
	}

	/**
	 * Редактирование поля "Дата и время"
	 *
	 * @return void
	 */
	public function edit_variable_master_id()
	{
		if ($this->diafan->is_new || empty($this->diafan->value))
		{
			$this->diafan->value = date("d.m.Y H:i");
		}
		else $this->diafan->value = date("d.m.Y H:i", $this->diafan->value);

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
	 * Редактирование поля "Псевдоссылка"
	 *
	 * @return void
	 */
	public function edit_variable_rewrite()
	{
		if ($this->diafan->is_new)
		{
			return;
		}

		$is_admin = false;
		if($this->diafan->is_variable('is_admin'))
		{
			$is_admin = $this->diafan->values('is_admin');
		}
		$this->diafan->value = BASE_PATH.(! empty($is_admin) ? ADMIN_FOLDER.'/': '').$this->diafan->value;
		$this->diafan->value = '<a href="'.$this->diafan->value.'">'.$this->diafan->value.'</a>';

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
	 * Редактирование поля "Параметры"
	 *
	 * @return void
	 */
	public function edit_variable_params()
	{
		if ($this->diafan->is_new || empty($this->diafan->value))
		{
			return;
		}

		$this->diafan->value = unserialize($this->diafan->value);
		ob_start();
		echo '<pre>';
		var_dump($this->diafan->value);
		echo '</pre>';
		$this->diafan->value = ob_get_contents();
		ob_end_clean();

		if($this->diafan->value)
		{
			$this->diafan->value = '
			<div class="helper">
				<input type="checkbox" id="'.$this->diafan->key.'_helper'.'" class="checkbox hide"/>
				<label for="'.$this->diafan->key.'_helper'.'" class="btn btn_black btn_small btn_helper">'.$this->diafan->_('Посмотреть').'</label>
				<div>'.$this->diafan->value.'</div>
			</div>';
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
	 * Редактирование поля "Стек инициализации"
	 *
	 * @return void
	 */
	public function edit_variable_init_backtrace()
	{
		if ($this->diafan->is_new || empty($this->diafan->value))
		{
			return;
		}

		$this->diafan->value = htmlspecialchars(urldecode($this->diafan->value));
		ob_start();
		echo '<pre>';
		echo $this->diafan->value;
		echo '</pre>';
		$this->diafan->value = ob_get_contents();
		ob_end_clean();

		if($this->diafan->value)
		{
			$this->diafan->value = '
			<div class="helper">
				<input type="checkbox" id="'.$this->diafan->key.'_helper'.'" class="checkbox hide"/>
				<label for="'.$this->diafan->key.'_helper'.'" class="btn btn_black btn_small btn_helper">'.$this->diafan->_('Посмотреть').'</label>
				<div>'.$this->diafan->value.'</div>
			</div>';
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
	 * Редактирование поля "Прогресс"
	 *
	 * @return void
	 */
	public function edit_variable_iteration()
	{
		if ($this->diafan->is_new)
		{
			return;
		}

		$text = '';
		if($this->diafan->is_variable('text'))
		{
			$text = $this->diafan->values('text');
		}
		$max_iteration = 0;
		if($this->diafan->is_variable('max_iteration'))
		{
			$max_iteration = $this->diafan->values('max_iteration');
		}

		$pos = ! empty($this->diafan->value) ? (int) $this->diafan->value : 0;
		$max = ! empty($max_iteration) ? (int) $max_iteration : 0;
		$max = $max < $pos ? $pos : $max;
		$progress = '&nbsp;';
		if($max > 0 && $pos <= $max)
		{
			if($pos < $max)
			{
				$progress = sprintf('%1.2f', ($pos*100/$max)); $p = explode('.', $progress);
				$progress = (count($p) == 2 ? array_shift($p).'.<span style="zoom: .85;">'.array_shift($p).'</span>' : $progress);
			}
			else $progress = 100;
			$progress = $progress.' %';
			if(! empty($text))
			{
				$progress = sprintf('%s ... %s', strip_tags($text), $progress);
			}
		}
		elseif(! empty($text))
		{
			$progress = sprintf('%s', strip_tags($text));
		}
		$this->diafan->value = '<span style="white-space:nowrap;">'.$progress.'</span>';

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
	 * Редактирование поля "Статус"
	 *
	 * @return void
	 */
	public function edit_variable_status()
	{
		if ($this->diafan->is_new)
		{
			return;
		}

		$select = array(
			"-3" => '<span class="red_text" title="Фоновый процесс выполняется">Прервано</span>',
			"-2" => '<span class="brown_text" title="Фоновый процесс выполняется">Прервано</span>',
			"-1" => '<span class="orange_text" title="Фоновый процесс выполняется">Прерывается</span>',
			"0" => '<span class="blue_text" title="Фоновый процесс ожидает инициализации">Ожидает</span>',
			"1" => '<span class="orange_text" title="Фоновый процесс выполняется">Выполняется</span>',
			"2" => '<span class="green_text" title="Фоновый процесс завершен">Завершено</span>',
			"3" => '<span class="red_text" title="Зарегистрирована ошибка при выполнении фонового процесса">Ошибка</span>',
		);

		if(empty($select[$this->diafan->value]))
		{
			return;
		}

		$this->diafan->value = $select[$this->diafan->value];

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
	 * Редактирование поля "Результат"
	 *
	 * @return void
	 */
	public function edit_variable_result()
	{
		if ($this->diafan->is_new || empty($this->diafan->value))
		{
			return;
		}

		$json = $this->diafan->value;
		Custom::inc('includes/json.php');
		$json = Json::from_json($json);
		if($json)
		{
			$this->diafan->value = '';
			if(! empty($json["result"]))
			{
				$this->diafan->value .= '<b class="unit">'.$this->diafan->_('Ответ фонового процесса:').'</b>';
				$this->diafan->value .= '<p><pre>'.htmlspecialchars($json["result"]).'</pre></p>';
			}
			if(! empty($json["data"]))
			{
				$this->diafan->value .= '<b class="unit">'.$this->diafan->_('Ответ метода:').'</b>';
				$this->diafan->value .= '<p><pre>';
				ob_start();
				var_dump($json["data"]);
				$this->diafan->value .= ob_get_contents();
				ob_end_clean();
				$this->diafan->value .= '</pre></p>';
			}
			if(! empty($json["query"]))
			{
				$this->diafan->value .= '<b class="unit">'.$this->diafan->_('SQL-запросы с маркером DEV:').'</b>';
				$this->diafan->value .= '<p><pre>'.$json["query"].'</pre></p>';
			}
			if(! empty($json["error"]))
			{
				$this->diafan->value .= '<b class="unit">'.$this->diafan->_('Ошибки при выполнении процесса:').'</b>';
				$this->diafan->value .= '<p><pre>'.implode(PHP_EOL, $json["error"]).'</pre></p>';
			}
			if(! empty($json["content"]))
			{
				$this->diafan->value .= '<b class="unit">'.$this->diafan->_('Контент процесса:').'</b>';
				$this->diafan->value .= '<p><pre>'.implode(PHP_EOL, $json["content"]).'</pre></p>';
			}
			if($this->diafan->value)
			{
				$this->diafan->value = '
				<div class="helper">
					<input type="checkbox" id="'.$this->diafan->key.'_helper'.'" class="checkbox hide"/>
					<label for="'.$this->diafan->key.'_helper'.'" class="btn btn_black btn_small btn_helper">'.$this->diafan->_('Посмотреть').'</label>
					<div>'.$this->diafan->value.'</div>
				</div>';
			}
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
}
