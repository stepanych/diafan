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

class Custom_exec extends Exec
{
	/**
	 * @var array массив объектов - установка модулей
	 */
	private $install = array();

	/**
	 * Инициализация API модуля
	 *
	 * @return void
	 */
	public function init()
	{
		switch($this->method)
		{
			case 'get_modules':
				$this->get_modules();
				break;

			default:
				$this->set_error();
				break;
		}
	}

	/**
	 * Получает список всех модулей, которые можно установить
	 *
	 * @return void
	 */
	private function get_modules()
	{
		$this->result["vars"] = array();
		$names = false;
		if(! empty($_POST["names"]) && (is_string($_POST["names"]) || is_array($_POST["names"])))
		{
			$names = $this->diafan->filter($_POST, 'string', 'names');
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
		$this->result["vars"]["names"] = $names;

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

		$modules = array();
		foreach ($this->diafan->all_modules as $r)
		{
			if($r["module_name"] == $r["name"])
			{
				$modules[$r["name"]] = $r["title"];
			}
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
		$rows = array();
		$rs = $this->diafan->_custom->get_dir("modules");
		$this->result["vars"]["rs"] = $rs;
		if(! empty($names))
		{
			$rs = array_diff($rs, $this->diafan->_custom->get_dir("modules", $names));
		}
		$this->result["vars"]["rs2"] = $rs;
		foreach($rs as $module)
		{
			if (Custom::exists('modules/'.$module.'/'.$module.'.install.php'))
			{
				Custom::inc('modules/'.$module.'/'.$module.'.install.php');
				$name = Ucfirst($module).'_install';
				$this->install[$module] = new $name($this->diafan);

				if($this->install[$module]->is_core)
					continue;

				$this->install[$module]->langs = $langs;
				$this->install[$module]->module = $module;

				$row["installed"] = in_array($module, array_keys($modules));

				if($row["installed"])
				{
					$row["name"] = $modules[$module];
				}
				else
				{
					$row["name"] = $this->install[$module]->title;
				}
				if(! $row["name"])
				{
					$row["name"] = $module;
				}
				$row["module_name"] = $module;
				$rows[$module] = $row;
			}
		}

		$GLOBALS["custom"] = $globals_custom;
		$this->result["rows"] = $rows;
	}
}
