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
 * Cache_memcached
 *
 * Кэширование при помощи MemCached
 */
class Cache_memcached implements Cache_interface
{
	/**
	 * @var object объект MemCached
	 */
	private $memcached;

	/**
	 * @var string уникальны код
	 */
	private $ukey;

	/**
	 * @var string префикс
	 */
	private $prefix;

	/**
	 * Конструктор класса
	 *
	 * @param array $options параметры класса
	 * @return void
	 */
	public function __construct($options = null)
	{
		$this->memcached = new Memcached();
		$this->memcached->addServer(CACHE_MEMCACHED_HOST, CACHE_MEMCACHED_PORT);
		$this->ukey = md5(DB_PREFIX.DB_URL);

		$this->prefix = 'cache';
		if($options && is_array($options))
		{
			$this->prefix = ! empty($options["prefix"]) ? $options["prefix"] : $this->prefix;
		}
		$this->prefix = md5($this->prefix);
	}

	/**
	 * Закрывает ранее открытое соединение
	 *
	 * @return void
	 */
	public function close()
	{
		$a = $this->memcached->getVersion();
		$version = 0;
		foreach($a as $k => $v)
		{
			$version = $v;
		}
		if($this->memcached && $version >= '2.0')
		{
			$this->memcached->quit();
		}
	}

	/**
	 * Проверяет параметры подключения
	 *
	 * @param string $host хост
	 * @param string $port порт
	 * @return boolean
	 */
	public static function check($host, $port)
	{
		$memcached = new Memcached();
		$memcached->addServer($host, $port);
		return $memcached ? true : false;
	}

	/**
	 * Читает кэш модуля $module с меткой $name.
	 *
	 * @param string|array $name метка кэша
	 * @param string $module название модуля
	 * @param boolean $refresh обновление кэша перед чтением
	 * @return mixed
	 */
	public function get($name, $module, $refresh)
	{
		return $this->memcached->get($this->ukey.$this->prefix.$module.$name);
	}

	/**
	 * Сохраняет данные $data для модуля $module с меткой $name
	 *
	 * @param mixed $data данные, сохраняемые в кэше
	 * @param string|array $name метка кэша
	 * @param string $module название модуля
	 * @return void
	 */
	public function save($data, $name, $module)
	{
		$this->memcached->set($this->ukey.$this->prefix.$module.$name, $data);
	}

	/**
	 * Удаляет кэш для модуля $module с меткой $name. Если функция вызвана с пустой меткой, то удаляется весь кэш для модуля $module
	 *
	 * @param string $name метка кэша
	 * @param string $module название модуля
	 * @return void
	 */
	public function delete($name, $module)
	{
		if(! $module)
		{
			$keys = $this->memcached->getAllKeys();
			if(! empty($keys))
			{
				foreach($keys as $key)
				{
					if(strpos($key, $this->ukey.$this->prefix) !== 0) continue;
					$this->memcached->delete($key);
				}
			}
		}
		elseif(! $name)
		{
			$keys = $this->memcached->getAllKeys();
			if(! empty($keys))
			{
				foreach($keys as $key)
				{
					if(strpos($key, $this->ukey.$this->prefix.$module) !== 0) continue;
					$this->memcached->delete($key);
				}
			}
		}
		else
		{
			$this->memcached->delete($this->ukey.$this->prefix.$module.$name);
		}
	}

	/**
	 * Очистка внутреннего кэша
	 *
	 * @return void
	 */
	public function refresh() {}

	/**
	 * Полная очистка кэша
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->memcached->flush();
	}
}
