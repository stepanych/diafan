<?php
/**
 * Котроллер модуля «Пользователи на сайте»
 *
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
 * Users
 */
class Users extends Controller
{
	/**
	 * Шаблонная функция: выводит статистику пользователей на сайте
	 *
	 * @param array $attributes атрибуты шаблонного тега
	 * defer - маркер отложенной загрузки шаблонного тега: **event** – загрузка контента только по желанию пользователя при нажатии кнопки "Загрузить", **emergence** – загрузка контента только при появлении в окне браузера клиента, **async** – асинхронная (одновременная) загрузка контента совместно с контентом шаблонных тегов с тем же маркером, **sync** – синхронная (последовательная) загрузка контента совместно с контентом шаблонных тегов с тем же маркером, по умолчанию отложенная загрузка не используется, обычный вывод шаблонных тегов в коде страницы
	 * defer_title - текстовая строка, выводимая на месте появления загружаемого контента с помощью отложенной загрузки шаблонного тега
	 * template - шаблон тега (файл modules/users/views/users.view.show_block_**template**.php; по умолчанию шаблон modules/users/views/users.view.show_block.php)
	 *
	 * @return void
	 */
	public function show_block($attributes)
	{
		$this->diafan->attributes($attributes, 'template');

		$result = $this->model->show_block();
		$result["attributes"] = $attributes;

		echo $this->diafan->_tpl->get('show_block', 'users', $result, $attributes["template"]);
	}
}
