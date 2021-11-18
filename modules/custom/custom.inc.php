<?php
/**
 * Подключение модуля
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
 * Custom_inc
 */
class Custom_inc extends Diafan
{
	/**
	 * @var array папки и файлы, индексируемые для точек возврата
	 */
	private $folders = array('adm', 'css', 'img', 'themes', 'modules', 'includes', 'plugins', 'js');

	/**
	 * @var array папки и файлы, не индексируемые для точек возврата
	 */
	private $exclude = array('adm/htmleditor', 'includes/custom.php');

	/**
	 * @var array массив объектов - установка модулей
	 */
	private $install = array();

	/**
	 * Генерирует тему из кастомизированных файлов
	 *
	 * @param boolean $enable активирует сгенерированную тему
	 * @return array
	 */
	public function generate($enable = false)
	{
		// получает все файлы в текущей системе
		$current_files = array();
		if ($dir = opendir(ABSOLUTE_PATH))
		{
			while (($file = readdir($dir)) !== false)
			{
				if ($file == '.' || $file == '..' || ! in_array($file, $this->folders) || in_array($file, $this->exclude))
					continue;

				if(is_dir(ABSOLUTE_PATH.$file))
				{
					$this->read_dir(ABSOLUTE_PATH, $file, $current_files);
				}
				else
				{
					if(is_readable(ABSOLUTE_PATH.$file))
					{
						$current_files[$file] = file_get_contents(ABSOLUTE_PATH.$file);
					}
				}
			}
			closedir($dir);
		}

		// получает все файлы из текущей точки возврата
		$return_id = DB::query_result("SELECT id FROM {update_return} WHERE current='1' LIMIT 1");
		$return_files = $this->diafan->_update->get_all_files($return_id);
		if(empty($return_files))
		{
			return 0;
		}

		// находит кастомизированные файлы
		$custom_diff = array(); $names = Custom::names();
		foreach($current_files as $k => $file)
		{
			if(! isset($return_files[$k]) || $this->is_diff($return_files[$k], $file))
			{
				$custom_diff[$k] = $file;
			}
			if(! empty($names))
			{
				foreach($names as $dummy => $name)
				{
					if($name && is_readable(ABSOLUTE_PATH.'custom/'.$name.'/'.$k)
					&& ! $this->is_diff($file, file_get_contents(ABSOLUTE_PATH.'custom/'.$name.'/'.$k)))
					{
						try
						{
							File::delete_file('custom/'.$name.'/'.$k);
						}
						catch (Exception $e){}
					}
				}
			}
		}

		// удаляем темы сайта, если в них нет фалов
		if(! empty($names))
		{
			$change_names = false;
			foreach($names as $dummy => $name)
			{
				if($name && is_dir(ABSOLUTE_PATH.'custom/'.$name))
				{
					if($this->delete_empty_dir('custom/'.$name))
					{
						Custom::del($name);
						DB::query("DELETE FROM {custom} WHERE name='%h' LIMIT 1", $name);
						$change_names = true;
					}
				}
			}
			if($change_names)
			{
				Custom::inc('includes/config.php');
				$config = new Config();
				$config->save(array('CUSTOM' => implode(',', Custom::names())), $this->diafan->_languages->all);
			}
		}

		// находит файлы из текущей точки возврата, которые были изменены
		$return_diff = array();
		foreach($return_files as $k => $file)
		{
			if(! isset($current_files[$k]) || $this->is_diff($current_files[$k], $file))
			{
				$return_diff[$k] = $file;
			}
		}
		if(! $custom_diff && ! $return_diff)
		{
			return 0;
		}


		// TO_DO: если нет текущей темы, создает новую тему, иначе добавляет кастомизированные файлы к текущей теме
		// if(! $name = Custom::name())
		// {
		// 	$name = 'custom_'.date("d_m_Y_H_i");
		// 	Custom::name($name);
		// 	DB::query("INSERT INTO {custom} (name, created, text) VALUES ('%s', %d, '%s')", $name, time(), $this->diafan->_('Автоматически сгенерированная тема.'));
		// 	Custom::inc('includes/config.php');
		// 	$config = new Config();
		// 	$config->save(array('CUSTOM' => $name), $this->diafan->_languages->all);
		// 	File::create_dir('custom/'.$name);
		// }

		$result = array("custom" => array(), "return" => array());

		if($custom_diff)
		{
			// TO_DO: добавляет кастомизированные файлы к новой теме
			$name = 'custom_'.date("d_m_Y_H_i");
			Custom::add($name);
			DB::query("INSERT INTO {custom} (name, created, text) VALUES ('%s', %d, '%s')", $name, time(), $this->diafan->_('Автоматически сгенерированная тема.'));
			if($enable)
			{
				Custom::inc('includes/config.php');
				$config = new Config();
				$config->save(array('CUSTOM' => implode(',', Custom::names())), $this->diafan->_languages->all);
				File::create_dir('custom/'.$name);
			}

			// добавляет кастомизированные файлы к автоматически созданной теме
			foreach($custom_diff as $k => $f)
			{
				if(! file_exists(ABSOLUTE_PATH.'custom/'.$name.'/'.$k))
				{
					try
					{
						File::save_file($f, 'custom/'.$name.'/'.$k);
					}
					catch (Exception $e){}
					$result["custom"][] = $k;
				}
			}

			// очищает основную систему от кастомизированных файлов
			foreach($custom_diff as $k => $f)
			{
				if(! isset($return_diff[$k]))
				{
					try
					{
						File::delete_file($k);
					}
					catch (Exception $e){}
				}
			}
		}

		// добавляет все файлы из текущей точки возврата
		foreach($return_diff as $k => $f)
		{
			if($f != 'deleted' && ! in_array($k, array('upgrade.php', 'downgrade.php')))
			{
				try
				{
					File::save_file($f, $k);
				}
				catch (Exception $e){}
				$result["return"][] = $k;
			}
		}
		return $result;
	}

