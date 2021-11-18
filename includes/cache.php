<?php
/**
 * @package    DIAFAN.CMS
 *
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
 * Cache
 *
 * Кэширование
 */
class Cache
{
	/**
	 * @var object бэкэнд
	 */
	private $backend;

	/**
	 * @var boolean вид режима работы кэша
	 */
	private $memory;

	/**
	 * @var string метка кэша
	 */
	private $name;

	/**
	 * @var string название модуля
	 */
	private $module;

	/**
	 * Конструктор класса
	 *
	 * @param boolean $local_cache вид режима работы кэша
	 * @return \Cache
	 */
	public function __construct($memory = false)
	{
		$this->memory = $memory;
		if(! $this->memory && defined('IS_DEMO') && IS_DEMO)
		{
			return;
		}
		if(defined('CACHE_MEMCACHED') && CACHE_MEMCACHED)
		{
			$backend = 'memcached';
		}
		else
		{
			$backend = 'files';
		}
		switch($backend)
		{
			case 'files':
				Custom::inc('includes/cache/cache.files.php');
				if($this->memory) $this->backend = new Cache_files(array('dir_path' => 'tmp/memory'));
				else $this->backend = new Cache_files(array('dir_path' => 'cache'));
				break;

			case 'memcached':
				Custom::inc('includes/cache/cache.memcached.php');
				if($this->memory) $this->backend = new Cache_memcached(array('prefix' => 'memory'));
				else $this->backend = new Cache_memcached(array('prefix' => 'cache'));
				break;
		}
	}

	/**
	 * Закрывает ранее открытое соединение
	 *
	 * @return void
	 */
	public function close()
	{
		if(! $this->memory && defined('IS_DEMO') && IS_DEMO)
		{
			return;
		}
		try
		{
			if($this->backend)
			{
				$this->backend->close();
			}
		}
		catch (Cache_exception $e)
		{
			Dev::exception($e, 'error');
		}
	}

	/**
	 * Читает кэш модуля $module с меткой $name
	 *
	 * @param string|array $name метка кэша
	 * @param string $module название модуля
	 * @param binary $mode флаг работы с кэш: CACHE_DATA, CACHE_DEVELOPER, CACHE_GLOBAL
	 * @return mixed
	 */
	public function get($name, $module, $mode = CACHE_DATA)
	{
		if(! $this->memory && defined('IS_DEMO') && IS_DEMO)
		{
			return false;
		}
		/*
		если отключен кэш и режим только DATA
		или
		если режим разработчика и режим DEVELOPER
		*/
		if(! $this->memory && (MOD_DEVELOPER_CACHE && ($mode == CACHE_DATA) || MOD_DEVELOPER && ($mode == CACHE_DEVELOPER)))
			return false;

		$this->transform_param($name, $module);

		try
		{
			return $this->backend->get($this->name, $this->module, ($mode & CACHE_REFRESH));
		}
		catch (Cache_exception $e)
		{
			Dev::exception($e, 'error');
		}
	}

	/**
	 * Сохраняет данные $data для модуля $module с меткой $name
	 *
	 * @param mixed $data данные, сохраняемые в кэше
	 * @param string|array $name метка кэша
	 * @param string $module название модуля
	 * @param binary $mode флаг работы с кэш
	 * @return boolean
	 */
	public function save($data, $name, $module, $mode = CACHE_DATA)
	{
		if(! $this->memory && defined('IS_DEMO') && IS_DEMO)
		{
			return false;
		}
		if(! $this->memory && (MOD_DEVELOPER_CACHE && ($mode == CACHE_DATA) || MOD_DEVELOPER && ($mode == CACHE_DEVELOPER)))
			return false;

		$this->transform_param($name, $module);

		try
		{
			return $this->backend->save($data, $this->name, $this->module);
		}
		catch (Cache_exception $e)
		{
			Dev::exception($e, 'error');
		}
	}

