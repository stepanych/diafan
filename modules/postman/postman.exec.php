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

class Postman_exec extends Exec
{
	/**
	 * Инициализация API модуля
	 *
	 * @return void
	 */
	public function init()
	{
		switch($this->method)
		{
			case 'send':
				$this->send();
				break;

			default:
				$this->set_error();
				break;
		}
	}

	/**
	 * Отправляет уведомление
	 *
	 * @return void
	 */
	public function send()
	{
		// @param mixed(array|string) $id идентификатор уведомления
		$id = $this->diafan->filter($_POST, 'string', 'id');

		if(! $row = $this->diafan->_db_ex->get('{postman}', $id))
		{
			return;
		}

		$status = false;
		$row["error"] = $row["trace"] = '';
		$this->diafan->_db_ex->update('{postman}', $row["id"], array("timesent='%d'", "status='%h'", "error='%s'", "trace='%s'"), array(time(), (! $status ? '2' : '1'), $row["error"], $row["trace"]));

		switch ($row["type"])
		{
			case 'mail':
				try {
					if(empty($row["recipient"]))
					{
						throw new Exception('Ошибка: для отправки уведомления необходимо указать адрес получателя.');
					}
					$status = $this->send_mail($row["recipient"], $row["subject"], $row["body"], $row["from"], false, $row["error"], $row["trace"]);
				} catch (Exception $e) {
					$row["error"] = $e->getMessage();
					$row["trace"] = '';
					$status = false;
				}
				break;

			case 'sms':
				try {
					if(empty($row["recipient"]))
					{
						throw new Exception('Ошибка: для отправки уведомления необходимо указать адрес получателя.');
					}
					if(! $this->diafan->configmodules("sms", 'postman'))
					{
						throw new Exception('Ошибка: для отправки уведомления необходимо настроить SMS-уведомления.');
					}
					$from = $this->diafan->configmodules("sms_provider", 'postman');
					$this->diafan->_db_ex->update('{postman}', $row["id"], array("`from`='%h'"), array($from));
					$status = $this->send_sms($row["body"], $row["recipient"], $row["error"], $row["trace"]);
				} catch (Exception $e) {
					$row["error"] = $e->getMessage();
					$row["trace"] = '';
					$status = false;
				}
				break;

			default:
				return;
				break;
		}

		if(! $this->diafan->configmodules('del_after_send', 'postman') || ! $status)
		{
			$this->diafan->_db_ex->update('{postman}', $row["id"], array("timesent='%d'", "status='%h'", "error='%s'", "trace='%s'"), array(time(), (! $status ? '2' : '1'), $row["error"], $row["trace"]));
		}
		else
		{
			$this->diafan->_db_ex->delete('{postman}', $id);
		}
	}

