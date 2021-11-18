<?php
/**
 * Обработка POST-запросов в административной части модуля
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
 * Executable_admin_action
 */
class Executable_admin_action extends Action_admin
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
				case 'group_break':
				case 'group_reexecute':
					$this->group_option();
					break;

				case 'clear':
					$this->clear();
					break;
			}
		}
	}

	/**
	 * Групповая операция "Прервать", "Возобновить" и др.
	 *
	 * @return void
	 */
	private function group_option()
	{
		if(! empty($_POST["ids"]))
		{
			$ids = array();
			foreach ($_POST["ids"] as $id)
			{
				$id = $this->diafan->filter($id, "string");
				if($id)
				{
					$ids[] = $id;
				}
			}
		}
		elseif(! empty($_POST["id"]))
		{
			$ids = array($this->diafan->filter($_POST, "string", "id"));
		}
		if(! empty($ids))
		{
			switch ($_POST["action"])
			{
				case 'group_break':
					$this->group_break($ids);
					break;

				case 'group_reexecute':
					$this->group_reexecute($ids);
					break;
			}
		}
	}

	/**
	 * Прервать фоновые процессы
	 *
	 * @param array $ids идентификаторы процессов
	 * @return void
	 */
	public function group_break($ids)
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->result["redirect"] = URL;
			return;
		}

		//проверка прав пользователя на активацию/блокирование
		if (! $this->diafan->_users->roles('edit', $this->diafan->_admin->rewrite))
		{
			$this->result["redirect"] = URL;
			return;
		}

		if(! empty($ids))
		{
			foreach($ids as $id)
			{
				if(empty($id)) continue;
				$this->diafan->_executable->break_down($id);
			}
		}
		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}

	/**
	 * Возобновить фоновые процессы
	 *
	 * @param array $ids идентификаторы процессов
	 * @return void
	 */
	public function group_reexecute($ids)
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->result["redirect"] = URL;
			return;
		}

		//проверка прав пользователя на активацию/блокирование
		if (! $this->diafan->_users->roles('edit', $this->diafan->_admin->rewrite))
		{
			$this->result["redirect"] = URL;
			return;
		}

		if(! empty($ids))
		{
			foreach($ids as $id)
			{
				if(empty($id)) continue;
				DB::query("UPDATE {executable} SET break='%d', timeedit=%d WHERE id='%h' AND break='%d'", 0, time(), $id, 1);
				$this->diafan->_executable->execute(array("id" => $id));
			}
		}
		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}

	/**
	 * Удаление завершённых фоновых процессов
	 *
	 * @return void
	 */
	public function clear()
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->result["redirect"] = URL;
			return;
		}

		//проверка прав пользователя на активацию/блокирование
		if (! $this->diafan->_users->roles('edit', $this->diafan->_admin->rewrite))
		{
			$this->result["redirect"] = URL;
			return;
		}

		if(! empty($_POST["all"]))
		{
			if($ids = DB::query_fetch_value("SELECT id FROM {executable} WHERE 1=1", "id"))
			{
				foreach($ids as $id)
				{
					if(empty($id)) continue;
					if($this->diafan->_executable->is_execute($id)) continue;
					$this->diafan->_db_ex->delete('{executable}', $id);
				}
			}
		}
		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}
}