	/**
	 * Читает папку в файлах точки
	 *
	 * @param string $absolute_path абсолютный путь к директории
	 * @param string $path путь до файлов относительно указанного абсолютного пути
	 * @param array $files получаемые файлы точки
	 * @return void
	 */
	private function read_dir($absolute_path, $path, &$files)
	{
		if(is_readable($absolute_path.($path ? $path.'/' : ''))
		&& is_dir($absolute_path.($path ? $path.'/' : ''))
		&& ($dir = opendir($absolute_path.($path ? $path.'/' : ''))))
		{
			while (($file = readdir($dir)) !== false)
			{
				if(($path ? $path.'/' : '').$file == USERFILES || in_array(($path ? $path.'/' : '').$file, $this->exclude))
				{
					continue;
				}
				if ($file != '.' && $file != '..')
				{
					if(is_dir($absolute_path.($path ? $path.'/' : '').$file))
					{
						$this->read_dir($absolute_path, ($path ? $path.'/' : '').$file, $files);
					}
					else
					{
						if(is_readable($absolute_path.($path ? $path.'/' : '').$file))
						{
							$files[($path ? $path.'/' : '').$file] = file_get_contents($absolute_path.($path ? $path.'/' : '').$file);
						}
					}
				}
			}
			closedir($dir);
		}
	}

	/**
	 * Читает директорию с учетом активных тем сайта
	 *
	 * @param string $path путь до директории относительно корня сайта
	 * @param mixed $names темы из числа активных тем сайта, исключаемые из чтения
	 * @return array
	 */
	public function get_dir($path, $names = false)
	{
		if(! is_array($names))
		{
			$names = array($names);
		}
		foreach($names as $key => $name)
		{
			if(! empty($name)) continue;
			unset($names[$key]);
		}

		$rows = array();
		if(! isset($this->cache["path"]))
		{
			$this->cache["path"] = array();
			if (is_dir(ABSOLUTE_PATH.$path) && is_readable(ABSOLUTE_PATH.$path) && ($dir = opendir(ABSOLUTE_PATH.$path)))
			{
				while (($file = readdir($dir)) !== false)
				{
					if ($file != '.' && $file != '..')
					{
						if(is_readable(ABSOLUTE_PATH.$path.'/'.$file))
						{
							$this->cache["path"][$path.'/'.$file] = $file;
						}
					}
				}
				closedir($dir);
			}
		}
		$rows = $this->cache["path"];
		if(Custom::names())
		{
			foreach(Custom::names() AS $name)
			{
				if(! empty($names) && in_array($name, $names))
				{
					continue;
				}
				if(! isset($this->cache["custom"][$name]))
				{
					$this->cache["custom"][$name] = array();
					if (is_dir(ABSOLUTE_PATH.'custom/'.$name.'/'.$path)
					&& is_readable(ABSOLUTE_PATH.'custom/'.$name.'/'.$path)
					&& ($dir = opendir(ABSOLUTE_PATH.'custom/'.$name.'/'.$path)))
					{
						while (($file = readdir($dir)) !== false)
						{
							if ($file != '.' && $file != '..')
							{
								if(is_readable(ABSOLUTE_PATH.'custom/'.$name.'/'.$path.'/'.$file))
								{
									$this->cache["custom"][$name][$path.'/'.$file] = $file;
								}
							}
						}
						closedir($dir);
					}
				}
				$rows = array_replace($rows, $this->cache["custom"][$name]);
			}
		}

		return $rows;
	}

