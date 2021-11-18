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
 * Кэширование в файлах
 */
class Cache_files implements Cache_interface
{
	/**
	 * @var array текущий кэш
	 */
	private $cache;

	/**
	 * @var string путь до папки относительно корня сайта
	 */
	private $dir_path;

	/**
	 * @var integer максимальное количество файлов в кэше для одного модуля
	 */
	private $max_files;

	/**
	 * Конструктор класса
	 *
	 * @param array $options параметры класса
	 * @return void
	 */
	public function __construct($options = null)
	{
		$this->dir_path = 'cache';
		$this->max_files = 300;
		if($options && is_array($options))
		{
			$this->dir_path = ! empty($options["dir_path"]) ? $options["dir_path"] : $this->dir_path;
			$this->max_files = ! empty($options["max_files"]) ? $options["max_files"] : $this->max_files;
		}
		$access_close = 'Options -Indexes
		<files *.txt>
		<IfModule mod_authz_core.c>
		Require all denied
		</IfModule>
		<IfModule !mod_authz_core.c>
		Order deny,allow
		Deny from all
		</IfModule>
		</files>';
		Custom::inc('includes/file.php');
		File::create_dir($this->dir_path, $access_close);
	}

	/**
	 * Закрывает ранее открытое соединение
	 *
	 * @return void
	 */
	public function close(){}

	/**
	 * Читает кэш модуля $module с меткой $name
	 *
	 * @param string|array $name метка кэша
	 * @param string $module название модуля
	 * @param boolean $refresh обновление кэша перед чтением
	 * @return mixed
	 */
	public function get($name, $module, $refresh)
	{
		if($refresh && ! empty($this->cache[$module][$name]))
		{
			unset($this->cache[$module][$name]);
		}

		if(empty($this->cache[$module][$name]))
		{
			$this->inc_cache_file($name, $module);
		}

		if(! isset($this->cache[$module][$name]))
		{
			return false;
		}
		return unserialize($this->cache[$module][$name]);
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
		$this->cache[$module][$name] = serialize($data);
		$this->write_cache($name, $module);
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
		if (! $module)
		{
			if(! $d = dir(ABSOLUTE_PATH.$this->dir_path))
			{
				throw new Cache_exception('Папка '.ABSOLUTE_PATH.$this->dir_path.' не существует. Создайте папку и установите права на запись (777).');
			}
			$error = '';
			try
			{
				while ($entry = $d->read())
				{
					if ($entry != "." and $entry != ".." and $entry != ".htaccess")
					{
						if (is_dir(ABSOLUTE_PATH.$this->dir_path.'/'.$entry))
						{
							File::delete_dir($this->dir_path.'/'.$entry);
						}
						else
						{
							File::delete_file($this->dir_path.'/'.$entry);
						}
					}
				}
			}
			catch (Exception $e)
			{
				$error .= $e->getMessage()."\n";
			}

			$this->cache = null;

			$d->close();
			if($error)
			{
				throw new Cache_exception($error);
			}
		}
		elseif (! $name)
		{
			File::delete_dir($this->dir_path.'/'.$module);
			if(isset($this->cache[$module])) unset($this->cache[$module]);
		}
		else
		{
			if(isset($this->cache[$module][$name])) $this->cache[$module][$name] = '';
			File::delete_file($this->dir_path.'/'.$module.'/'.$name.'.txt');
		}
	}

	/**
	 * Подключает файл с кэшем модуля
	 *
	 * @param string $name метка кэша
	 * @param string $module название модуля
	 * @return void
	 */
	private function inc_cache_file($name, $module)
	{
		if (empty($this->cache[$module][$name]) && file_exists(ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : '').'/'.($name ?: $this->empty_name()).'.txt'))
		{
			$this->cache[$module][$name] = file_get_contents(ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : '').'/'.($name ?: $this->empty_name()).'.txt');
		}
	}

	/**
	 * Записывает кэш в файл
	 *
	 * @param string $name метка кэша
	 * @param string $module название модуля
	 * @return void
	 */
	private function write_cache($name, $module)
	{
		if (! is_dir(ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : '')))
		{
			if(! mkdir(ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : ''), 0777))
			{
				throw new Cache_exception('Невозможно создать папку '.ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : '').'. Установите права на запись (777) для папки '.ABSOLUTE_PATH.$this->dir_path.'.');
			}
		}
		else
		{
			$c = 0;
			$d = dir(ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : ''));
			while($str = $d->read())
			{
				if(substr($str, 0, 1) != '.')
				{
					if(! is_dir(ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : '').'/'.$str))
					{
						$c++;
					}
				}
			}
			$d->close();
			if($c > $this->max_files)
			{
				File::delete_dir($this->dir_path.($module ? '/'.$module : ''));
				mkdir(ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : ''), 0777);
			}
		}

		if(! $fp = fopen(ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : '').'/'.($name ?: $this->empty_name()).'.txt', "wb"))
		{
			throw new Cache_exception('Невозможно записать файл '.ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : '').'/'.($name ?: $this->empty_name()).'. Установите права на запись (777) для на папку '.ABSOLUTE_PATH."cache".($module ? '/'.$module : '').' и для файла '.ABSOLUTE_PATH.$this->dir_path.($module ? '/'.$module : '').'/'.($name ?: $this->empty_name()).'.');
		}
		fwrite($fp, $this->cache[$module][$name]);
		fclose($fp);
	}

	/**
	 * Очистка внутреннего кэша
	 *
	 * @return void
	 */
	public function refresh()
	{
		$this->cache = null;
	}

	/**
	 * Полная очистка кэша
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->delete('', '');
	}

	/**
	 * Возвращает псевданим пустого имени метки кэша
	 *
	 * @return string
	 */
	public function empty_name()
	{
		static $empty_name;
		if(! $empty_name) $empty_name = md5('');
		return $empty_name;
	}
}
