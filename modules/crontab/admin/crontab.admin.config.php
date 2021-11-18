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
 * Crontab_admin_config
 */
class Crontab_admin_config extends Frame_admin
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
			'enable' => array(
				'type' => 'checkbox',
				'name' => 'Включить CRON',
				'help' => 'Если отмечено, то активируется расписание задач, которые будут потенциально активированы в указанное время.',
			),
			'check' => array(
				'type' => 'checkbox',
				'name' => 'Восстановление после сбоя',
				'help' => 'CRON может отключиться из-за ошибки на сайте или перезагрузки веб-сервера. Если отмечено, то CRON активируется автоматически при первом обращении к сайту. Важно: первое обращение к сайту в данном случае считается обращением, которое произошло не ранее чем через 4 секунды после ошибки или перезагрузки веб-сервера, спровоцировавшей отключение CRON.',
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
	 * Генерирует форму редактирования/добавления элемента
	 *
	 * @return void
	 */
	public function edit()
	{
		if(_LANG != $this->diafan->_languages->admin)
		{
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/crontab/config/');
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
		if($field == 'enable')
		{
			$value = !! $this->diafan->_executable->tick_status();
		}
		return $value;
	}

	/**
	 * Функция, выполняющаяся после сохранения перед редиректом
	 *
	 * @return void
	 */
	public function save_after()
	{
		$status_tick = !! $this->diafan->_executable->tick_status();
		if(! empty($_POST["enable"]) && ! $status_tick)
		{
			// запуск тика
			$this->diafan->_executable->tick();
		}
		elseif(empty($_POST["enable"]) && $status_tick)
		{
			// остановка тика
			$this->diafan->_executable->tick_delete(sprintf('Process is stopped in %s', date("H:i:s d.m.Y")));
			// TO_DO: альтернативная остановка тика
			// $this->diafan->_memory->delete(Executable_inc::CACHE_META_TICK, "executable");
		}
	}
}