	/**
	 * Удаляет пустые поддиректории в указанной директории
	 *
	 * @param string $path путь до директории относительно корня сайта
	 * @return boolean
	 */
	private function delete_empty_dir($path)
	{
		if(! $path)
		{
			return false; // throw new File_exception('Нельзя удалить корневую директорию.');
		}

		if(is_file(ABSOLUTE_PATH.$path))
		{
			return false;
		}
		if(! is_writable(ABSOLUTE_PATH.$path))
		{
			return false;
		}
		if(is_dir(ABSOLUTE_PATH.$path))
		{
			$empty_dir = true;
			foreach(scandir(ABSOLUTE_PATH.$path) as $p)
			{
				if(($p != '.') && ($p != '..'))
				{
					if(! $this->delete_empty_dir($path.DIRECTORY_SEPARATOR.$p))
					{
						$empty_dir = false;
					}
				}
			}
			if($empty_dir)
			{
				try { $return = rmdir($path); }
				catch (Exception $e) { $return = false; }
				return $return;
			}
			else return false;
		}
		return false;
	}



	/**
	 * Сравнение контента
	 *
	 * @param string $a содержание контента
	 * @param string $b содержание контента
	 * @param boolean $simple упрощенное сравнение
	 * @return boolean
	 */
	public function is_diff($a, $b, $simple = false)
	{
		if($simple)
		{
			return (str_replace("\r\n", "\n", $a) != str_replace("\r\n", "\n", $b));
		}
		if(! $a && ! $b) return false;
		if(! $a || ! $b) return true;
		$variable = array(0 => $a, 1 => $b);
		foreach($variable as $key => $value)
		{
			// Удаляем пробелы (или другие символы) из начала и конца строки
			$value = trim($value);
			// Удаляем комментарии (многострочные и однострочные): /*...*/ и //...
			$value = preg_replace('/(^(\s)*\/\/(.*?)$)|(\/\*(.*?)\*\/)/ims', '', $value);
			// однострочный комментарий в коде: code //...
			$value = preg_replace_callback(
				'/^(.*?)(\/\/)(.*?)$/im',
				function($m){
					if(! empty($m[0]))
					{
						$str = '';
						if(! $chars = preg_split('//u', $m[0], NULL, PREG_SPLIT_NO_EMPTY))
						{
							$chars = array();
						}
						$count = count($chars);
						$quote = $double_quote = false;
						// перебор без учета: &quot; и &#039;
						// а также без учета: \' и \"
						foreach($chars as $i => $char)
						{
							if($char == "'" && ! $double_quote) $quote = ! $quote;
							if($char == '"' && ! $quote) $double_quote = ! $double_quote;

							if(! $quote && ! $double_quote)
							{
								if($char == '/' && ($i+1 < $count) && $chars[$i+1] == $char)
								break;
							}

							$str .= $char;
						}
						$m[0] = $str;
					}
					return $m[0];
				},
				$value
			);
			// Чистим строки
			$pattern = array(
				// '/<\!--((?!noindex|\/noindex|googleoff|googleon)(.*?))-->/ims',	// комментарии HTML
				// '/\/\*([\\s\\S]*?)\*\//',	// комментарии CSS
				'/^\\s+|\\s+$/m',		// trim each line
				'/\s\s+/',					// двойные пробелы (в том числе табуляция)
				"/^\n$/m",					// пустые или состоящие только из пробелов строки
				// "/\r\n/",				// перевод строки и возврат коретки
				// "/\n/",					// перевод на новую строку
				// "/\n(<span|\w)/m",	// перевод на новую строку c последующим тегом SPAN или Буквой (буквы, цифры, подчеркивание)
				"/\n/m",					  // перевод на новую строку
			);
			$replacement = array(
				// '',
				// '',
				'',
				' ',
				'',
				// '',
				// '',
				// ' ${1}',
				'',
			);
			$value = preg_replace($pattern, $replacement, $value);
			// Удаляем:
			// " " (ASCII 32 (0x20)), обычный пробел.
			// "\t" (ASCII 9 (0x09)), символ табуляции.
			// "\n" (ASCII 10 (0x0A)), символ перевода строки.
			// "\r" (ASCII 13 (0x0D)), символ возврата каретки.
			// "\0" (ASCII 0 (0x00)), NUL-байт.
			// "\x0B" (ASCII 11 (0x0B)), вертикальная табуляция.
			$value = str_replace(array("\r\n", "\n", "\r", "\0", "\x0B"), array(" ", " ", " ", "", " "), $value);
			// Заменяем сдвоенные на одинарные пробелы
			$value = preg_replace('/\s+/', ' ', $value, -1);
			// Сохраняем результат обработки
			$variable[$key] = $value;
		}
		return ($variable[0] != $variable[1]);
	}

