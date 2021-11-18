<?php
/**
 * Обрабатывает полученные данные из формы
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

class Service_exec extends Exec
{
	/**
   * @var string директория для резервного копирования
   */
	const BACKUP_BASE_PATHE = "tmp/backup";

	/**
	 * Инициализация API модуля
	 *
	 * @return void
	 */
	public function init()
	{
		switch($this->method)
		{
			case 'backup_database':
				$this->backup_database();
				break;

			case 'backup_files':
				$this->backup_files();
				break;

			default:
				$this->set_error();
				break;
		}
	}

	/**
	 * Определение свойств класса
	 *
	 * @return void
	 */
	public function variables()
	{
		switch($this->method)
		{
			// case 'backup_database':
			// case 'backup_files':
			// 	$this->verify = false;
			// 	break;

			default:
				break;
		}
	}

	/**
	 * Резервное копирование базы данных
	 *
	 * @return void
	 */
	private function backup_database()
	{
		$limit = 1024; // TO_DO: лимит обработки элементов за одну итерацию
		$max = 838860;
		if($row = DB::query_fetch_array("SHOW VARIABLES LIKE 'max_allowed_packet'"))
		{
			if(! empty($row["Value"])) $max = $max > $row["Value"] ? $row["Value"] : $max;
		}

		$url = parse_url(DB_URL);
		$dbname = substr($url['path'], 1);

		$db = $db_tables = array_fill_keys(array_keys(DB::fields()), 0);
		if(empty($db_tables))
		{
			throw new Exec_exception('Таблицы БД отсутствуют.');
		}
		foreach($db_tables as $table => $dummy)
		{
			$db_tables[$table] = (int) DB::query_result("SELECT COUNT(*) FROM {%h} WHERE 1=1", $table);
		}

		$tables = $this->diafan->filter($_POST, "integer", "tables");
		if(empty($tables) || ! is_array($tables)) $tables = array();

		$text = '';
		$files = $this->diafan->filter($_POST, "string", "files");
		if(empty($files) || ! is_array($files)) $files = array();
		if($files) $files = array_unique($files);
		$name = $this->diafan->filter($_POST, "string", "name");

		$zip_class = class_exists('ZipArchive');
		$zip = $this->diafan->filter($_POST, "boolean", "zip");
		$zip = ! isset($_POST["zip"]) ? 0 : $zip;

		if($zip === 0)
		{
			if(! $tables)
			{
				$db_url = DB_URL;
				$url = parse_url($db_url);
				$text .= '-- DIAFAN.CMS
-- version '.Custom::version_core().'
-- http://www.diafan.ru/
--
-- Хост: '.$_SERVER['HTTP_HOST'].':'.$_SERVER['REMOTE_PORT'].'
-- Время создания: '.date("l j M Y, H:i:s", $_SERVER['REQUEST_TIME']).'
-- Версия сервера'.(! empty($url["scheme"]) ? ' '.$url["scheme"] : '').': '.DB::query_result("SELECT version() as `version`").'
-- Версия PHP: '.$this->diafan->version_php(2).'

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: '.(! empty($url['host']) ? '`'.$url['host'].'`' : '').'
--'.PHP_EOL;
			}
			$write = $this->write_dump($text, $name, $files, $zip_class);

			foreach($db_tables as $table => $count)
			{
				if(isset($tables[$table]) && $tables[$table] >= $count)
				{
					unset($db_tables[$table]);
					continue;
				}
				if(! isset($tables[$table])) $tables[$table] = 0;
				elseif($tables[$table] < 0) $tables[$table] = 0;

				if($tables[$table] == 0)
				{
					// структура
					$row_s = DB::query_fetch_array("SHOW CREATE TABLE `".DB_PREFIX.$table."`");
					$text .=
					PHP_EOL.'-- --------------------------------------------------------'.PHP_EOL.PHP_EOL
					.'--'.PHP_EOL
					.'-- Структура таблицы `'.DB_PREFIX.$table.'`'.PHP_EOL
					.'--'.PHP_EOL.PHP_EOL;
					$text .= "DROP TABLE IF EXISTS `".DB_PREFIX.$table."`;".PHP_EOL.$row_s["Create Table"].";".PHP_EOL;
					$write = $this->write_dump($text, $name, $files, $zip_class);
				}

				if(! in_array(
					$table, array(
						'sessions', 'sessions_hash', 'search_index', 'search_keywords', 'search_results', 'log', 'log_note',
						'visitors_session', 'visitors_url', 'visitors_stat_traffic', 'visitors_stat_traffic_source', 'visitors_stat_traffic_pages', 'visitors_stat_traffic_names_search_bot',
						'executable'
					)
				))
				{
					// данные
					while($tables[$table] < $count)
					{
						if($tables[$table] == 0)
						{
							$text .= PHP_EOL.'--'.PHP_EOL.'-- Дамп данных таблицы `'.DB_PREFIX.$table.'`'.PHP_EOL.'--'.PHP_EOL.PHP_EOL;
						}
						$rows = DB::query_range_fetch_all("SELECT * FROM {%h} WHERE 1=1", $table, $tables[$table], $limit);
						$tables[$table] += count($rows);
						$sql = '';
						foreach($rows as $row)
						{
							$values = '';
							foreach($row as $val)
							{
								$values .= $values ? ',' : '';
								if(is_null($val)) $values .= "NULL";
								else $values .= "'".DB::escape_string($val)."'";
							}
							$sql .= ($sql ? ',' : '')."(".$values.")";
							if(strlen($sql) > $max)
							{
								$text .= "INSERT INTO `".DB_PREFIX.$table."` VALUES ".$sql.";".PHP_EOL;
								$sql = '';
								$write = $this->write_dump($text, $name, $files, $zip_class);
							}
						}
						if($sql)
						{
							$text .= "INSERT INTO `".DB_PREFIX.$table."` VALUES ".$sql.";".PHP_EOL;
							$write = $this->write_dump($text, $name, $files, $zip_class);
						}

						// индексы
						if($tables[$table] > 0 && $tables[$table] == $count)
						{
							if($rows_i = DB::query_fetch_all("SHOW INDEXES FROM {%h}", $table))
							{
								$text .= PHP_EOL.'--'.PHP_EOL.'-- Индексы таблицы `'.DB_PREFIX.$table.'`'.PHP_EOL.'--'.PHP_EOL.PHP_EOL;
								$primary_keys = array(); $keys = array();
								foreach($rows_i as $row_i)
								{
									if($row_i["Key_name"] == 'PRIMARY') $primary_keys[] = $row_i["Column_name"];
									else $keys[] = array("Column_name" => $row_i["Column_name"], "Sub_part" => $row_i["Sub_part"]);
								}
								$sql = array();
								if($primary_keys)
								{
									foreach ($primary_keys as $key => $val) $primary_keys[$key] = '`'.$val.'`';
									$sql[] = '  ADD PRIMARY KEY ('.implode(',', $primary_keys).')';
								}
								if($keys)
								{
									foreach($keys as $key) $sql[] = '  ADD KEY `'.$key["Column_name"].'` (`'.$key["Column_name"].'`'.($key["Sub_part"] ? '('.$key["Sub_part"].')' : '').')';
								}
								$text .= 'ALTER TABLE `'.DB_PREFIX.$table.'`'.PHP_EOL.implode(','.PHP_EOL, $sql).';'.PHP_EOL;
							}
						}

						// auto_increment
						if($tables[$table] > 0 && $tables[$table] == $count)
						{
							if($row_ai = DB::query_fetch_array("SHOW COLUMNS FROM {".$table."} FROM `".$dbname."` WHERE Extra='%s'", 'auto_increment'))
							{
								$columns = DB::columns($table);
								$increment = ((int) DB::query_result("SELECT MAX(%h) FROM {%h} WHERE 1=1", $row_ai["Field"], $table)) + 1;
								$text .= PHP_EOL.'--'.PHP_EOL.'-- AUTO_INCREMENT для таблицы `'.DB_PREFIX.$table.'`'.PHP_EOL.'--'.PHP_EOL.PHP_EOL;
								$text .= 'ALTER TABLE `'.DB_PREFIX.$table.'`'.PHP_EOL
									.'  MODIFY `'.$row_ai["Field"].'`'
									.' '.$row_ai["Type"]
									.($row_ai["Null"] == 'NO' ? ' NOT NULL' : '')
									.(! is_null($row_ai["Default"]) ? " DEFAULT '".$row_ai["Default"]."'" : "")
									.' AUTO_INCREMENT'
									.(! empty($columns[$row_ai["Field"]]["COLUMN_COMMENT"]) ? " COMMENT '".$columns[$row_ai["Field"]]["COLUMN_COMMENT"]."'" : "")
									.', AUTO_INCREMENT='.$increment.';'.PHP_EOL;
							}
						}

						if($write)
						{
							break 2;
						}
					}
				}
				unset($db_tables[$table]);
			}
			if(! empty($db_tables))
			{
				$this->post = array(
					"tables" => $tables,
					"files"  => $files,
					"name"   => $name,
				);
				$this->repeat = true;
			}
			else
			{
				$text .= PHP_EOL."-- DIAFAN.CMS dump end".PHP_EOL;
			}
			$write = $this->write_dump($text, $name, $files, $zip_class, true);
		}


		if(! $this->repeat && $zip === 0 && $zip_class && ! empty($files) && ! empty($name))
		{
			$this->post = array(
				"tables" => $tables,
				"files"  => $files,
				"name"   => $name,
				"zip"    => true,
			);
			$this->repeat = true;
		}
		elseif(! $this->repeat && $zip && $zip_class && ! empty($files) && ! empty($name))
		{
			$is_zip = false;
			$dir = self::BACKUP_BASE_PATHE.'/'.$name;
			if(is_writable(ABSOLUTE_PATH.self::BACKUP_BASE_PATHE) && is_writable(ABSOLUTE_PATH.$dir))
			{
				if($file = File::tempnam($name.'.zip', self::BACKUP_BASE_PATHE))
				{
					$file = ABSOLUTE_PATH.self::BACKUP_BASE_PATHE.'/'.$file;
					$zip_object = new ZipArchive;
					if($zip_object->open($file, ZipArchive::CREATE) === true)
					{
						foreach($files as $i => $filename)
						{
							if(! is_writable(ABSOLUTE_PATH.$dir.'/'.$filename))
							{
								continue;
							}
							// $text = file_get_contents(ABSOLUTE_PATH.$dir.'/'.$filename);
							// $zip_object->addFromString(basename(ABSOLUTE_PATH.$dir.'/'.$filename), $text);
							// File::rm($dir.'/'.$filename);
							$zip_object->addFile(ABSOLUTE_PATH.$dir.'/'.$filename, DB_PREFIX."db.sql");
						}
						$zip_object->close();
						File::rm($dir);
						$is_zip = true;
					}
				}
			}
			if(! $is_zip)
			{
				throw new Exec_exception('Ошибка при создании архивного файла дампа БД.');
			}
		}

		$this->iteration = 0;
		$this->max_iteration = 0;
		foreach($db as $table => $count)
		{
			$this->max_iteration += $count;
			if(isset($tables[$table]) && ($tables[$table] > 0)) $this->iteration += $tables[$table];
		}
		if($this->max_iteration && $zip_class)
		{
			$this->max_iteration++;
			if($zip) $this->iteration++;
		}
	}

	/**
	 * Записывает дамп в файл при достяжении определённого размера (1Mb)
	 * и возвращает количество всех аналогичных записей в файл за текущую сессию
	 *
	 * @param string $text текущее содержание дампа
	 * @param string $name имя директории/файла дампа
	 * @param array $files массив имен файлов, содержащих части дампа
	 * @param boolean $divide делить общее содержание на части
	 * @param boolean $write принудительная запись в файл
	 * @return integer
	 */
	private function write_dump(&$text, &$name, &$files, $divide = false, $write = false)
	{
		static $count = 0;

		if(! $text)
		{
			return $count;
		}

		$base_name = DB_PREFIX.'db.sql';
		$max_length = 1024 * 1024;
		$length = mb_strlen($text, '8bit');

		if($length < $max_length && ! $write)
		{
			return $count;
		}

		File::create_dir(self::BACKUP_BASE_PATHE, true);
		if(! $name)
		{
			$name = DB_PREFIX.'db_sql_'.date("d_m_Y_H_i");
			$name = File::tempnam($name, self::BACKUP_BASE_PATHE, true);
		}
		$dir = self::BACKUP_BASE_PATHE.'/'.$name;
		File::create_dir($dir);

		$file = File::tempnam($base_name, $dir);
		if($divide && ! empty($files))
		{
			$file = array_pop($files);
		}
		$count = count($files);
		if($count > 0)
		{
			$text = "-- DIAFAN.CMS part ".($count + 1).PHP_EOL
				."-- Datetime: ".date('Y-m-d H:i:s').PHP_EOL
				."-- Site: ".BASE_PATH.PHP_EOL.PHP_EOL
				.$text;
		}
		File::save_file($text, $dir.'/'.$file, 1);
		$files[] = $file;
		$text = '';

		return ++$count;
	}

	/**
	 * Резервное файлов сайта
	 *
	 * @return integer
	 */
	private function backup_files()
	{
		if(! $this->max_execution_time || ! $this->timestart)
		{
			return;
		}
		$max_execution_time = $this->max_execution_time - self::TICK_DELAY;
		if($max_execution_time < 1)
		{
			throw new Exec_exception('Недостаточно выделено времени для процесса.');
		}
		$datestamp = time();
		$datestamp_end = $datestamp + $max_execution_time;

		$limit = 1024; // TO_DO: лимит обработки элементов за одну итерацию

		if(! class_exists('ZipArchive'))
		{
			throw new Exec_exception('Не доступно PHP-расширение ZipArchive. Обратитесь в техническую поддержку хостинга.');
		}

		$name = $this->diafan->filter($_POST, "string", "name");
		if($name && ! is_writable(ABSOLUTE_PATH.self::BACKUP_BASE_PATHE.'/'.$name))
		{
			throw new Exec_exception('Ошибка при работе с архивным файлом.');
		}
		if(! $name)
		{
			$name = DB_PREFIX.'files_'.date("d_m_Y_H_i");
			$name = File::tempnam($name.'.zip', self::BACKUP_BASE_PATHE);
		}

		$files = array();
		$prepare = function (&$list) use (&$files, &$prepare)
		{
			if(empty($list)) return;
			foreach($list as $path => $name)
			{
				if(! is_readable(ABSOLUTE_PATH.$path)) continue;
				if(is_array($name) && in_array($path, array('cache', 'tmp')))
				{
					$name = array();
					$list[$path] = $name;
				}
				if(is_array($name))
				{
					$files[$path] = true;
					$prepare($name);
				}
				else $files[$path] = false;
			}
		};
		$list = File::scandir('', -1, true);
		$prepare($list);
		$this->max_iteration = count($files);
		$this->max_iteration = $this->max_iteration > 0 ? $this->max_iteration : 0;

		$dir_separator = '/';
		if(defined('DIRECTORY_SEPARATOR') && DIRECTORY_SEPARATOR)
		{
			$dir_separator = DIRECTORY_SEPARATOR;
		}

		if(! empty($files))
		{
			$zip = new ZipArchive;
			if($zip->open(ABSOLUTE_PATH.self::BACKUP_BASE_PATHE.'/'.$name, ZipArchive::CREATE) === true)
			{
				for($i = 0; $i < $zip->numFiles; $i++)
				{
					if(empty($files)) break;
					$file = $zip->getNameIndex($i);
					if(substr($file, -1) !== '/') $dir = false;
					else $dir = true;
					foreach($files as $path => $is_dir)
					{
						if($dir != $is_dir || $is_dir && $path.'/' != $file || ! $is_dir && $path != $file) continue;
						unset($files[$path]);
						break;
					}
				}
				foreach($files as $path => $is_dir)
				{
					if($is_dir)
					{
						$zip->addEmptyDir($path);
						$limit--;
					}
					else
					{
						$zip->addFile(ABSOLUTE_PATH.$path, $path);
						$limit--;
					}
					unset($files[$path]);

					$datestamp = time();
					if($limit <= 0 || $datestamp > $datestamp_end)
					{
						break;
					}
				}
				$zip->close();
			}
			else throw new Exec_exception('Ошибка при создании архивного файла.');
		}
		else throw new Exec_exception('Список файлов для резервирования пуст.');

		if(! empty($files))
		{
			$this->post = array(
				"name"  => $name,
			);
			$this->repeat = true;
		}

		$this->iteration = $this->max_iteration - count($files);
		$this->iteration = $this->iteration > 0 && $this->iteration <= $this->max_iteration ? $this->iteration : 0;
	}
}