	/**
	 * Отправляет письмо
	 *
	 * @param string|array $recipient получатель/получатели
	 * @param string $subject тема письма
	 * @param string $body содержание письма
	 * @param string $from адрес отправителя
	 * @param string $error_output вывод ошибки
	 * @param string $trace_output вывод трассировки
	 * @param array $attachments массив прикрепленных файлов
	 * @return boolean
	 */
	private function send_mail($recipient, $subject, $body, $from = '', $attachments = false, &$error_output = '', &$trace_output = '')
	{
		Custom::inc('plugins/class.phpmailer.php');

		$mail = new PHPMailer();

		if($this->diafan->configmodules("smtp_mail", 'postman')
		&& $this->diafan->configmodules("smtp_host", 'postman')
		&& $this->diafan->configmodules("smtp_login", 'postman')
		&& $this->diafan->configmodules("smtp_password", 'postman'))
		{
			$mail->isSMTP(); // telling the class to use SMTP
			$mail->Host       = $this->diafan->configmodules("smtp_host", 'postman');     // SMTP server
			$mail->SMTPDebug = 1;
			// $mail->SMTPDebug  = MOD_DEVELOPER ? 1 : 0; // enables SMTP debug information (for testing)
			// 								                           // 1 = errors and messages
			// 								                           // 2 = messages only
			$mail->SMTPAuth   = true;          // enable SMTP authentication
			if ($this->diafan->configmodules("smtp_port", 'postman'))
			{
				$mail->Port   = $this->diafan->configmodules("smtp_port", 'postman');       // set the SMTP port for the GMAIL server
			}
			$mail->Username   = $this->diafan->configmodules("smtp_login", 'postman');    // SMTP account username
			$mail->Password   = $this->diafan->configmodules("smtp_password", 'postman'); // SMTP account password

			// TO_DO: Don't mix up these modes; ssl on port 587 or tls on port 465 will not work.
			// TO_DO: PHPMailer 5.2.10 introduced opportunistic TLS - if it sees that the server is advertising TLS encryption (after you have connected to the server), it enables encryption automatically, even if you have not set SMTPSecure. This might cause issues if the server is advertising TLS with an invalid certificate, but you can turn it off with $mail->SMTPAutoTLS = false;.
			$mail->SMTPAutoTLS = false;

			// TO_DO: Failing that, you can allow insecure connections via the SMTPOptions property introduced in PHPMailer 5.2.10 (it's possible to do this by subclassing the SMTP class in earlier versions), though this is not recommended as it defeats much of the point of using a secure transport at all:
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
		}

		$mail->setFrom($from ? $from : $this->diafan->configmodules("email", 'postman'), TITLE);
		$mail->Subject = $subject;
		$mail->msgHTML($body);

		if (is_array($recipient))
		{
			foreach ($recipient as $to)
			{
				$mail->addAddress($to);
			}
		}
		elseif (strpos($recipient, ',') !== false)
		{
			$recipients = explode(',', $recipient);
			foreach ($recipients as $r)
			{
				$mail->addAddress(trim($r));
			}
		}
		else
		{
			$mail->addAddress($recipient);
		}
		if($attachments && is_array($attachments))
		{
			foreach($attachments as $a)
			{
				if(is_array($a))
				{
					$mail->addAttachment($a["path"], $a["name"]);
				}
				else
				{
					$mail->addAttachment($a);
				}
			}
		}

		ob_start();
		$mailssend = $mail->send();
		$trace_output = ob_get_contents();
		ob_end_clean();
		$error_output = $mail->ErrorInfo;
		return $mailssend;
	}

	/**
	 * Отправляет SMS
	 *
	 * @param string $text текст SMS
	 * @param string $to номер получателя
	 * @param string $error_output вывод ошибки
	 * @param string $trace_output вывод трассировки
	 * @return boolean
	 */
	private function send_sms($text, $to, &$error_output = '', &$trace_output = '')
	{
		if(! $this->diafan->configmodules("sms", 'postman'))
		{
			$error_output = "ERROR: SMS isn't enabled";
			return false;
		}
		$backend = $this->diafan->configmodules("sms_provider", 'postman');
		if(! $backend
		|| ! Custom::exists('modules/postman/backend/'.$backend.'/postman.'.$backend.'.sms.php'))
		{
			$error_output = "ERROR: no service provider defined";
			return false;
		}
		$to = preg_replace('/[^0-9]+/', '', $to);
		Custom::inc('includes/validate.php');
		if($error = Validate::phone($to))
		{
			$error_output = "ERROR: ".$error;
			return false;
		}
		$text = urlencode(str_replace("\n", "%0D", substr($text, 0, 800)));
		$backend = $this->diafan->configmodules("sms_provider", 'postman');
		if(! Custom::exists('modules/postman/backend/'.$backend.'/postman.'.$backend.'.sms.php'))
		{
			$error_output = "ERROR: unidentified service provider";
			return false;
		}
		Custom::inc('modules/postman/backend/'.$backend.'/postman.'.$backend.'.sms.php');

		$name_class = 'Postman_'.$backend.'_sms';
		$class = new $name_class($this->diafan);
		if (! is_callable(array(&$class, "send")))
		{
			$error_output = "ERROR: unidentified service provider";
			return false;
		}
		return $class->send($text, $to, $error_output, $trace_output);
	}
}
