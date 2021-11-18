<?php
/**
 * Обрабатывает полученные данные из формы CRON
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

class Service_cron extends Cron
{
	/**
	 * Подготавливает конфигурацию
	 *
	 * @return void
	 */
	protected function prepare_config()
	{
		$this->methods["delete"] = 'Сбросить кэш';
		$this->methods["backup_database"] = 'Резервное копирование БД';
		$this->methods["backup_files"] = 'Резервное копирование файлов сайта';
	}

	/**
	 * Сбросить кэш
	 *
	 * @return void
	 */
	public function delete()
	{
		// удаляем кэш всех модулей
		$this->diafan->_cache->delete("", array());
	}

	/**
	 * Инициализация создания дампа базы данных
	 *
	 * @return void
	 */
	public function backup_database()
	{
		$this->diafan->_executable->execute(array(
			"module" => "service",
			"method" => "backup_database",
			"params" => array(),
			"text"   => $this->diafan->_('Дамп БД'),
		));
	}

	/**
	 * Инициализация создания дампа базы данных
	 *
	 * @return void
	 */
	public function backup_files()
	{
		$this->diafan->_executable->execute(array(
			"module" => "service",
			"method" => "backup_files",
			"params" => array(),
			"text"   => $this->diafan->_('Дамп файлов'),
		));
	}
}