	/**
	 * Удаляет кэш для модуля $module с меткой $name. Если функция вызвана с пустой меткой, то удаляется весь кэш для модуля $module
	 *
	 * @param string|array $name метка кэша
	 * @param string $module название модуля
	 * @return boolean
	 */
	public function delete($name, $module = '')
	{
		if(! $this->memory && defined('IS_DEMO') && IS_DEMO)
		{
			return false;
		}

		try
		{
			if(! $this->memory)
			{
				if($module == 'cache_extreme' && (! defined('CACHE_EXTREME') || ! CACHE_EXTREME))
				{
					return false;
				}
				$this->transform_param($name, $module);
				$result = $this->backend->delete($this->name, $this->module);
	
				// удаляет экстремальный кэш
				if(defined('CACHE_EXTREME') && CACHE_EXTREME && $module != 'cache_extreme')
				{
					$this->transform_param('', 'cache_extreme');
					$this->backend->delete($this->name, $this->module);
				}
			}
			else
			{
				$this->transform_param($name, $module);
				$result = $this->backend->delete($this->name, $this->module);
			}
		}
		catch (Cache_exception $e)
		{
			Dev::exception($e, 'error');
		}

		return $result;
	}

	/**
	 * Преобразует метку и название модуля для работы с кэшем
	 *
	 * @param string|array $name метка кэша
	 * @param string $module название модуля
	 * @return boolean true
	 */
	private function transform_param($name, $module)
	{
		if($name)
		{
			if (! is_array($name))
			{
				$this->name = md5($name);
			}
			else
			{
				$this->name = md5(serialize($name));
			}
		}
		else
		{
			$this->name = '';
		}
		if($module)
		{
			$this->module = md5($module);
		}
		else
		{
			$this->module = '';
		}
		return true;
	}

	/**
	 * Очистка внутреннего кэша
	 *
	 * @return boolean
	 */
	public function refresh()
	{
		if(! $this->memory && defined('IS_DEMO') && IS_DEMO)
		{
			return false;
		}

		try
		{
			$this->backend->refresh();
		}
		catch (Cache_exception $e)
		{
			Dev::exception($e, 'error');
		}
		return true;
	}

	/**
	 * Полная очистка кэша
	 *
	 * @return boolean
	 */
	public function flush()
	{
		if(! $this->memory && defined('IS_DEMO') && IS_DEMO)
		{
			return false;
		}

		try
		{
			$this->backend->flush();
		}
		catch (Cache_exception $e)
		{
			Dev::exception($e, 'error');
		}
		return true;
	}
}

/**
 * Cache_exception
 *
 * Исключение для кэширования
 */
class Cache_exception extends Exception{}

/**
 * Cache_interface
 *
 * Интерфейс бэкенда для работы с кэшем
 */
interface Cache_interface
{
	/**
	 * Закрывает ранее открытое соединение
	 *
	 * @return void
	 */
	public function close();

	/*
	 * Читает кэш модуля $module с меткой $name.
	 *
	 * @param string|array $name метка кэша
	 * @param string $module название модуля
	 * @param boolean $refresh обновление кэша перед чтением
	 * @return mixed
	 */
	public function get($name, $module, $refresh);

	/*
	 * Сохраняет данные $data для модуля $module с меткой $name
	 *
	 * @param mixed $data данные, сохраняемые в кэше
	 * @param string|array $name метка кэша
	 * @param string $module название модуля
	 * @return void
	 */
	public function save($data, $name, $module);

	/*
	 * Удаляет кэш для модуля $module с меткой $name. Если функция вызвана с пустой меткой, то удаляется весь кэш для модуля $module
	 *
	 * @param string $name метка кэша
	 * @param string $module название модуля
	 * @return void
	 */
	public function delete($name, $module);

	/*
	 * Очистка внутреннего кэша
	 *
	 * @return void
	 */
	public function refresh();

	/*
	 * Полная очистка кэша
	 *
	 * @return void
	 */
	public function flush();
}

/**
 * Cache const
 *
 * Константы для работы с кэшем
 */

// Флаг кэша, зависящего от MOD_DEVELOPER_CACHE
if(! defined('CACHE_DATA')) define('CACHE_DATA', 1 << 0);                           // 001
// Флаг кэша, зависящего от MOD_DEVELOPER и не зависящего от MOD_DEVELOPER_CACHE
if(! defined('CACHE_DEVELOPER')) define('CACHE_DEVELOPER', 1 << 1);                 // 010
// Флаг кэша, не зависящего от MOD_DEVELOPER_CACHE и MOD_DEVELOPER
if(! defined('CACHE_GLOBAL')) define('CACHE_GLOBAL', CACHE_DATA | CACHE_DEVELOPER); // 011
// Флаг обновления кэша
if(! defined('CACHE_REFRESH')) define('CACHE_REFRESH', 1 << 2);                     // 100