	/**
	 * Изменяет состояние темы
	 *
	 * @param mixed $array название темы или массив названий тем
	 * @param boolean $enable активирует тему
	 * @param boolean $sql выполняет дополнительные запросы к базе данных
	 * @return boolean
	 */
	public function set($array, $enable, $sql = false)
	{
		if(! is_array($array))
		{
			$array = array($array);
		}
		foreach($array as $key => $name)
		{
			if(! empty($name)) continue;
			unset($array[$key]);
		}
		if(empty($array))
		{
			return false;
		}

		$edit = false;
		$names = Custom::names();
		if ($enable)
		{
			foreach($array as $name)
			{
				if(! in_array($name, $names))
				{
					$edit = true;
					$names[] = $name;
					if($sql)
					{
						$this->query($name, true);
					}
				}
			}
		}
		else
		{
			$new_names = array();
			foreach($names as $n)
			{
				if(! in_array($n, $array))
				{
					$new_names[] = $n;
				}
				else
				{
					$edit = true;
					if($sql)
					{
						$this->query($n, false);
					}
				}
			}
			$names = $new_names;
		}
		if($edit)
		{
			$new_values = array('CUSTOM' => implode(',', $names));
			Custom::inc('includes/config.php');
			Config::save($new_values, $this->diafan->_languages->all);
		}
		return $edit;
	}

	/**
	 * Импортирует тему
	 *
	 * @param string $file_path архивный файл темы
	 * @param string $name название темы
	 * @return boolean
	 */
	public function import($file_path, $name)
	{
		if(defined('IS_DEMO') && IS_DEMO)
		{
			return false;
		}
		if ($name != '')
		{
			//File::delete_dir('custom/'.$name);
			File::create_dir('custom/'.$name);
			if(class_exists('ZipArchive'))
			{
				$paths = array();
				$zip = new ZipArchive;
				if ($zip->open($file_path) === true)
				{
					for($i = 0; $i < $zip->numFiles; $i++)
					{
						$file_name = $zip->getNameIndex($i);
						if($file_name && substr($file_name, 0, 1) != '/')
						{
							$file_name = '/'.$file_name;
						}
						if(substr($file_name, -1) == '/')
						{
							$arr = explode('/', $file_name);
							array_pop($arr);
							$file_name = array_pop($arr);
							File::create_dir('custom/'.$name.($arr ? '/'.implode('/', $arr) : '').'/'.$file_name);
						}
						else
						{
							File::save_file($zip->getFromName($zip->getNameIndex($i)), 'custom/'.$name.$file_name);
						}
					}
					$zip->close();
				}
			}
		}
		return File::check_dir('custom/' . $name);
	}

