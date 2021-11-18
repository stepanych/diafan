<?php
/**
 * Подключение модуля «Уведомления» для работы с сообщениями
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
 * Postman_inc_message
 */
class Postman_inc_message extends Diafan
{
	/**
	 * Добавляет письмо в список почтовых отправлений
	 *
	 * @param string|array $recipient получатель/получатели
	 * @param string $subject тема письма
	 * @param string $body содержание письма
	 * @param string $from адрес отправителя
	 * @param boolean $prior приоритет при отправлении сообщения
	 * @return string
	 */
	public function add_mail($recipient, $subject, $body, $from = '', $prior = true)
	{
		if(! $id = $this->diafan->_postman->db_add($recipient, $subject, $body, $from, 'mail', true))
		{
			return false;
		}

		if($this->diafan->configmodules('auto_send', 'postman'))
		{
			$this->send($id, $prior);
		}

		return $id;
	}

	/**
	 * Добавляет SMS в список почтовых отправлений
	 *
	 * @param string $text текст SMS
	 * @param string $to номер получателя
	 * @param boolean $prior приоритет при отправлении сообщения
	 * @return mixed string
	 */
	public function add_sms($text, $to, $prior = true)
	{
		$recipient = $to;
		$subject = '';
		$body = $text;
		$from = $this->diafan->configmodules("sms_provider", 'postman');

		if(! $id = $this->diafan->_postman->db_add($recipient, $subject, $body, $from, 'sms', true))
		{
			return false;
		}

		if($this->diafan->configmodules('auto_send', 'postman'))
		{
			$this->send($id, $prior);
		}

		return $id;
	}

	/**
	 * Отправляет уведомление
	 *
	 * @param mixed(array|string) $id идентификатор уведомления
	 * @param boolean $prior приоритет исполнения
	 * @return boolean
	 */
	public function send($id, $prior = true)
	{
		$this->diafan->_executable->execute(array(
			"module" => "postman",
			"method" => "send",
			"params" => array("id" => $id),
			"text"   => $this->diafan->_('Отправка уведомления'),
			"prior"  => !! $prior,
			// "trash" => true,
		));
	}
}

/**
 * Postman_message_exception
 *
 * Исключение для почтовых отправлений
 */
class Postman_message_exception extends Exception{}
