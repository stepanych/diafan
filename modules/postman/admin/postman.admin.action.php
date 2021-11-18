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
 * Postman_admin_action
 */
class Postman_admin_action extends Action_admin
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
				case 'send':
				case 'group_send':
					$this->group_option();
					break;

				case 'smtp_check':
					$this->smtp_check();
					break;
			}
		}
	}

	/**
	 * Групповая операция "Отправить уведомления"
	 *
	 * @return void
	 */
	private function group_option()
	{
		$ids = array();
		if(! empty($_POST["ids"]))
		{
			foreach ($_POST["ids"] as $id)
			{
				$id = $this->diafan->_db_ex->filter_uid($id);
				if($id)
				{
					$ids[] = $id;
				}
			}
		}
		elseif(! empty($_POST["id"]))
		{
			$ids = array($this->diafan->_db_ex->filter_uid($_POST["id"]));
		}
		if(! empty($ids))
		{
			switch ($_POST["action"])
			{
				case 'send':
				case 'group_send':
					$this->group_send($ids);
					break;
			}
		}
	}

	/**
	 * Групповая отправка уведомлений или отправка уведомления кнопкой управления
	 *
	 * @param array $ids идентификаторы
	 * @return void
	 */
	public function group_send($ids)
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->result["redirect"] = URL;
			return;
		}

		//проверка прав пользователя на редактирование модуля
		if (! $this->diafan->_users->roles('edit', $this->diafan->_admin->rewrite))
		{
			$this->result["redirect"] = URL;
			return;
		}

		if(! empty($ids))
		{
			foreach($ids as $id)
			{
				$id = $this->diafan->_db_ex->converter_id('{postman}', $id);
				if(false === $id)
				{
					continue;
				}
				$this->diafan->_postman->message_send($id);
			}
		}

		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}

	/**
	 * Проверка SMTP-соединения
	 *
	 * @return void
	 */
	private function smtp_check()
	{
		if(empty($_POST["smtp_host"]) || empty($_POST["smtp_login"]) || empty($_POST["smtp_password"]))
		{
			$this->result["error"] = $this->diafan->_('Заполните все поля.');
			return;
		}
		try
		{
			Custom::inc('plugins/class.phpmailer.php');

			$mail = new PHPMailer();
			$mail->isSMTP(); // telling the class to use SMTP
			$mail->Host       = $_POST["smtp_host"];     // SMTP server
			$mail->SMTPDebug = 1;
			$mail->SMTPAuth   = true;          // enable SMTP authentication
			if (! empty($_POST["smtp_port"]))
			{
				$mail->Port   = $_POST["smtp_port"];
			}
			$mail->Username   = $_POST["smtp_login"];
			$mail->Password   = $_POST["smtp_password"];

			$mail->SMTPAutoTLS = false;
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);

			ob_start();
			$check = $mail->smtpConnect($mail->SMTPOptions);
			if($check)
			{
	            $mail->smtp->quit();
	            $mail->smtp->close();
			}
			$trace_output = ob_get_contents();
			ob_end_clean();
	        if (! $check) 
	        {
				$this->result["error"] = $this->diafan->_('SMTP соединение не установлено. Обратитесь к своему хостинг провайдеру.').'<br>'.$trace_output;
				return;
	        }
		}
		catch (Exception $e)
		{
			$this->result["error"] = $this->diafan->_('SMTP соединение не установлено. Обратитесь к своему хостинг провайдеру.');
			return;
		}

		$this->result["data"] = $this->diafan->_('SMTP соединение установлено.');
	}
}
