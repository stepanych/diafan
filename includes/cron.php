<?php
/**
 * Каркас для задач CRON
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
 * Cron
 *
 * Абстрактный класс для задач CRON
 */
abstract class Cron extends Diafan
{
	/**
	 * @var string название модуля
	 */
	public $title = "";

	/**
	 * @var array названия методов
	 */
	public $methods = array();

	/**
	 * Подключает модель
	 *
	 * @return object|null
	 */
	public function __get($name)
	{
		if($name == 'model' || $name == 'inc')
		{
			$module = $this->diafan->current_module;
			if(! isset($this->cache[$name.'_'.$module]))
			{
				if(Custom::exists('modules/'.$module.'/'.$module.'.'.$name.'.php'))
				{
					Custom::inc('modules/'.$module.'/'.$module.'.'.$name.'.php');
					$class = ucfirst($module).'_'.$name;
					$this->cache[$name.'_'.$module] = new $class($this->diafan, $module);
				}
			}
			return $this->cache[$name.'_'.$module];
		}
		return NULL;
	}

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);

		if(empty($this->title))
		{
			$class_names = explode('_', strtolower(get_class($this)));
			$this->title = reset($class_names);
			$modules = $this->diafan->all_modules;
			if(! empty($modules))
			{
				foreach($modules as $row)
				{
					if($this->title != $row["name"]) continue;
					$this->title = ! empty($row["title"]) ? $row["title"] : $this->title;
					break;
				}
			}
		}
		if(empty($this->methods))
		{
			$class = new ReflectionClass(get_class($this));
			if($methods = $class->getMethods(ReflectionMethod::IS_PUBLIC))
			{
				foreach($methods as $method)
				{
					if(empty($method->name) || strpos($method->name, '__') === 0) continue;
					$this->methods[$method->name] = $method->name;
				}
			}
			unset($class);
		}
		$this->prepare_config();
	}

	/**
	 * Деструктор класса
	 *
	 * @return void
	 */
	public function __destruct()
	{

	}

	/**
	 * Подготавливает конфигурацию
	 *
	 * @return void
	 */
	protected function prepare_config() {}
}

/**
 * Cron_exception
 *
 * Исключение для запросов CRON
 */
class Cron_exception extends Exception{}