	/**
	 * Исполнение SQL-запросов в файле install.sql или uninstall.sql
	 *
	 * @param string $name название темы
	 * @param boolean $install определяет файл запросов: install или uninstall
	 * @return void
	 */
	public function query($name, $install = true)
	{
		$install = $install ? 'install' : 'uninstall';

		if(! file_exists(ABSOLUTE_PATH.'custom/'.$name.'/'.$install.'.sql'))
			return;

		Custom::inc("modules/service/admin/service.admin.db.php");
		$obj = new Service_admin_db($this->diafan);
		$obj->import_query(ABSOLUTE_PATH.'custom/'.$name.'/'.$install.'.sql', false);
	}

	/**
	 * Получает список всех модулей, которые можно установить
	 *
	 * @param mixed $names темы, для которых определяются модули (по умолчанию все активные темы)
	 * @return array
	 */
	public function get_modules($names = false)
	{
		$key = serialize($names);
		if(! isset($this->cache["get_modules"][$key]))
		{
			$request = $this->diafan->_executable->execute(array(
				"module" => "custom",
				"method" => "get_modules",
				"params" => array("names" => $names),
				"text"   => $this->diafan->_('Формирование списока модулей'),
				"forced" => true
			), REQUEST_ANSWER);
			$this->cache["get_modules"][$key] = (! empty($request["rows"]) ? $request["rows"] : array());
		}
		return $this->cache["get_modules"][$key];
	}

	/**
	 * Установка/удаление модулей
	 *
	 * @param mixed $modules название модуля или массив названий модулей
	 * @param boolean $enable маркер установки/удаления модулей
	 * @param mixed $names не активные темы, которые необходимо временно подключить для установки/удаления определенных в их коде модулей
	 * @return boolean
	 */
	public function set_modules($modules, $enable, $names = false)
	{
		if(empty($modules))
		{
			return false;
		}

		if(! is_array($modules))
		{
			$modules = array($modules);
		}

		if(! is_array($names))
		{
			$names = array($names);
		}
		foreach($names as $key => $name)
		{
			if(! empty($name)) continue;
			unset($names[$key]);
		}

		$globals_custom = $GLOBALS["custom"];
		if(! empty($names))
		{
			$customs = Custom::names();
			foreach($names as $name)
			{
				if(in_array($name, $customs))
				{
					continue;
				}
				Custom::add($name);
			}
		}

		$rows = $this->diafan->_custom->get_modules();
		if(empty($rows))
		{
			return false;
		}

		$langs = array();
		foreach($this->diafan->_languages->all as $l)
		{
			$langs[] = $l["id"];
		}

		if(! class_exists('Install'))
		{
			Custom::inc("includes/install.php");
		}
		foreach($rows as $module => $row)
		{
			if(isset($this->install[$module])) continue;

			if (Custom::exists('modules/'.$module.'/'.$module.'.install.php'))
			{
				Custom::inc('modules/'.$module.'/'.$module.'.install.php');
				$name = Ucfirst($module).'_install';
				$this->install[$module] = new $name($this->diafan);

				if($this->install[$module]->is_core)
					continue;

				$this->install[$module]->langs = $langs;
				$this->install[$module]->module = $module;
			}
		}

		foreach($rows as $module => $row)
		{
			if(! isset($this->install[$module])) continue;

			if(! in_array($module, $modules))
			{
				continue;
			}

			// удаление модуля
			if(! $enable)
			{
				if($row["installed"])
				{
					$this->install[$module]->uninstall();
				}
			}
			else
			{
				if(! $row["installed"])
				{
					$this->install[$module]->tables();
					$this->install[$module]->start(false);

					//установка прав на административную часть установленного модуля текущему пользователю
					if(! $this->diafan->_users->roles('all', 'all'))
					{
						$rs = DB::query_fetch_all("SELECT rewrite FROM {admin} WHERE BINARY rewrite='%s' OR BINARY rewrite LIKE '%s%%'", $module, $module);
						foreach ($rs as $r)
						{
							DB::query("INSERT INTO {users_role_perm} (role_id, perm, rewrite, type) VALUES (%d, 'all', '%s', 'admin')", $this->diafan->_users->role_id, $r["rewrite"]);
						}
					}
				}
			}
		}
		foreach ($rows as $module => $row)
		{
			if(! isset($this->install[$module])) continue;

			// удаление модуля
			if($enable &&  ! $row["installed"])
			{
				$this->install[$module]->action_post();
			}
		}

		$GLOBALS["custom"] = $globals_custom;
		return true;
	}
}
