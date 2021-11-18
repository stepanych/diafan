<?php
/**
 * Обработка POST-запросов в административной части модуля
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
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

class Cashregister_admin_action extends Action_admin
{
    public function init()
	{
        if (empty($_POST["action"]))
            return false;

        switch ($_POST["action"])
		{
            case 'test':
                if(! $this->result['error'] = $this->diafan->_cashregister->receipt_test())
				{
					$this->result['data'] = $this->diafan->_('Чек создан.');
				}
                break;

			case 'send':
			case 'group_send':
				$this->group_send();
                break;
        }
    }

	/**
	 * Групповая отправка чеков или отправка чеков кнопкой управления
	 *
	 * @return void
	 */
	public function group_send()
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

		$ids = array();
		if(! empty($_POST["ids"]))
		{
			$ids = $this->diafan->filter($_POST["ids"], "uid");
		}
		elseif(! empty($_POST["id"]))
		{
			$ids = array($this->diafan->filter($_POST["id"], "uid"));
		}

		if(! empty($ids))
		{
			foreach($ids as $id)
			{
				$id = $this->diafan->_db_ex->converter_id('{shop_cashregister}', $id);
				if(false === $id)
				{
					continue;
				}
				$this->diafan->_cashregister->receipt_send($id);
			}
		}
		if(count($ids) > 1)
		{
			$msg = 'Чеки отправлены.';
		}
		else
		{
			$msg = 'Чек отправлен.';	
		}
		$this->diafan->set_one_shot(
			'<div class="ok">'.$this->diafan->_($msg).'</div>'
		);

		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}
}