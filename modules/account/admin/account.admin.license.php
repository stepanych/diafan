<?php
/**
 * Редактирование модуля
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
 * Account_admin_license
 */
class Account_admin_license extends Frame_admin
{
  /**
	 * @var boolean маркер отложенной загрузки контента
	 */
	public $defer = true;

  /**
	 * @var string заголовок отложенной загрузки контента
	 */
	public $defer_title = 'Подождите, идет соединение с сервером ...';

  /**
	 * @var array массив текущих ошибок
	 */
	public $self_errors = array();

  /**
	 * Выводит содержание "Ваша лицензия"
   *
	 * @return void
	 */
	public function show()
	{
        if(! $this->diafan->_account->is_auth())
        {
            if(! $this->diafan->defer) $this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/'.$this->diafan->_admin->module.'/');
            else $this->diafan->defer_redirect = BASE_PATH.ADMIN_FOLDER.'/'.$this->diafan->_admin->module.'/';
        }
        $this->info_block();
	}

  /**
	 * Выводит содержание "Информационный блок"
   *
	 * @return void
	 */
	public function info_block()
	{
        if(! $this->diafan->_account->is_auth())
        {
          return;
        }
        $url = $this->diafan->_account->uri('users', 'license');
        if(! $result = $this->diafan->_client->request($url, $this->diafan->_account->token))
        {
          return;
        }
        $this->diafan->attributes($result, 'license');

        echo '
        <div class="box box_height">';
        echo $result["license"];
        echo '
        </div>';
	}
}
