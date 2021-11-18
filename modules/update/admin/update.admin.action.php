<?php
/**
 * Точки возврата
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
 * Update_admin_action
 */
class Update_admin_action extends Action_admin
{
	/**
	 * Вызывает обработку Ajax-запросов
	 *
	 * @return void
	 */
	public function init()
	{
		if (! empty($_POST["action"]))
		{
			switch($_POST["action"])
			{
				case 'current':
					$this->current();
					break;

				case 'update':
					$this->update();
					break;

				case 'download':
					$this->download();
					break;

				case 'count':
					$this->count();
					break;
			}
		}
	}

	/**
	 * Применяет точку возврата
	 *
	 * @return void
	 */
	public function current()
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->diafan->redirect(URL);
		}

		if (! empty($_POST["ids"]))
		{
			$_POST["id"] = $_POST["ids"][0];
		}

		$id = intval($_POST["id"]);
		if(! $this->diafan->_update->current($id, true))
		{
			$this->diafan->redirect(URL);
		}

		$this->diafan->set_one_shot('<div class="ok">'.$this->diafan->_('Обновление применено.').'</div>');
		$this->diafan->set_one_shot('<div class="commentary">'.$this->diafan->_('Активирован режим защищенной работы CMS: кастомизация критически важных файлов отключена. Выключить защищенный режим можно в разделе "%sПараметры сайта%s"', '<a href="'.BASE_PATH_HREF.'config/#tabs-2">', '</a>').'</div>');
	}

	/**
	 * Обновление
	 *
	 * @return void
	 */
	private function update()
	{
		if(IS_DEMO)
		{
			throw new Exception('В демонстрационном режиме эта функция не доступна.');
		}
		$rows = $this->get_result();
		if(empty($this->result["redirect"]))
		{
			if($rows && is_array($rows))
			{
				$this->result["data"] = '<div id="update_list" class="noreset"><h3>'.$this->diafan->_('Доступно новое обновление, со следующими изменениями').':</h3>
				<ol>';
				foreach($rows as $row)
				{
					$this->result["data"] .= '<li>'.$row["text"].'</li>';
				}
				$this->result["data"] .= '</ol><span class="btn btn_small btn_dwnl">
					<span class="fa fa-cloud-download"></span>'.$this->diafan->_('Скачать').'</span>
					<img class="spinner hide" src="'.BASE_PATH.'adm/img/loading.gif">
				<div>
				<div class="progress-bar" id="update_download">';
				foreach($rows as $row)
				{
					$this->result["data"] .= '<div class="progress-item empty"></div>';
				}
				$this->result["data"] .= '</div>
				<div class="progress-procent">0%</div>';
				$this->result["rows"] = $rows;
			}
			else
			{
				$this->result["messages"] = '<div class="commentary">'.$this->diafan->_('Обновлений нет.').'</div>';
			}
		}
		$this->result["result"] = "success";
	}

	/**
	 * Обновление
	 *
	 * @return void
	 */
	private function download()
	{
		if(IS_DEMO)
		{
			throw new Exception('В демонстрационном режиме эта функция не доступна.');
		}

		$id = $this->diafan->filter($_POST, "int", "id");
		$hash = $this->diafan->filter($_POST, "string", "hash");
		$text = $this->diafan->filter($_POST, "string", "text");
		$version = $this->diafan->filter($_POST, "string", "version");
		$preview = $this->diafan->filter($_POST, "string", "preview");
		if($id)
		{
			$commentary = '';
			if(file_exists(ABSOLUTE_PATH.'return/'.$id.'.zip'))
			{
				File::delete_file('return/'.$id.'.zip');
				$commentary .= 'Точка № '.$id.' обновлена';
			}
			if($hash)
			{
				File::copy_file('http'.(IS_HTTPS ? "s" : '').'://user.diafan.ru/file/update/'.$id.'/'.$hash, 'return/'.$id.'.zip');
				// else File::copy_file('http'.(IS_HTTPS ? "s" : '').'://user.diafan.ru/api/file/get/update/'.$id.'.zip'.'?'.$this->diafan->uid(), 'return/'.$id.'.zip');
			}
			else
			{
				File::save_file(file_get_contents('http'.(IS_HTTPS ? "s" : '').'://user.diafan.ru/api/file/get/update/'.$id.'.zip'.'?'.$this->diafan->uid()), 'return/'.$id.'.zip');
			}

			$name = $hash ? 'Обновление' : 'Тестовое обновление';
			if($row = DB::query_fetch_array("SELECT id, current, `hash` FROM {update_return} WHERE id=%d LIMIT 1", $id))
			{
				$downgrade = $before_id = false;
				if($row["current"] && ! $row["hash"]
				&& ($before_id = DB::query_result("SELECT id FROM {update_return} WHERE id<%d ORDER BY id DESC LIMIT 1", $row["id"])))
				{
					$downgrade = $this->diafan->_update->current($before_id, false);
				}
				DB::query(
					"UPDATE {update_return} SET id=%d, name='%h', created=%d, `text`='%h', `hash`='%h', version='%h' WHERE id=%d LIMIT 1",
					$id, $name, time(), $text, $hash, $version, $id
				);
				if($downgrade)
				{
					if($this->diafan->_update->current($id, false))
					{ $commentary .= ' и вновь применена'; }
					elseif($before_id)
					{ $commentary .= ' и применена предыдущая точка № '.$before_id; }
				}
			}
			else
			{
				DB::query(
					"INSERT INTO {update_return} (id, name, created, `text`, `hash`, version, current) VALUES (%d, '%h', %d, '%h', '%h', '%h', '0')",
					$id, $name, time(), $text, $hash, $version
				);
			}
			if($commentary) $this->diafan->set_one_shot('<div class="commentary">'.$commentary.'.</div>');

			if($hash) $this->diafan->configmodules("hash", "update", 0, false, $hash);
			if($preview) $this->diafan->configmodules("preview", "update", 0, false, $preview);
		}
		unset($_SESSION["update_count"]);
		$this->result["result"] = "success";
		$this->result["redirect_url"] = URL.'success1/?'.$this->diafan->uid();
	}

	/**
	 * Количество доступных обновлений
	 *
	 * @return void
	 */
	private function count()
	{
		if(IS_DEMO)
		{
			throw new Exception('В демонстрационном режиме эта функция не доступна.');
		}
		$rows = $this->get_result();
		$this->result["redirect"] = '';
		if(empty($this->result["redirect"]))
		{
			$time = mktime(23, 59, 0, date("m"), date("d"), date("Y"));
			if($rows && is_array($rows))
			{
				$count = count($rows);
				$this->result["data"] = $count > 0 ? ' <span class="nav__info">'.count($rows).'</span>' : '';
				$this->result["rows"] = $rows;
				$this->result["messages"] = '<div class="commentary">'.$this->diafan->_('Доступно обновление: для установки необходимо перейти в %sраздел "Обновление CMS"%s.', '<a href="'.BASE_PATH_HREF.'update/">', '</a>').'</div>';
				$_SESSION["update_count"] = array(
					"time" => $time,
					"value" => $count,
					"messages" => $this->result["messages"]
				);
			}
			else
			{
				$this->result["data"] = '';
				$this->result["rows"] = array();
				$this->result["messages"] = '';
				$_SESSION["update_count"] = array(
					"time" => $time,
					"value" => 0,
					"messages" => ''
				);
			}
		}
		$this->result["result"] = "success";
	}

	/**
	 * Формирует данные
	 *
	 * @return void
	 */
	private function get_result()
	{
		if(! $result)
		{
			global $result;
		}
		if($result === 71)
		{
			$this->result["redirect"] = base64_decode('aHR0cDovL3d3dy5kaWFmYW4ucnUvbm9hdXRoLw==');
		}
		if(is_array($result) && $this->diafan->_account->is_auth())
		{
			$hash = $this->diafan->configmodules("hash","update");
			$id = DB::query_result("SELECT id FROM {update_return} WHERE `hash`='%h' ORDER BY id DESC LIMIT 1", $hash);
			$id = $id ?: 1; foreach($result as $value) { if(! empty($value["id"]) && $id < $value["id"]) { $id = $value["id"]; $hash = $value["hash"]; } }
			$url = $this->diafan->_account->uri('update', 'upgrade');
			if($upgrade = $this->diafan->_client->request($url, $this->diafan->_account->token, array("hash" => $this->diafan->configmodules("hash","update"))))
			{
				if(! empty($upgrade) && is_array($upgrade) && ! empty($upgrade["hash"]))
				{
					$id = DB::query_result("SELECT id FROM {update_return} WHERE id>%d AND `hash`='' ORDER BY id DESC LIMIT 1", $id);
					if(! $id || ! ($hash = $this->diafan->configmodules("preview","update")) || $id && $hash && $hash != $upgrade["hash"])
					{ $upgrade["preview"] = $upgrade["hash"]; $upgrade["hash"] = ''; $upgrade = array($upgrade); }
					else $upgrade = array();
				}
				else $upgrade = array();
				$result = ! empty($upgrade) && is_array($upgrade) ? array_merge($result, $upgrade) : $result;
			}
		}
		return $result;
	}
}
