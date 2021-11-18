<?php
/**
 * Количество непрочитанных уведомлений службы поддержки для меню административной панели
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
 * Account_admin_support_tab_count
 */
class Account_admin_support_tab_count extends Diafan
{
	/**
	 * @var integer метка времени
	 */
	static private $count = 0;

	/**
	 * Возвращает количество непрочитанных уведомлений службы поддержки для меню административной панели
	 *
	 * @return integer
	 */
	public function count()
	{
		if(self::$count)
		{
			return self::$count;
		}

		self::$count = 0;

		if(! $this->diafan->_account->is_auth())
    {
      return self::$count;
    }
    $url = $this->diafan->_account->uri('support', 'count');
		if(! $result = $this->diafan->_client->request($url, $this->diafan->_account->token))
    {
      return self::$count;
    }
    $this->diafan->attributes($result, 'count');

		self::$count += $this->diafan->filter($result, 'integer', 'count');

		return self::$count;
	}
}
