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
 * Account_admin_action
 */
class Account_admin_action extends Action_admin
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
				case 'warn_read':
					$this->warn_read();
					break;

				case 'close':
					$this->close();
					break;
			}
		}
	}

	/**
	 * Отметка о прочтении сообщения "Предупреждение"
	 *
	 * @return void
	 */
	private function warn_read()
	{
		$this->diafan->attributes($_POST, 'checked');
		$this->result["result"] = 'success';

		if(! $this->diafan->_account->is_auth())
    {
      return;
    }
    $url = $this->diafan->_account->uri('support', 'warn_read');
		$param = array(
			'checked' => !empty($_POST["checked"]) ? '1' : '',
		);
    $result = $this->diafan->_client->request($url, $this->diafan->_account->token, $param);

		return;
	}

	/**
	 * Закрывается тикет
	 *
	 * @return void
	 */
	private function close()
	{
		$this->diafan->attributes($_POST, 'id');
		$this->result["result"] = 'success';

		if(! $this->diafan->_account->is_auth())
    {
      return;
    }
    $url = $this->diafan->_account->uri('support', 'close');
		$param = array(
			'id' => $this->diafan->filter($_POST, "integer", "id"),
		);
		$result = $this->diafan->_client->request($url, $this->diafan->_account->token, $param);

		return;
	}
}
