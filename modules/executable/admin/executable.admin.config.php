<?php
/**
 * Настройки модуля
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
 * Executable_admin_config
 */
class Executable_admin_config extends Frame_admin
{
	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'base' => array (
			'hr1' => array(
				'type' => 'title',
				'name' => 'Основные',
			),
			'max_global_execute' => array(
				'type' => 'numtext',
				'name' => 'Количество процессов',
				'help' => 'Максимальное количество фоновых процессов. Количество процессов не может быть меньше одного.',
			),
			'max_module_execute' => array(
				'type' => 'numtext',
				'name' => 'Количество процессов модуля',
				'help' => 'Максимальное количество фоновых процессов одного модуля.',
			),
			'sys_visible' => array(
				'type' => 'checkbox',
				'name' => 'Показывать системные процессы',
			),
			'token' => array(
				'type' => 'none',
				'name' => 'Токен Exec',
				'help' => 'Электронный ключ.',
				'no_save' => true,
			),
			'hr2' => array(
				'type' => 'title',
				'name' => 'Хранение записей процессов',
			),
			'gc_maxlifetime' => array(
				'type' => 'numtext',
				'name' => 'Время жизни записи о завершённых процессах',
				'help' => 'Отсрочка времени в секундах, после которой данные будут рассматриваться как "мусор" и потенциально будут удалены. Например, 86400 - 1 день.',
			),
			'maxlifetime' => array(
				'type' => 'numtext',
				'name' => 'Время жизни всех записей',
				'help' => 'Отсрочка времени в секундах, после которой любые данные (в том числе данные отложенных или невыполненных процессов) будут рассматриваться как "мусор" и потенциально будут удалены. Например, 1209600 - 14 дней.',
			),
			'maxlimitrows' => array(
				'type' => 'numtext',
				'name' => 'Максимальное число всех записей',
				'help' => 'При привышении лимита любые записи рассматриваются, как "мусор", и потенциально будут удалены.',
			),
			'hr3' => array(
				'type' => 'title',
				'name' => 'Память CMS',
			),
			'mod_delete_memory_execute' => array(
				'type' => 'checkbox',
				'name' => 'Сбросить содержание памяти контроллера фоновых процессов',
				'help' => 'Если отметить, внутренняя память контроллера фоновых процессов будет удалена. Галка при этом не останется отмечена. Рекомендуется сбрасывать память, только после завершения всех фоновых процессов. (Веб-мастеру и программисту. Не меняйте этот параметр, если не уверены в результате!)',
				'no_save' => true,
			),
			'mod_delete_memory' => array(
				'type' => 'checkbox',
				'name' => 'Сбросить содержание всей памяти CMS',
				'help' => 'Если отметить, вся внутренняя память CMS будет удалена. Галка при этом не останется отмечена. Рекомендуется сбрасывать память, только после завершения всех фоновых процессов. (Веб-мастеру и программисту. Не меняйте этот параметр, если не уверены в результате!)',
				'no_save' => true,
			),
		),
	);

	/**
	 * @var array названия табов
	 */
	public $tabs_name = array(
		'base' => 'Основные настройки',
	);

	/**
	 * @var array настройки модуля
	 */
	public $config = array (
		'tab_card', // использование вкладок
		'config', // файл настроек модуля
	);

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		Custom::inc('modules/executable/executable.inc.php');
	}

	/**
	 * Подготавливает конфигурацию модуля
	 *
	 * @return void
	 */
	public function prepare_config()
	{
		if(! defined('MOD_DEVELOPER') || ! MOD_DEVELOPER)
		{
			$this->diafan->variable_disabled("max_global_execute", true);
			$this->diafan->variable_disabled("max_module_execute", true);
			$this->diafan->variable_disabled("gc_maxlifetime", true);
			$this->diafan->variable_disabled("maxlifetime", true);
			$this->diafan->variable_disabled("maxlimitrows", true);
		}
		else
		{
			$this->diafan->variable_disabled("max_global_execute", false);
			$this->diafan->variable_disabled("max_module_execute", false);
			$this->diafan->variable_disabled("gc_maxlifetime", false);
			$this->diafan->variable_disabled("maxlifetime", false);
			$this->diafan->variable_disabled("maxlimitrows", false);
		}
		if($this->diafan->_executable->count())
		{
			$this->diafan->variable_disabled("mod_delete_memory_execute", true);
			$this->diafan->variable_disabled("mod_delete_memory", true);
		}
		else
		{
			$this->diafan->variable_disabled("mod_delete_memory_execute", false);
			$this->diafan->variable_disabled("mod_delete_memory", false);
		}
	}

	/**
	 * Получает значение поля
	 *
	 * @param string $field название поля
	 * @param mixed $default значение по умолчанию
	 * @param boolean $save записать значение по умолчанию
	 * @return mixed
	 */
	public function values($field, $default = false, $save = false)
	{
		$value = parent::__call('values', array($field, $default, $save));
		if(! defined('MOD_DEVELOPER') || ! MOD_DEVELOPER)
		{
			if($field == 'max_global_execute') $value = Executable_inc::MAX_GLOBAL_EXECUTE;
			if($field == 'max_module_execute') $value = Executable_inc::MAX_MODULE_EXECUTE;
			if($field == 'gc_maxlifetime')
			{
				$max_execution_time = (MAX_EXECUTION_TIME < Executable_inc::MIN_EXECUTION_TIME ? Executable_inc::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
				$value = Executable_inc::GS_MAXLIFETIME; // значение в секундах: 86400 = 1 день
				$value = $value < $max_execution_time ? $max_execution_time : $value;
			}
			if($field == 'maxlifetime')
			{
				$max_execution_time = (MAX_EXECUTION_TIME < Executable_inc::MIN_EXECUTION_TIME ? Executable_inc::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
				$value = Executable_inc::MAXLIFETIME; // значение в секундах: 1209600 = 14 дней
				$value = $value < $max_execution_time ? $max_execution_time : $value;
			}
			if($field == 'maxlimitrows') $value = Executable_inc::MAXLIMITROWS;
		}
		elseif(empty($value))
		{
			if($field == 'max_global_execute') $value = Executable_inc::MAX_GLOBAL_EXECUTE;
			if($field == 'max_module_execute') $value = Executable_inc::MAX_MODULE_EXECUTE;
			if($field == 'gc_maxlifetime')
			{
				$max_execution_time = (MAX_EXECUTION_TIME < Executable_inc::MIN_EXECUTION_TIME ? Executable_inc::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
				$value = Executable_inc::GS_MAXLIFETIME; // значение в секундах: 86400 = 1 день
				$value = $value < $max_execution_time ? $max_execution_time : $value;
			}
			if($field == 'maxlifetime')
			{
				$max_execution_time = (MAX_EXECUTION_TIME < Executable_inc::MIN_EXECUTION_TIME ? Executable_inc::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
				$value = Executable_inc::MAXLIFETIME; // значение в секундах: 1209600 = 14 дней
				$value = $value < $max_execution_time ? $max_execution_time : $value;
			}
			if($field == 'maxlimitrows') $value = Executable_inc::MAXLIMITROWS;
		}
		else
		{
			if($field == 'max_global_execute')
			{
				$value = $value < Executable_inc::MAX_GLOBAL_EXECUTE ? Executable_inc::MAX_GLOBAL_EXECUTE : $value;
				$value = $value > Executable_inc::LIMIT_GLOBAL_EXECUTE ? Executable_inc::LIMIT_GLOBAL_EXECUTE : $value;
			}
			if($field == 'max_module_execute')
			{
				$max_global_execute = $this->diafan->values('max_global_execute');
				$max_global_execute = ($max_global_execute < Executable_inc::MAX_GLOBAL_EXECUTE
					? Executable_inc::MAX_GLOBAL_EXECUTE
					: $max_global_execute);
				$max_global_execute = ($max_global_execute > Executable_inc::LIMIT_GLOBAL_EXECUTE
					? Executable_inc::LIMIT_GLOBAL_EXECUTE
					: $max_global_execute);
				$value = $value > $max_global_execute ? $max_global_execute : $value;
			}
			if($field == 'gc_maxlifetime')
			{
				$max_execution_time = (MAX_EXECUTION_TIME < Executable_inc::MIN_EXECUTION_TIME ? Executable_inc::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
				$value = $value < $max_execution_time ? $max_execution_time : $value;
			}
			if($field == 'maxlifetime')
			{
				$max_execution_time = (MAX_EXECUTION_TIME < Executable_inc::MIN_EXECUTION_TIME ? Executable_inc::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
				$gc_maxlifetime = $this->diafan->values('gc_maxlifetime');
				$gc_maxlifetime = $gc_maxlifetime < $max_execution_time ? $max_execution_time : $gc_maxlifetime;
				$value = $value < $gc_maxlifetime ? $gc_maxlifetime : $value;
			}
			if($field == 'maxlimitrows')
			{
				$max_global_execute = $this->diafan->values('max_global_execute');
				$max_global_execute = ($max_global_execute < Executable_inc::MAX_GLOBAL_EXECUTE
					? Executable_inc::MAX_GLOBAL_EXECUTE
					: $max_global_execute);
				$max_global_execute = ($max_global_execute > Executable_inc::LIMIT_GLOBAL_EXECUTE
					? Executable_inc::LIMIT_GLOBAL_EXECUTE
					: $max_global_execute);
				$value = $value < $max_global_execute ? $max_global_execute : $value;
				$value = $value > Executable_inc::MAXLIMITROWS ? Executable_inc::MAXLIMITROWS : $value;
			}
		}
		return $value;
	}

	/**
	 * Проверка параметров поля "Количество процессов"
	 *
	 * @return void
	 */
	public function validate_config_variable_max_global_execute()
	{
		if(! empty($_POST["max_global_execute"]))
		{
			$max_global_execute = $this->diafan->filter($_POST, "integer", "max_global_execute");
			if($max_global_execute < Executable_inc::MAX_GLOBAL_EXECUTE || $max_global_execute > Executable_inc::LIMIT_GLOBAL_EXECUTE)
			{
				$this->diafan->set_error(
					"max_global_execute",
					"Максимальное количество процессов должно быть не меньше ".Executable_inc::MAX_GLOBAL_EXECUTE
					." и не больше ".Executable_inc::LIMIT_GLOBAL_EXECUTE."."
				);
			}
		}
	}

	/**
	 * Проверка параметров поля "Количество процессов модуля"
	 *
	 * @return void
	 */
	public function validate_config_variable_max_module_execute()
	{
		if(! empty($_POST["max_module_execute"]))
		{
			$max_global_execute = $this->diafan->filter($_POST, "integer", "max_global_execute");
			$max_global_execute = ($max_global_execute < Executable_inc::MAX_GLOBAL_EXECUTE
				? Executable_inc::MAX_GLOBAL_EXECUTE
				: $max_global_execute);
			$max_global_execute = ($max_global_execute > Executable_inc::LIMIT_GLOBAL_EXECUTE
				? Executable_inc::LIMIT_GLOBAL_EXECUTE
				: $max_global_execute);
			$max_module_execute = $this->diafan->filter($_POST, "integer", "max_module_execute");
			if($max_module_execute > $max_global_execute)
			{
				$this->diafan->set_error(
					"max_module_execute",
					"Максимальное количество процессов должно быть не больше количества всех процессов (".$max_global_execute.")."
				);
			}
		}
	}

	/**
	 * Проверка параметров поля "Время жизни записи о завершённых процессах"
	 *
	 * @return void
	 */
	public function validate_config_variable_gc_maxlifetime()
	{
		if(! empty($_POST["gc_maxlifetime"]))
		{
			$gc_maxlifetime = $this->diafan->filter($_POST, "integer", "gc_maxlifetime");
			$max_execution_time = (MAX_EXECUTION_TIME < Executable_inc::MIN_EXECUTION_TIME ? Executable_inc::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
			if($gc_maxlifetime < $max_execution_time)
			{
				$this->diafan->set_error(
					"gc_maxlifetime",
					"Время жизни записи о завершённых процессах не может быть меньше лимита (".$max_execution_time." сек.), выделенного на выполнение скрипта."
				);
			}
		}
	}

	/**
	 * Проверка параметров поля "Время жизни всех записей"
	 *
	 * @return void
	 */
	public function validate_config_variable_maxlifetime()
	{
		if(! empty($_POST["maxlifetime"]))
		{
			$gc_maxlifetime = $this->diafan->filter($_POST, "integer", "gc_maxlifetime");
			$max_execution_time = (MAX_EXECUTION_TIME < Executable_inc::MIN_EXECUTION_TIME ? Executable_inc::MIN_EXECUTION_TIME : MAX_EXECUTION_TIME);
			$gc_maxlifetime = $gc_maxlifetime < $max_execution_time ? $max_execution_time : $gc_maxlifetime;
			$maxlifetime = $this->diafan->filter($_POST, "integer", "maxlifetime");
			if($maxlifetime < $gc_maxlifetime)
			{
				$this->diafan->set_error(
					"maxlifetime",
					"Время жизни всех записей не может быть меньше времени жизни записей о завершённых процессах (".$gc_maxlifetime." сек.)."
				);
			}
		}
	}

	/**
	 * Проверка параметров поля "Количество процессов"
	 *
	 * @return void
	 */
	public function validate_config_variable_maxlimitrows()
	{
		if(! empty($_POST["maxlimitrows"]))
		{
			$max_global_execute = $this->diafan->filter($_POST, "integer", "max_global_execute");
			$max_global_execute = ($max_global_execute < Executable_inc::MAX_GLOBAL_EXECUTE
				? Executable_inc::MAX_GLOBAL_EXECUTE
				: $max_global_execute);
			$max_global_execute = ($max_global_execute > Executable_inc::LIMIT_GLOBAL_EXECUTE
				? Executable_inc::LIMIT_GLOBAL_EXECUTE
				: $max_global_execute);
			$maxlimitrows = $this->diafan->filter($_POST, "integer", "maxlimitrows");
			if($maxlimitrows < $max_global_execute || $maxlimitrows > Executable_inc::MAXLIMITROWS)
			{
				$this->diafan->set_error(
					"maxlimitrows",
					"Максимальное число всех записей должно быть не меньше количества всех процессов (".$max_global_execute.") и не больше ".Executable_inc::MAXLIMITROWS."."
				);
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
		if(! empty($_POST["mod_delete_memory_execute"]))
		{
			if(! $this->diafan->_executable->count())
			{
				$this->diafan->_memory->delete("", "executable");
			}
		}

		if(! empty($_POST["mod_delete_memory"]))
		{
			if(! $this->diafan->_executable->count())
			{
				$this->diafan->_memory->delete("", array());
			}
		}
	}
}
