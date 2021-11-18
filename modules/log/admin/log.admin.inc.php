<?php
/**
 * Подключение модуля к административной части других модулей
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

if ( ! defined('DIAFAN'))
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
 * Log_admin_inc
 */
class Log_admin_inc extends Diafan
{
	/**
	 * Возвращает массив ошибок из лог-файла
	 *
	 * @param boolean $refresh обновление кеша перед чтением
	 * @return array
	 */
	public function errors($refresh = false)
	{
		$refresh = true;
		$cache_meta = array(
			'hash' => File::hash_file(Dev::LOG_ERRORS_PATH),
			'name' => __METHOD__,
		);
		if($refresh || ! ($result = $this->diafan->_cache->get($cache_meta, 'log', CACHE_GLOBAL)))
		{
			$result = array();
			$rows = array();
			if(is_readable(ABSOLUTE_PATH.Dev::LOG_ERRORS_PATH))
			{
				$string = file_get_contents(ABSOLUTE_PATH.Dev::LOG_ERRORS_PATH);
				$string = (string) $string;
				if($string)
				{
					$charset = mb_detect_encoding($string);
					$string = iconv($charset, "UTF-8//TRANSLIT//IGNORE", $string);
					$string = stripslashes($string);
					$string = htmlspecialchars($string);
					$lines = explode(PHP_EOL, $string);
					$row_id = 0; $time = false; $keys = array('uri', 'client', 'host', 'agent', 'referer'); $index = false;
					foreach($lines as $i => $line)
					{
						if(preg_match('/^\[(\d{2})\.(\d{2})\.(\d{4}) (\d{2}):(\d{2}):(\d{2})\]$/msiu', $line, $matches))
						{
							$time = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[1], $matches[3]);
							$row_id++;
						}
						if($row_id === 0 || $time === false) continue;
						if(! isset($rows[$row_id]["id"]))
						{
							$rows[$row_id]["id"] = $row_id;
						}
						if(! isset($rows[$row_id]["datetime"]))
						{
							$rows[$row_id]["datetime"] = $time;
							continue;
						}
						if(empty($line)) continue;
						foreach($keys as $key)
						{
							if(isset($rows[$row_id][$key]) || 0 !== stripos($line, $key.':')) continue;
							$rows[$row_id][$key] = substr($line, strlen($key.':'));
							continue 2;
						}
						if(preg_match('/^Error #(\d+): (.*?)$/iu', $line, $matches))
						{
							$index = (int) $matches[1];
							$rows[$row_id]["errors"][$index] = array(
								"name" => (string) $matches[2],
								"message" => (string) $matches[2],
								"trace" => array(),
							);
							$name = trim(preg_replace('/^(.*?) called at \[(.*?)\](.*?)$/msiu', '${1}', $rows[$row_id]["errors"][$index]["name"]));
							if($name) $rows[$row_id]["errors"][$index]["name"] = $name;
							$rows[$row_id]["errors"][$index]["line_start"] = $i;
							$rows[$row_id]["errors"][$index]["line_end"] = $rows[$row_id]["errors"][$index]["line_start"];
							continue;
						}
						if(preg_match('/^#(\d+) (.*?)$/iu', $line, $matches))
						{
							$rows[$row_id]["errors"][$index]["trace"][(int) $matches[1]] = trim((string) $matches[2]);
							$rows[$row_id]["errors"][$index]["line_end"] = $i;
						}
					}
				}
			}
			$id = 0;
			foreach($rows as $row)
			{
				if(empty($row["errors"])) continue;
				foreach($row["errors"] as $value)
				{
					$id++;
					$result[$id] = array(
						"id" => $id,
						"group_id" => isset($row["id"]) ? trim($row["id"]) : 0,
						"datetime" => isset($row["datetime"]) ? trim($row["datetime"]) : '',
						"uri" => isset($row["uri"]) ? trim($row["uri"]) : '',
						"client" => isset($row["client"]) ? trim($row["client"]) : '',
						"host" => isset($row["host"]) ? trim($row["host"]) : '',
						"agent" => isset($row["agent"]) ? trim($row["agent"]) : '',
						"referer" => isset($row["referer"]) ? trim($row["referer"]) : '',
						"name" => isset($value["name"]) ? trim($value["name"]) : '',
						"message" => isset($value["message"]) ? trim($value["message"]) : '',
						"trace" => isset($value["trace"]) ? $value["trace"] : '',
						"line_start" => isset($value["line_start"]) ? $value["line_start"] : '',
						"line_end" => isset($value["line_end"]) ? $value["line_end"] : '',
					);
				}
			}
			$this->diafan->_cache->save($result, $cache_meta, 'log', CACHE_GLOBAL);
		}
		return $result;
	}

	/**
	 * Удаляет ошибки из лог-файла
	 *
	 * @param array $ids массив порядковых номеров ошибок
	 * @return void
	 */
	public function delete($ids)
	{
		$rows = $this->errors(true);
		if(empty($rows)) return;

		if(! $ids) return;
		$this->diafan->filter($ids, "integer");
		if(! is_array($ids)) $ids = array($ids);
		foreach($ids as $key => $id)
		{
			if(! $id || ! is_int($id) || empty($rows[$id])
			|| empty($rows[$id]["line_start"]) || empty($rows[$id]["line_end"]))
			{
				unset($ids[$key]);
				continue;
			}
			if($rows[$id]["line_end"] < $rows[$id]["line_start"])
			{
				$rows[$id]["line_end"] = $rows[$id]["line_start"];
			}
		}
		if(empty($ids)) return;

		if(! is_writable(ABSOLUTE_PATH.Dev::LOG_ERRORS_PATH)) return;
		$string = file_get_contents(ABSOLUTE_PATH.Dev::LOG_ERRORS_PATH);
		$string = (string) $string;
		if(! $string) return;
		$lines = explode(PHP_EOL, $string);

		$result = array(); // $del_lines = array();
		foreach($lines as $i => $line)
		{
			if(! empty($ids))
			{
				foreach($ids as $id)
				{
					if($i >= $rows[$id]["line_start"] && $i <= $rows[$id]["line_end"])
					{
						if($rows[$id]["line_start"] == $i && isset($result[$i-1]) && empty($result[$i-1]))
						{
							// $del_lines[$i-1] = $result[$i-1];
							unset($result[$i-1]);
						}
						// $del_lines[$i] = $line;
						continue 2;
					}
				}
			}
			$result[$i] = $line;
		}

		$lines = '';
		if(! empty($result))
		{
			$lines = implode(PHP_EOL, $result);
		}
		if($lines) File::save_file($lines, Dev::LOG_ERRORS_PATH, false);
		else File::delete_file(Dev::LOG_ERRORS_PATH);
	}
}

/**
 * Log_admin_inc_exception
 *
 * Исключение для подключений модуля к административной части других модулей
 */
class Log_admin_inc_exception extends Exception{}
