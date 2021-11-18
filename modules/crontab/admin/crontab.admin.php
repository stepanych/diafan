<?php
/**
 * Расписание задач
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

class Crontab_admin extends Frame_admin
{
	/**
	 * @var string таблица в базе данных
	 */
	public $table = 'crontab';

	/**
	 * @var string условие для отбора
	 */
	public $where = "";

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'name' => array(
				'type' => 'text',
				'name' => 'Название задачи',
			),
			'text' => array(
				'type' => 'textarea',
				'name' => 'Описание задачи',
				'height' => 200,
			),
			'hr1' => 'hr',
			'act' => array(
				'type' => 'checkbox',
				'name' => 'Активировать задачу',
				'help' => 'Если не отмечена, задача не будет участвовать в расписании.',
				'default' => true,
			),
			'hr2' => array(
				'type' => 'title',
				'name' => 'Инициализация',
			),
			'datetime_rules' => array(
				'type' => 'function',
				'name' => 'Выбор времени выполнения задания',
				'no_save' => true,
			),
			'datetime' => array(
				'type' => 'text',
				'name' => 'Время выполнения задачи',
				'help' => 'Время выполнения задачи необходимо указывать в формате CRON.',
			),
			'datetime_expected' => array(
				'type' => 'datetime',
				'name' => 'Дата ожидаемого исполнения задачи',
				'no_save' => true,
				'disabled' => true,
				'help' => 'Вводится в формате дд.мм.гггг чч:мм.',
			),
			'module_name' => array(
				'type' => 'select',
				'name' => 'Название модуля',
				'select' => array(
					'' => 'не определен',
				),
				'help' => 'Имя модуля, метод которого необходимо выполнить.',
			),
			'method' => array(
				'type' => 'select',
				'name' => 'Название метода',
				'select' => array(
					'' => 'не определен',
				),
				'help' => 'Имя метода, который необходимо выполнить.',
			),
			'params_rules' => array(
				'type' => 'function',
				'name' => 'Выбор параметров выполнения задания',
				'no_save' => true,
			),
			'params' => array(
				'type' => 'textarea',
				'name' => 'Параметры инициализации в формате JSON',
				'help' => 'Указанные параметры будут переданы методу в качестве массива значений POST.',
			),
			'hr3' => array(
				'type' => 'title',
				'name' => 'Результат исполнения задачи',
			),
			'timeinit' => array(
				'type' => 'datetime',
				'name' => 'Дата последнего исполнения',
				'no_save' => true,
				'disabled' => true,
				'help' => 'Отображается в формате дд.мм.гггг чч:мм.',
			),
			'result' => array(
				'type' => 'textarea',
				'name' => 'Полученный результат исполнения',
				'no_save' => true,
				'disabled' => true,
			),
			'errors' => array(
				'type' => 'textarea',
				'name' => 'Ошибки при исполнении',
				'no_save' => true,
				'disabled' => true,
			),
			'content' => array(
				'type' => 'textarea',
				'name' => 'Контент по результатам исполнения',
				'no_save' => true,
				'disabled' => true,
			),
		),
		'other_rows' => array (
			'timeedit' => array(
				'type' => 'datetime',
				'name' => 'Время изменения',
				'help' => 'Изменяется после сохранения элемента.',
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
			'desc' => true,
    ),
		'name' => array(
			'name' => 'Название',
			'sql' => true,
		),
		'adapt' => array(
			'class_th' => 'item__th_adapt',
		),
		'separator' => array(
			'class_th' => 'item__th_seporator',
		),
		'datetime' => array(
			'name' => 'Время CRON',
			'type' => 'text',
			'sql' => true,
			'help' => 'Запланируемое время исполнения. Вводится в формате CRON.',
		),
		'datetime_expected' => array(
			'type' => 'datetime',
			'name' => 'Ожидаемая дата',
			'help' => 'Ожидаемая дата исполнения. Вводится в формате дд.мм.гггг чч:мм.',
			'no_important' => true,
		),
		'module_name' => array(
			'name' => 'Модуль',
			'type' => 'select',
			'sql' => true,
			'select' => array(
				'' => 'не определен',
			),
		),
		'method' => array(
			'name' => 'Метод',
			'type' => 'select',
			'sql' => true,
			'select' => array(
				'' => 'не определен',
			),
		),
		'text' => array(
			'name' => 'Описание задачи',
			'type' => 'text',
			'sql' => true,
			'no_important' => true,
		),
		'actions' => array(
			'act' => true,
			'trash' => true,
		),
	);

	/**
	 * @var string информационное сообщение
	 */
	private $important_title = '';

	/**
	 * @var integer минимально допустимое время для CRON
	 */
	private $min_time;

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		Custom::inc('modules/crontab/crontab.inc.php');
	}

	/**
	 * Подготавливает конфигурацию модуля
	 *
	 * @return void
	 */
	public function prepare_config()
	{
		$tick_status = !! $this->diafan->_executable->tick_status();
		$this->important_title .=
			'<div class="head-box head-box_warning">
				<a href="javascript:void(0)" title="'
				.($tick_status ? $this->diafan->_('Выключить') : $this->diafan->_('Включить'))
				.'" action="cron" module="crontab" class="action switch"'
				.' switch="'.($tick_status ? 'on' : 'off').'">
					<i class="fa fa-toggle-on"></i> <span>'.$this->diafan->_('Включить CRON').'</span>
					<img class="spinner hide" src="'.BASE_PATH.'adm/img/loading.gif">
				</a>
			</div>';

		$this->important_title .=
			'<div class="commentary">'.sprintf($this->diafan->_('Программный демон, эмулятор cron, поддерживает %sклассический формат постановки задач%s. Замечания и предложения отправляйте в %sТехническую поддержку%s DIAFAN.CMS.'), '<a href="https://ru.wikipedia.org/wiki/Cron">', '</a>', '<a href="https://user.diafan.ru/support/">', '</a>').'</div><br />';

		$this->min_time = mktime(0,0,0, date("n", Crontab_inc::MIN_YEAR), date("j", Crontab_inc::MIN_YEAR), date("Y", Crontab_inc::MIN_YEAR));

		// формирование списков модулей и их методов
		$this->diafan->variable_disabled("module_name", true);
		$this->diafan->variable_disabled("method", true);
		$modules = $this->diafan->all_modules;
		Custom::inc('includes/cron.php');
		if(! empty($modules))
		{
			$modules_select = array(); $methods_select = array();
			foreach($modules as $row)
			{
				$module = $row["name"];
				if(! Custom::exists('modules/'.$module.'/'.$module.'.cron.php')) continue;
				Custom::inc('modules/'.$module.'/'.$module.'.cron.php');
				$class = ucfirst($module).'_cron';
				if(! class_exists($class) || ! is_subclass_of($class, 'Cron')) continue;
				$object = new $class($this->diafan);
				if(! empty($object->methods))
				{
					$modules_select[$module] = $object->title;
					foreach($object->methods as $method => $name)
					{
						$methods_select[$module.'_'.$method] = $name;
						$this->cache["module_method"][$module.'_'.$method] = array("module" => $module, "method" => $method);
					}
				}
				unset($object);
			}
			if(! empty($modules_select) && ! empty($methods_select))
			{
				$this->diafan->variable("module_name", "select", $modules_select);
				$this->diafan->variable("method", "select", $methods_select);
				$this->diafan->variable_disabled("module_name", false);
				$this->diafan->variable_disabled("method", false);
				$this->diafan->variable_list("module_name", "select", $modules_select);
				$this->diafan->variable_list("method", "select", $methods_select);
			}
		}
	}

	/**
	 * Выводит ссылку на добавление
	 *
	 * @return void
	 */
	public function show_add()
	{
		$this->diafan->addnew_init('Добавить задачу');
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
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/crontab/');
		}
		if(IS_DEMO)
		{
			echo '<div class="error">'.$this->diafan->_('не доступно в демонстрационном режиме').'</div>';
		}
		else
		{
			echo $this->important_title;
			$this->diafan->list_row();
		}
	}

	/**
	 * Формирует поле "Дата ожидаемого исполнения задачи" в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_datetime_expected($row, $var)
	{
		$datetime = false;
		if(! empty($row["datetime"]))
		{
			$datetime = $row["datetime"];
			$datetime = $this->diafan->_crontab->parser($datetime, time());
			if($datetime !== false)
			{
				$datetime = date("d.m.Y H:i", $datetime);
			}
		}
		return ! empty($datetime)
		? '<span>'.$datetime.'</span>'
		: '<span>&nbsp;</span>';
	}

	/**
	 * Формирует поле "Название метода" в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_method($row, $var)
	{
		$method = false;
		if(! empty($row["module_name"]) && ! empty($row["method"])
		&& ! empty($this->cache["module_method"][$row["module_name"].'_'.$row["method"]]["method"]))
		{
			$method = $this->cache["module_method"][$row["module_name"].'_'.$row["method"]]["method"];
			if(! empty($var["select"][$row["module_name"].'_'.$row["method"]]))
			{
				$method = $var["select"][$row["module_name"].'_'.$row["method"]];
			}
		}
		return ! empty($method)
		? '<span>'.$method.'</span>'
		: '<span>&nbsp;</span>';
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
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/crontab/');
		}
		if(IS_DEMO)
		{
			echo '<div class="error">'.$this->diafan->_('не доступно в демонстрационном режиме').'</div>';
		}
		else
		{
			echo $this->important_title;
			echo parent::__call('edit', array()); // parent::edit();
		}
	}

	/**
	 * Редактирование поля "Выбор времени выполнения задания"
	 *
	 * @return void
	 */
	public function edit_variable_datetime_rules()
	{
		$this->diafan->value = '
		<div>
			<p><b>'.$this->diafan->_('Время запуска представляется в таком виде').'</b>:</p>
			<pre class="code bash">'
				.$this->diafan->_('минута%sчас%sдень_месяца%sмесяц%sдень_недели%sгод', '&emsp;', '&emsp;', '&emsp;', '&emsp;', '&emsp;')
			.'</pre>
			<p>
				<table class="inline">
					<thead>
						<tr>
							<th>'.$this->diafan->_('Значение').'</th><th>'.$this->diafan->_('Диапазон').'</th><th>'.$this->diafan->_('Дополнительно').'</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>'.$this->diafan->_('минуты').'</td><td>0-59</td><td></td>
						</tr>
						<tr>
							<td>'.$this->diafan->_('часы').'</td><td>0-23</td><td></td>
						</tr>
						<tr>
							<td>'.$this->diafan->_('дни месяца').'</td><td>1-31</td><td></td>
						</tr>
						<tr>
							<td>'.$this->diafan->_('месяцы').'</td><td>1-12</td><td>'.$this->diafan->_('можно задавать и в 3-х буквенном варианте').'</td>
						</tr>
						<tr>
							<td>'.$this->diafan->_('дни недели').'</td><td>0-6</td><td>'.$this->diafan->_('можно задавать и в 3-х буквенном варианте (0=воскресенье)').'</td>
						</tr>
						<tr>
							<td>'.$this->diafan->_('год').'</td><td></td><td>'.$this->diafan->_('4-х значное число (опционально)').'</td>
						</tr>
					</tbody>
				</table>
			</p>
			<p>'.$this->diafan->_('Символ %s подразумевает - любое значение', '&#10033;').'.</p>
			<p>'.$this->diafan->_('Минимальное время 1-а минута. Это связано с тем что cron каждую минуту просматривает список заданий, и ищет которые нужно выполнить.').'</p>
			<p>
				<table class="inline">
					<thead>
						<tr>
							<th>'.$this->diafan->_('Дни недели и месяца в трех буквенном варианте').':</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>&emsp;sun&emsp;mon&emsp;tue&emsp;wed&emsp;thu&emsp;fri&emsp;sat&emsp;</td>
						</tr>
						<tr>
							<td>&emsp;jan&emsp;feb&emsp;mar&emsp;apr&emsp;may&emsp;jun&emsp;jul&emsp;aug&emsp;sep&emsp;oct&emsp;nov&emsp;dec&emsp;</td>
						</tr>
					</tbody>
				</table>
			</p>
			<p><b>'.$this->diafan->_('Дополнительные переменные cron').'</b>:</p>
			<p>
				<table class="inline">
					<thead>
						<tr>
							<th>'.$this->diafan->_('Переменная').'</th><th>'.$this->diafan->_('Описание').'</th><th>'.$this->diafan->_('Эквивалент').'</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><s>@reboot</s></td><td><strike>'
								.$this->diafan->_('Запуск при загрузке')
								.'</strike> ('.$this->diafan->_('не поддерживается').')</td><td></td>
						</tr>
						<tr>
							<td>@yearly</td><td>'
								.$this->diafan->_('Раз в год (в полночь первого января каждого месяца)')
								.'</td><td>&emsp;0&ensp;0&ensp;1&ensp;1&ensp;&#9913;&emsp;</td>
						</tr>
						<tr>
							<td>@annually</td><td>'
								.$this->diafan->_('Тоже что и %s', '@yearly')
								.'</td><td></td>
						</tr>
						<tr>
							<td>@monthly</td><td>'
								.$this->diafan->_('Раз в месяц (в полночь первого числа каждого месяца)')
								.'</td><td>&emsp;0&ensp;0&ensp;1&ensp;&#9913;&ensp;&#9913;&emsp;</td>
						</tr>
						<tr>
							<td>@weekly</td><td>'
								.$this->diafan->_('Раз в неделю (в полночь каждого воскресенья)')
								.'</td><td>&emsp;0&ensp;0&ensp;&#9913;&ensp;&#9913;&ensp;0&emsp;</td>
						</tr>
						<tr>
							<td>@daily</td><td>'
								.$this->diafan->_('Раз в день (в полночь каждого дня)')
								.'</td><td>&emsp;0&ensp;0&ensp;&#9913;&ensp;&#9913;&ensp;&#9913;&emsp;</td>
						</tr>
						<tr>
							<td>@midnight</td><td>'
								.$this->diafan->_('В полночь')
								.' (00:00)</td><td></td>
						</tr>
						<tr class="row8">
							<td>@hourly</td><td>'
								.$this->diafan->_('Каждый час (в ноль минут каждого часа)')
								.'</td><td>&emsp;0&ensp;&#9913;&ensp;&#9913;&ensp;&#9913;&ensp;&#9913;&emsp;</td>
						</tr>
					</tbody>
				</table>
			</p>
		</div>';

		$this->diafan->value = '
		<div class="infofield">'.$this->diafan->_('Выбор времени выполнения задания').'</div>
		<div class="helper">
			<input type="checkbox" id="'.$this->diafan->key.'_helper'.'" class="checkbox hide"/>
			<label for="'.$this->diafan->key.'_helper'.'" class="btn btn_black btn_small btn_helper">'.$this->diafan->_('Посмотреть').'</label>
			<div>'.$this->diafan->value.'</div>
		</div>';

		echo $this->diafan->value;
	}

	/**
	 * Редактирование поля "Время выполнения задачи"
	 * @return void
	 */
	public function edit_variable_datetime()
	{
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
	 * Редактирование поля "Дата ожидаемого исполнения задачи"
	 *
	 * @return void
	 */
	public function edit_variable_datetime_expected()
	{
		if ($this->diafan->is_new)
		{
			return;
		}

		$datetime = $this->diafan->values("datetime");
		if(empty($datetime))
		{
			return;
		}

		$datetime = $this->diafan->_crontab->parser($this->diafan->values("datetime"), time());
		if($datetime === false)
		{
			return;
		}

		$this->diafan->value = $datetime;

		$type = $this->diafan->variable('', 'type');
		$this->diafan->show_table_tr(
			$type,
			$this->diafan->key,
			$this->diafan->value,
			$this->diafan->variable_name().($datetime < time() ? ' <span class="red">'.$this->diafan->_('истекла').'</span>' : ''),
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
	 * Редактирование поля "Название метода"
	 *
	 * @return void
	 */
	public function edit_variable_method()
	{
		$depend = $this->diafan->variable('', 'depend');
		$attr = $this->diafan->variable('', 'attr');

		$attr = $attr ?: '';
		$class = '';
		if($depend)
		{
			$attr .= ' depend="'.$depend.'"';
			$class = "depend_field";
		}

		$key = $this->diafan->key;
		$name = $this->diafan->variable_name();
		$value = $this->diafan->value;
		$help = $this->diafan->help();
		$disabled = $this->diafan->variable_disabled();
		$options = $this->diafan->variable('', 'select');
		if (! $options)
		{
			return;
		}
		$site_id = false;
		foreach ($options as $k => $select) { if($site_id = (is_array($select) && ! empty($select["site_id"]))) break; }
		echo '
		<div class="unit'.($class ? ' '.$class : '').'" id="'.$key.'"'.$attr.'>
			<div class="infofield">'.$name.$help.'</div>
			<select name="'.$key.'"'.($disabled ? ' disabled' : '').($site_id ? ' depend="site_id"' : '').'>';
			foreach ($options as $k => $select)
			{
				$site_id = false;
				if(is_array($select))
				{
					$k = $select["id"];
					$site_id = ! empty($select["site_id"]) ? $select["site_id"] : $site_id;
					$select = $select["name"];
				}
				if(! empty($this->cache["module_method"][$k]))
				{
					$module = $this->cache["module_method"][$k]["module"];
					$method = $this->cache["module_method"][$k]["method"];

					$k = $method;
					echo '<option value="'.$k.'"'.($value == $k ? ' selected' : '').($site_id ? ' rel="'.$site_id.'"' : '')
						.' module="'.$module.'" method="'.$method.'"'.($value == $k ? ' current="true"' : '').'>'
						.$this->diafan->_($select)
						.'</option>';
				}
				else
				{
					echo '<option value="'.$k.'"'.($value == $k ? ' selected' : '').($site_id ? ' rel="'.$site_id.'"' : '')
						.'>'
						.$this->diafan->_($select)
						.'</option>';
				}
			}
			echo '</select>
		</div>';
	}

	/**
	 * Редактирование поля "Выбор параметров выполнения задания"
	 *
	 * @return void
	 */
	public function edit_variable_params_rules()
	{
		$this->diafan->value = '
		<div>
			<p><b>Основные правила для создания строки JSON</b></p>
			<p>Строка JSON содержит либо массив значений, либо объект - ассоциативный массив пар имя/значение.<br />
			Массив заключается в квадратные скобки <b>[</b> и <b>]</b> и содержит разделенный запятой список значений.<br />
			Объект заключается в фигурные скобки <b>{</b> и <b>}</b> и содержит разделенный запятой список пар имя/значение.<br />
			Пара имя/значение состоит из имени поля, заключенного в двойные кавычки <b>"</b>, за которым следует двоеточие <b>:</b> и значение поля.</p>
			<p>Значение в массиве или объекте может быть:<br />
			Числом (целым или с плавающей точкой)<br />
			Строкой (в двойных кавычках)<br />
			Логическим значением (<b>true</b> или <b>false</b>)<br />
			Другим массивом (заключенным в квадратные скобки)<br />
			Другой объект (заключенный в фигурные скобки)<br />
			Значение <b>null</b></p>
			<p>Чтобы включить двойные кавычки в строку, нужно использовать обратную косую черту: <b>\"</b>.<br />
			Так же, как и во многих языках программирования, можно помещать управляющие символы и шестнадцатеричные коды в строку, предваряя их обратной косой чертой.</p>
			<p>Подробнее смотрите детали на <a href="https://www.json.org/json-en.html">сайте JSON</a>.</p>
			<p><b>'.$this->diafan->_('Пример строки инициализации в формате JSON').'</b>:</p>
			<pre class="code bash">'
				.'{"id":123, "status":false, "message":"Site not found"}'
			.'</pre>
		</div>';

		$this->diafan->value = '
		<div class="infofield">'.$this->diafan->_('Выбор параметров выполнения задания').'</div>
		<div class="helper">
			<input type="checkbox" id="'.$this->diafan->key.'_helper'.'" class="checkbox hide"/>
			<label for="'.$this->diafan->key.'_helper'.'" class="btn btn_black btn_small btn_helper">'.$this->diafan->_('Посмотреть').'</label>
			<div>'.$this->diafan->value.'</div>
		</div>';

		echo $this->diafan->value;
	}

	/**
	 * Редактирование поля "Параметры инициализации в формате JSON"
	 *
	 * @return void
	 */
	public function edit_variable_params()
	{
		$value = '';
		if(! $this->diafan->is_new && ! empty($this->diafan->value))
		{
			$value = unserialize($this->diafan->value);
			if(! empty($value))
			{
				Custom::inc('includes/json.php');
				$this->diafan->value = Json::encode($value);
			}
			else $this->diafan->value = '';
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

		if(! empty($value))
		{
			ob_start();
			echo '<pre>';
			var_dump($value);
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
				echo $this->diafan->value;
			}
		}
	}

	/**
	 * Редактирование поля "Полученный результат исполнения"
	 * @return void
	 */
	public function edit_variable_result()
	{
		if(empty($this->diafan->value))
		{
			return;
		}

		$this->diafan->value = htmlspecialchars($this->diafan->value);

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
	 * Редактирование поля "Ошибки при исполнении"
	 * @return void
	 */
	public function edit_variable_errors()
	{
		if(empty($this->diafan->value))
		{
			return;
		}

		$this->diafan->value = htmlspecialchars($this->diafan->value);

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
	 * Редактирование поля "Контент по результатам исполнения"
	 * @return void
	 */
	public function edit_variable_content()
	{
		if(empty($this->diafan->value))
		{
			return;
		}

		$this->diafan->value = htmlspecialchars($this->diafan->value);

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
	 * Проверка параметров поля "Время жизни всех записей"
	 *
	 * @return void
	 */
	public function validate_variable_datetime()
	{
		if(empty($_POST["datetime"])
		|| ! $this->diafan->_crontab->trim($this->diafan->filter($_POST, "string", "datetime")))
		{
			$this->diafan->set_error(
				"datetime",
				"Неверно задано время выполнения задачи."
			);
		}
	}

	/**
	 * Проверка параметров поля "Название модуля"
	 *
	 * @return void
	 */
	public function validate_variable_module_name()
	{
		if(! empty($_POST["module_name"]))
		{
			$modules = $this->diafan->all_modules;
			$modules = $this->diafan->array_column($modules, "name");
			$module = $this->diafan->filter($_POST, "string", "module_name");
			if(empty($module) || ! in_array($module, $modules) || ! Custom::exists('modules/'.$module.'/'.$module.'.cron.php'))
			{
				$this->diafan->set_error(
					"module_name",
					"Неверно задано имя модуля."
				);
			}
			else
			{
				Custom::inc('includes/cron.php');
				Custom::inc('modules/'.$module.'/'.$module.'.cron.php');
				$class = ucfirst($module).'_cron';
				if(! class_exists($class) || ! is_subclass_of($class, 'Cron'))
				{
					$this->diafan->set_error(
						"method",
						"Неверно задано имя модуля."
					);
				}
			}
		}
	}

	/**
	 * Проверка параметров поля "Название метода"
	 *
	 * @return void
	 */
	public function validate_variable_method()
	{
		if(! empty($_POST["method"]))
		{
			$modules = $this->diafan->all_modules;
			$modules = $this->diafan->array_column($modules, "name");
			$module = $this->diafan->filter($_POST, "string", "module_name");
			$method = $this->diafan->filter($_POST, "string", "method");
			if(empty($module) || ! in_array($module, $modules) || ! Custom::exists('modules/'.$module.'/'.$module.'.cron.php') || empty($method))
			{
				$this->diafan->set_error(
					"method",
					"Неверно задано имя метода."
				);
			}
			else
			{
				Custom::inc('includes/cron.php');
				Custom::inc('modules/'.$module.'/'.$module.'.cron.php');
				$class = ucfirst($module).'_cron';
				if(! class_exists($class) || ! is_subclass_of($class, 'Cron'))
				{
					$this->diafan->set_error(
						"method",
						"Неверно задано имя метода."
					);
				}
				else
				{
					$object = new $class($this->diafan);
					if(empty($object->methods) || ! array_key_exists($method, $object->methods))
					{
						$this->diafan->set_error(
							"method",
							"Неверно задано имя метода."
						);
					}
					unset($object);
				}
			}
		}
	}

	/**
	 * Проверка параметров поля "Параметры инициализации в формате JSON"
	 *
	 * @return void
	 */
	public function validate_variable_params()
	{
		if(! empty($_POST["params"]))
		{
			$params = $this->diafan->filter($_POST, "string", "params");
			$params = str_replace('&quot;', '"', $params);
			if(! empty($params))
			{
				Custom::inc('includes/json.php');
				$params = Json::decode($params);
				$code_error = json_last_error();
				if($code_error !== JSON_ERROR_NONE)
				{
					$last_error = Json::last_error();
					$this->diafan->set_error(
						"params",
						"Не верно заданы параметры. ".$last_error."."
					);
				}
				elseif(! empty($params) && ! is_array($params))
				{
					$this->diafan->set_error(
						"params",
						"Не верно заданы параметры: значения должны быть в виде массива."
					);
				}
				else
				{
					$params = serialize($params);
					if(empty($params))
					{
						$this->diafan->set_error(
							"params",
							"Не верно заданы параметры: значения должны быть в виде массива."
						);
					}
				}
			}
		}
	}

	/**
	 * Функция, выполняющаяся перед сохранением
	 *
	 * @return void
	 */
	public function save_before()
	{
		if(! empty($_POST["datetime"])
		&& false !== $this->diafan->_crontab->parser($this->diafan->filter($_POST, "string", "datetime"), $this->min_time))
		{
			$_POST["datetime"] = $this->diafan->_crontab->trim($this->diafan->filter($_POST, "string", "datetime"));
		}
		else $_POST["datetime"] = '';

		if(! empty($_POST["module_name"]) || ! empty($_POST["method"]))
		{
			$modules = $this->diafan->all_modules;
			$modules = $this->diafan->array_column($modules, "name");
			$module = $this->diafan->filter($_POST, "string", "module_name");
			$method = $this->diafan->filter($_POST, "string", "method");
			if(empty($module) || ! in_array($module, $modules) || ! Custom::exists('modules/'.$module.'/'.$module.'.cron.php') || empty($method))
			{
				$module = ''; $method = '';
			}
			else
			{
				Custom::inc('includes/cron.php');
				Custom::inc('modules/'.$module.'/'.$module.'.cron.php');
				$class = ucfirst($module).'_cron';
				if(! class_exists($class) || ! is_subclass_of($class, 'Cron'))
				{
					$module = ''; $method = '';
				}
				else
				{
					$object = new $class($this->diafan);
					if(empty($object->methods) || ! array_key_exists($method, $object->methods))
					{
						$module = ''; $method = '';
					}
					unset($object);
				}
			}
			$_POST["module_name"] = $module;
			$_POST["method"] = $method;
		}
	}

	/**
	 * Сохранение поля "Параметры инициализации в формате JSON"
	 *
	 * @return void
	 */
	public function save_variable_params()
	{
		if(! empty($_POST["params"]))
		{
			$params = $this->diafan->filter($_POST, "string", "params");
			$params = str_replace('&quot;', '"', $params);
			if(! empty($params))
			{
				Custom::inc('includes/json.php');
				$params = Json::decode($params);
				$code_error = json_last_error();
				if($code_error === JSON_ERROR_NONE && ! empty($params) && is_array($params))
				{
					$params = serialize($params);
					if(! empty($params))
					{
						$this->diafan->set_query("params='%s'");
						$this->diafan->set_value($params);
						return;
					}
				}
			}
		}
		$this->diafan->set_query("params='%s'");
		$this->diafan->set_value('');
	}
}
