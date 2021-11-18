<?php
/**
 * HTTP код 200 (OK) для отправки уведомлений
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
 * Postman_send
 */
class Postman_send extends Diafan
{
	/**
	 * Формируем страницу 404 c HTTP код 200 (OK)
	 *
	 * @return void
	 */
	public function init()
	{
		// Custom::inc('includes/404.php');
		// TO_DO: в ответ на передачу статуса сообщений клиент должен вернуть bytehand.com HTTP код 200 (OK).
		$this->diafan->_site->theme = '404.php';
		header('HTTP/1.0 200 OK');
		header('Content-Type: text/html; charset=utf-8');
		$this->diafan->_parser_theme->show_theme();
	}
}

$class = new Postman_send($this->diafan);
$class->init();
exit;
