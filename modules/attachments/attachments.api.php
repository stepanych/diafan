<?php
/**
 * Обрабатывает полученные данные из формы: Вывод прикрепленных файлов
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

class Attachments_api extends Api
{
	/**
	 * @var string имя отдаваемого файла
	 */
	private $filename;

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		$this->filename = '';
	}

	/**
	 * Определение свойств класса
	 *
	 * @return void
	 */
	public function variables()
	{
		$this->verify = false;

		$this->errors["file_not_found"] = "File not found.";
	}

	/**
	 * Инициализация API модуля
	 *
	 * @return void
	 */
	public function init()
	{
		switch($this->method)
		{
			case 'check':
			case 'get':
				$this->get();
				break;

			default:
				$this->set_error("method_unknown");
				break;
		}
	}

	/**
	 * Возвращает список дополнений
	 *
	 * @return void
	 */
	private function get()
	{
		$this->filename = '';

		$filename = explode("/", $_GET["rewrite"]);
		if(count($filename) != 2)
		{
			$this->set_error("file_not_found", "Файл не найден.");
		}
		if($this->result())
		{
			if($this->method == 'check')
			{
				return;
			}
			Custom::inc('includes/404.php');
		}

		$id = array_shift($filename);
		$this->filename = array_shift($filename);
		if(! $row = DB::query_fetch_array("SELECT * FROM {attachments} WHERE id=%d AND name='%h' AND is_image<>'1' LIMIT 1", $id, $this->filename))
		{
			$this->set_error("file_not_found", "Файл не найден.");
		}
		if($row["access_admin"] && ! $this->diafan->_users->roles("init", $row["module_name"]))
		{
			$this->set_error("file_not_found", "Файл не найден.");
		}
		if($this->result())
		{
			if($this->method == 'check')
			{
				return;
			}
			Custom::inc('includes/404.php');
		}

		$this->is_access();
		if($this->result())
		{
			if($this->method == 'check')
			{
				return;
			}
			Custom::inc('includes/404.php');
		}

		$file_path = USERFILES."/".$row["module_name"]."/files/".$row["id"];
		if(! file_exists(ABSOLUTE_PATH.$file_path))
		{
			$this->set_error("file_not_found", "Файл не найден.");
		}
		if($this->result())
		{
			if($this->method == 'check')
			{
				return;
			}
			Custom::inc('includes/404.php');
		}


		if($this->method == 'check')
		{
			$this->result["result"] = array(
				"result" => "File is found"
			);
		}
		else
		{
			$extension = 'application/octet-stream'; // 'Content-type: text/plain'
			if(preg_match('/\.zip$/i', $row["name"], $matches)) $extension = 'application/zip';
			$this->file_flush($file_path, $row["name"], $extension);
		}
	}

	/**
	 * Проверяет право клиента
	 *
	 * @return void
	 */
	private function is_access()
	{
		if(! $this->is_auth() || ! $this->user->id || ! $this->is_verify())
		{
			$this->set_error("access_denied", 'Файл %s доступн только для авторизованных пользователей.', '<b>'.$this->filename.'</b>');
		}
		if($this->result())
		{
			return;
		}
	}

	/**
	 * Отдает контент файла
	 *
	 * @param string $file_path путь к файлу относительно корня сайта
	 * @param string $name имя файла
	 * @param string $extension тип контента
	 * @return void
	 */
	private function file_flush($file_path, $name, $extension = 'application/octet-stream')
	{
		if(! file_exists(ABSOLUTE_PATH.$file_path))
		{
			$this->set_error("file_not_found", "Файл не найден.");
		}
		if($this->result())
		{
			Custom::inc('includes/404.php');
			return;
		}


		/*
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: max-age=86400');
		if ($extension)
		{
			header("Content-type: ".$extension);
		}
		$file_size = filesize(ABSOLUTE_PATH.$file_path);

		if ($file_size > 0)
		{
			header("Content-length: ".$file_size);
		}

		header("Content-Disposition: attachment; filename=".$name);
		header('Content-transfer-encoding: binary');
		header("Connection: close");

		$handle = fopen(ABSOLUTE_PATH.$file_path, "rb");

		echo fread($handle, $file_size);
		flush();
		exit;
		*/


		header("Cache-Control: public, must-revalidate");
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-Description: File Transfer");
		header("Expires: Sat, 30 Dec 1990 07:07:07 GMT");
		header("Accept-Ranges: bytes");

		// HTTP Range - see RFC2616 for more informations (http://www.ietf.org/rfc/rfc2616.txt)
		$http_range = 0;
		$file_size = filesize(ABSOLUTE_PATH.$file_path);
		$file_size = ($file_size > 0 ? $file_size : 1);
		$new_file_size = $file_size - 1;
		// значение по умолчанию. Ниже может быть переопределено
		$result_lenght = (string) $file_size;
		$result_range = "0-".$new_file_size;
		// Если есть заголовок HTTP_RANGE, то обрабатывает его и отправляем часть файла, иначе отправляем весь файл
		if(getenv('HTTP_RANGE') && preg_match('%^bytes=\d*\-\d*$%', getenv('HTTP_RANGE')))
		{
			list($a, $http_range) = explode('=', getenv('HTTP_RANGE'));
			$http_range = explode('-', $http_range);
			if(! empty($http_range[0]) || ! empty($http_range[1]))
			{
				// переопределяет размер файла...
				$result_lenght = $file_size - $http_range[0] - $http_range[1];
				// и отдает 206 статус
				header("HTTP/1.1 206 Partial Content");
				// переопределяет диапазон
				if(empty($http_range[0]))
				{
					$result_range = $result_lenght.'-'.$new_file_size;
				}
				elseif(empty($http_range[1]))
				{
					$result_range = $http_range[0].'-'.$new_file_size;
				}
				else
				{
					$result_range = $http_range[0].'-'.$http_range[1];
				}
				//header("Content-Range: bytes ".$http_range.$new_file_size .'/'. $file_size);
			}
		}
		header("Content-Length: ".$result_lenght);
		header("Content-Range: bytes ".$result_range.'/'.$file_size);

		if ($extension)
		{
			header("Content-Type: ".$extension);
		}
		header('Content-Disposition: attachment; filename="'.$name.'"');
		header("Content-Transfer-Encoding: binary\n");

		if(function_exists('set_time_limit'))
		{
			if(! $this->diafan->is_disable_function('set_time_limit'))
			{
				set_time_limit(0);
			}
		}

		$fp = @fopen(ABSOLUTE_PATH.$file_path, 'rb');
		if ($fp !== false)
		{
			while (!feof($fp))
			{
				echo fread($fp, 8192);
			}
			fclose($fp);
		}
		else
		{
			@readfile(ABSOLUTE_PATH.$file_path);
		}
		flush();
		exit;
	}
}
