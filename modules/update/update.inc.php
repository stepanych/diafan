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
 * Update_inc
 */
class Update_inc extends Diafan
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
	 * Добавляет первую точку возврата
	 *
	 * @return void
	 */
	public function first_return()
	{
		$VERSION = Custom::version_core();

		DB::query("INSERT INTO {update_return} (id, name, current, created, version, `hash`) VALUES (1, 'Установка', '1', %d, '%h', '%h')", time(), $VERSION, $this->diafan->configmodules("hash", "update"));

		// создает  файл  .htaccess, чтобы закрыть доступ извне ко всем файлам точек возврата
		File::create_dir('return', true);

		if(! class_exists('ZipArchive'))
		{
			throw new Exception('Не доступно PHP-расширение ZipArchive. Обратитесь в техническую поддержку хостинга.');
		}
		$zip = new ZipArchive;
		if ($zip->open(ABSOLUTE_PATH.'return/1.zip', ZipArchive::CREATE) === true)
		{
			if ($dir = opendir(ABSOLUTE_PATH))
			{
				while (($file = readdir($dir)) !== false)
				{
					if ($file == '.' || $file == '..' || ! in_array($file, $this->folders) || in_array($file, $this->exclude))
						continue;

					if(is_dir(ABSOLUTE_PATH.$file))
					{
						$this->add_to_zip($zip, $file);
					}
					else
					{
						if(is_readable(ABSOLUTE_PATH.$file))
						{
							$zip->addFile(ABSOLUTE_PATH.$file, $file);
						}
					}
				}
				closedir($dir);
			}
			$zip->close();
		}
	}

	/**
	 * Получает обновленные файлы точки с содержимым
	 *
	 * @param integer $id идентификатор точки
	 * @return array
	 */
	public function get_files($id)
	{
		if(! $id || ! $id = DB::query_result("SELECT id FROM {update_return} WHERE id='%d' LIMIT 1", $id))
		{
			return array();
		}
		if(! file_exists(ABSOLUTE_PATH."return/".$id.".zip"))
		{
			if(! $this->recover_return($id))
			{
				return array();
			}
		}
		// return File::unzip('return/'.$id.'.zip');
		return $this->unzip('return/'.$id.'.zip');
	}

	/**
	 * Получает все файлы DIAFAN.CMS в точке с содержимым
	 *
	 * @param integer $id идентификатор точки
	 * @return array
	 */
	public function get_all_files($id)
	{
		$files = array();
		if(! $id)
		{
			return $files;
		}
		if(! class_exists('ZipArchive'))
		{
			throw new Exception('Не доступно PHP-расширение ZipArchive. Обратитесь в техническую поддержку хостинга.');
		}

		// получает все точки начиная от точки возврата и ранее
		$ids  = DB::query_fetch_value("SELECT id FROM {update_return} WHERE id<=%d ORDER BY id DESC", $id, "id");
		foreach($ids as $id)
		{
			if(! file_exists(ABSOLUTE_PATH."return/".$id.".zip"))
			{
				if(! $this->recover_return($id))
				{
					continue;
				}
			}
			// $array = File::unzip('return/'.$id.'.zip');
			$array = $this->unzip('return/'.$id.'.zip');
			foreach($array as $file => $value)
			{
				$exclude = false;
				foreach($this->exclude as $f)
				{
					if(strpos($file, $f) === 0)
					{
						$exclude = true;
						break;
					}
				}
				if(! $exclude)
				{
					$exclude = true;
					foreach($this->folders as $f)
					{
						if(strpos($file, $f) === 0)
						{
							$exclude = false;
							break;
						}
					}
				}
				if(! in_array($file, array('updrade.php', 'downgrade.php')) && ! $exclude && ! isset($files[$file]) && substr($file, -1) !== '/')
				{
					$files[$file] = $value;
				}
			}
		}
		return $files;
	}

	/**
	 * Добавляет файлы из директории в архив
	 *
	 * @param object $zip архив
	 * @param string $dir относительный путь до директории
	 * @return void
	 */
	private function add_to_zip(&$zip, $dir)
	{
		if(is_readable(ABSOLUTE_PATH.$dir) && ($ddir = opendir(ABSOLUTE_PATH.$dir)))
		{
			while (($file = readdir($ddir)) !== false)
			{
				if ($file != '.' && $file != '..' && ! in_array($dir.'/'.$file, $this->exclude))
				{
					if(is_dir(ABSOLUTE_PATH.$dir.'/'.$file))
					{
						$this->add_to_zip($zip, $dir.'/'.$file);
					}
					else
					{
						if(is_readable(ABSOLUTE_PATH.$dir.'/'.$file))
						{
							$zip->addFile(ABSOLUTE_PATH.$dir.'/'.$file, $dir.'/'.$file);
						}
					}
				}
			}
			closedir($ddir);
		}
	}

	/**
	 * Восстонавливает файл точки с содержимым
	 *
	 * @param integer $id идентификатор точки
	 * @param boolean $replace восстановление с заменой файла точки
	 * @return boolean
	 */
	public function recover_return($id, $replace = false)
	{
		if(! $id || ! $id = DB::query_result("SELECT id FROM {update_return} WHERE id='%d' LIMIT 1", $id))
		{
			return false;
		}
		if(file_exists(ABSOLUTE_PATH."return/".$id.".zip") && ! $replace)
		{
			return true;
		}
		if(! class_exists('ZipArchive'))
		{
			// throw new Exception('Не доступно PHP-расширение ZipArchive. Обратитесь в техническую поддержку хостинга.');
			return false;
		}
		$this->diafan->set_time_limit();

		$hash = DB::query_result("SELECT `hash` FROM {update_return} WHERE id='%d' LIMIT 1", $id);
		if($id == 1 && ! $hash)
		{
			return false;
		}
		elseif($id != 1 && ! $hash)
		{
			try
			{
				if(file_exists(ABSOLUTE_PATH.'return/'.$id.'.zip')) File::delete_file('return/'.$id.'.zip');
				File::copy_file('http'.(IS_HTTPS ? "s" : '').'://user.diafan.ru/api/file/get/update/'.$id.'.zip', 'return/'.$id.'.zip');
			}
			catch (Exception $e) {
				return false;
			}
			return file_exists(ABSOLUTE_PATH.'return/'.$id.'.zip');
		}

		$min = false; $min_hash = '';
		if($row = DB::query_fetch_array("SELECT id, `hash` FROM {update_return} WHERE id<%d ORDER BY id DESC LIMIT 1", $id))
		{
			if(file_exists(ABSOLUTE_PATH."return/".$row["id"].".zip") || $this->recover_return($row["id"]))
			{
				$min_hash = $row["hash"];
				$min = $row["id"];
			}
		}

		$url = $this->diafan->_account->uri('update', 'hash');
		$param = array("max_hash" => ($hash ?: ''), "max" => $id, "min_hash" => ($min_hash ?: ''), "min" => ($min ?: 1));
		if(! $response = $this->diafan->_client->request($url, $this->diafan->_account->token, $param))
		{
			return false;
		}
		$array_hash = ! empty($response["result"]) ? $response["result"] : array();
		if(empty($array_hash))
		{
			return false;
		}
		$file_path = false;
		File::create_dir('tmp/return', true);
		foreach ($array_hash as $uid => $uhash)
		{
			if($min && ($uhash == $min_hash || $uid == $min)) continue;
			$file = 'tmp/return/'.$uid.'.zip';
			if(file_exists(ABSOLUTE_PATH.$file)) File::delete_file($file);
			File::copy_file('http'.(IS_HTTPS ? "s" : '').'://user.diafan.ru/file/update/'.$uid.'/'.$uhash, $file);
			if(! file_exists(ABSOLUTE_PATH.$file)) continue;
			if($file_path !== false && file_exists(ABSOLUTE_PATH.$file_path))
			{
				// склейка точек
				$this->merge_return($file, $file_path, (! $min ? true : false));
				File::delete_file($file_path);
			}
			$file_path = $file;
		}
		if($file_path !== false && file_exists(ABSOLUTE_PATH.$file_path)
		&& $min == 1 && ! $min_hash)
		{
			$this->dissociate_return($file_path, "return/".$min.".zip", true);
		}
		$result = false;
		if($file_path !== false && file_exists(ABSOLUTE_PATH.$file_path))
		{
			if(file_exists(ABSOLUTE_PATH."return/".$id.".zip"))
			{
				File::delete_file("return/".$id.".zip");
			}
			File::upload_file(ABSOLUTE_PATH.$file_path, "return/".$id.".zip");
			chmod(ABSOLUTE_PATH."return/".$id.".zip", 0777);
			$result = true;
		}
		File::delete_dir('tmp/return');
		return $result;
	}

	/**
	 * Склейка файлов точек с содержимым
	 *
	 * @param string $main относительный путь до основного ZIP-файла
	 * @param string $filename относительный путь до второстипенного ZIP-файла
	 * @param boolean $start_point основная точка является исходной
	 * @return boolean
	 */
	public function merge_return($main, $slave, $start_point = false)
	{
		if(! $main || ! $slave || ! class_exists('ZipArchive') || ! file_exists(ABSOLUTE_PATH.$main) || ! file_exists(ABSOLUTE_PATH.$slave))
		{
			return false;
		}

		if(! class_exists('ZipArchive'))
		{
			// throw new Exception('Не доступно PHP-расширение ZipArchive. Обратитесь в техническую поддержку хостинга.');
			return false;
		}

		// $main_files = File::unzip($main);
		$main_files = $this->unzip($main);
		// $slave_files = File::unzip($slave);
		$slave_files = $this->unzip($slave);

		$zip = new ZipArchive;
		if($zip->open(ABSOLUTE_PATH.$main, ZIPARCHIVE::OVERWRITE) !== true)
		{
			return false;
		}

		foreach($main_files as $k => $f)
		{
			if(! isset($slave_files[$k]))
			{
				if(in_array($k, array('upgrade.php', 'downgrade.php')))
				{
					if($start_point) continue;
					$slave_files[$k] = '';
				}
				else $slave_files[$k] = $main_files[$k];
			}
		}
		foreach($slave_files as $k => $f)
		{
			if($start_point)
			{
				$exclude = false;
				foreach($this->exclude as $val)
				{
					if(strpos($k, $val) === 0) { $exclude = true; break; }
				}
				if(! $exclude)
				{
					$exclude = true;
					foreach($this->folders as $val)
					{
						if(strpos($k, $val) === 0) { $exclude = false; break; }
					}
				}
				$include = (! in_array($k, array('updrade.php', 'downgrade.php')) && ! $exclude && substr($k, -1) !== '/');
				if(! $include) continue;
			}
			// файлы upgrade и downgrade совмещаются с файлами в следующей точке
			if(in_array($k, array('upgrade.php', 'downgrade.php')))
			{
				if(isset($main_files[$k]))
				{
					if(! $f) $f = $main_files[$k];
					else $f .= "\n?>\n".$main_files[$k];
				}
			}
			elseif(isset($main_files[$k]))
			{
				$f = $main_files[$k];
			}
			if($start_point && $f == 'deleted') continue;
			$zip->addFromString($k, $f);
		}
		$zip->close();

		return true;
	}

	/**
	 * Расклейка файлов точек с содержимым
	 *
	 * @param string $main относительный путь до основного ZIP-файла
	 * @param string $filename относительный путь до второстипенного ZIP-файла
	 * @param boolean $del_post удалить файлы upgrade.php и downgrade.php
	 * @return boolean
	 */
	private function dissociate_return($main, $slave, $del_post = false)
	{
		if(! $main || ! $slave || ! class_exists('ZipArchive') || ! file_exists(ABSOLUTE_PATH.$main) || ! file_exists(ABSOLUTE_PATH.$slave))
		{
			return false;
		}

		if(! class_exists('ZipArchive'))
		{
			// throw new Exception('Не доступно PHP-расширение ZipArchive. Обратитесь в техническую поддержку хостинга.');
			return false;
		}

		// $main_files = File::unzip($main);
		$main_files = $this->unzip($main);
		// $slave_files = File::unzip($slave);
		$slave_files = $this->unzip($slave);

		$zip = new ZipArchive;
		if($zip->open(ABSOLUTE_PATH.$main, ZIPARCHIVE::OVERWRITE) !== true)
		{
			return false;
		}

		foreach($main_files as $k => $f)
		{
			if(isset($slave_files[$k]) && $slave_files[$k] == $f
			|| $del_post && in_array($k, array('upgrade.php', 'downgrade.php')))
			{
				continue;
			}
			$zip->addFromString($k, $f);
		}
		$zip->close();

		return true;
	}

	/**
	 * Получает контент файлов из архива
	 *
	 * @param string $filename относительный путь до ZIP-файла
	 * @return array
	 */
	private function unzip($filename)
	{
		$files = array();
		if(! $filename || ! class_exists('ZipArchive') || ! file_exists(ABSOLUTE_PATH.$filename))
		{
			return $files;
		}
		// if(! class_exists('ZipArchive'))
		// {
		// 	throw new Exception('Не доступно PHP-расширение ZipArchive. Обратитесь в техническую поддержку хостинга.');
		// }
		$zip = new ZipArchive;
		if ($zip->open(ABSOLUTE_PATH.$filename) !== true)
		{
			return $files;
		}
		for($i = 0; $i < $zip->numFiles; $i++)
		{
			$file = $zip->getNameIndex($i);
			if(substr($file, -1) !== '/')
			{
				$files[$file] = $zip->getFromName($file);
			}
		}
		$zip->close();
		return $files;
	}

	/**
	 * Применяет точку возврата
	 *
	 * @param integer $id номер точки
	 * @param boolean $mod_protected включает защищенный режим работы CMS после применения точки
	 * @return boolean
	 */
	public function current($id, $mod_protected = true)
	{
		if(! $id = DB::query_result("SELECT id FROM {update_return} WHERE id=%d LIMIT 1", $id))
		{
			return false;
		}

		$current_id = DB::query_result("SELECT id FROM {update_return} WHERE current='1' LIMIT 1");
		if($id == $current_id)
		{
			return false;
		}

		$this->diafan->_custom->generate(false);

		if($current_id > $id)
		{
			$type = 'down';
		}
		else
		{
			$type = 'up';
		}

		// возврат назад
		if($type == 'down')
		{
			// получает текущую точку и все точки до точки возврата
			$rows = DB::query_fetch_value("SELECT id FROM {update_return} WHERE id<=%d AND id>%d ORDER BY id DESC", $current_id, $id, "id");
			$down_files = array();
			foreach($rows as $r)
			{
				$files = $this->get_files($r);
				foreach($files as $k => $f)
				{
					switch($k)
					{
						case 'upgrade.php':
							break;

						// производит откат изменений в полученных точках
						case 'downgrade.php':
							File::save_file($f, 'return/downgrade.php');
							include(ABSOLUTE_PATH.'return/downgrade.php');
							File::delete_file('return/downgrade.php');
							break;

						// запоминает все обновленные файлы в полученных  точках
						default:
							if(! in_array($k, $down_files))
							{
								$down_files[] = $k;
							}
							break;
					}
				}
			}
			$files = $this->get_all_files($id);
			try
			{
				foreach($files as $k => $v)
				{
					File::save_file($v, $k);
				}
				foreach($down_files as $df)
				{
					if(! in_array($df, array_keys($files)))
					{
						$in_exclude = false;
						foreach($this->exclude as $f)
						{
							if($f == $df || preg_match('/^'.preg_quote($f, '/').'\//', $df))
							{
								$in_exclude = true;
							}
						}
						if($in_exclude)
						{
							continue;
						}
						$in_folders = false;
						foreach($this->folders as $f)
						{
							if(preg_match('/^'.$f.'\//', $df))
							{
								$in_folders = true;
							}
						}
						if(! $in_folders)
						{
							continue;
						}
						File::delete_file($df);
					}
				}
			}
			catch (Exception $e){}
		}
		// обновление вперед
		else
		{
			// получает все точки, старше текущей
			$rows = DB::query_fetch_value("SELECT id FROM {update_return} WHERE id>%d AND id<=%d ORDER BY id ASC", $current_id, $id, "id");
			foreach($rows as $r)
			{
				$files = $this->get_files($r);
				foreach($files as $k => $f)
				{
					switch($k)
					{
						case 'downgrade.php':
							break;

						// производит обновление в полученных точках
						case 'upgrade.php':
							File::save_file($f, 'return/upgrade.php');
							include(ABSOLUTE_PATH.'return/upgrade.php');
							File::delete_file('return/upgrade.php');
							break;

						// заменяет файлы
						default:
							if($f == 'deleted')
							{
								File::delete_file($k);
							}
							else
							{
								try
								{
									File::save_file($f, $k);
								}
								catch (Exception $e){}
							}
							break;
					}
				}
			}
		}
		DB::query("UPDATE {update_return} SET current='0'");
		DB::query("UPDATE {update_return} SET current='1' WHERE id=%d", $id);
		$this->diafan->_cache->delete("", array());

		if($mod_protected)
		{
			$new_values = array('MOD_PROTECTED' => 'true');
			Custom::inc('includes/config.php');
			Config::save($new_values, $this->diafan->_languages->all);
		}
		$this->diafan->_cache->delete("", array());

		return true;
	}
}
