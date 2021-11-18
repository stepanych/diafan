<?php
/**
 * Подключение модуля
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
 * Account_inc
 */
class Account_inc extends Diafan
{
	/**
	 * @var string имя модуля
	 */
	const MODULE_NAME = 'addons';

	/**
	 * @var object вспомогательный объект модуля
	 */
	private $_account = null;

	/**
	 * @var integer метка времени
	 */
	static private $timemarker = 0;

	/**
	 * @var array характеристики текущего пользователя
	 */
	private $_user;

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		Custom::inc('modules/account/admin/account.admin.inc.php');
		$this->_account = new Account_admin_inc($this->diafan);
		self::$timemarker = mktime(23, 59, 0, date("m"), date("d"), date("Y")); // кешируем на сутки
		$this->_user = $this->user();
	}

	/**
	 * Доступ к свойствам текущего пользователя
	 *
	 * @param string $name название переменной
	 * @return mixed
	 */
	public function __get($name)
	{
		if(property_exists($this->_account, $name))
		{
			return ! empty($this->_account->$name) ? $this->_account->$name : '';
		}
		elseif(property_exists($this->_user, $name))
		{
			return ! empty($this->_user->$name) ? $this->_user->$name : '';
		}
		else return null;
	}

	/**
	 * Сохраняет переменные
	 *
	 * @param string $name название переменной
	 * @param mixed $value значение переменной
	 * @return void
	 */
	public function __set($name, $value)
	{
		if(property_exists($this->_account, $name))
		{
			$this->_account->$name = $value;
		}
		elseif(property_exists($this->_user, $name))
		{
			$this->_user->$name = $value;
		}
	}

	/**
	 * Вызывает методы, определенные в файлах действий
	 *
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		if(! $this->_account)
		{
			return false;
		}
		return call_user_func_array(array(&$this->_account, $name), $arguments);
	}

	/**
	 * Определяет данные о пользователе
	 *
	 * @param boolean $upgrade принудительное обновление
	 * @return object
	 */
	private function user($upgrade = false)
	{
		if($upgrade)
		{
			$this->diafan->_cache->delete("", self::MODULE_NAME);
		}

		$cache_meta = array(
			'time' => self::$timemarker,
			'name' => __METHOD__,
			'addr' => getenv('REMOTE_ADDR', true) ?: getenv('REMOTE_ADDR'),
			'host' => getenv('HTTP_HOST', true) ?: getenv('HTTP_HOST'),
			'token' => $this->_account->token,
			'is_auth' => $this->_account->is_auth(),
		);

		if(! $result = $this->diafan->_cache->get($cache_meta, self::MODULE_NAME, CACHE_GLOBAL))
		{
			$result = array();
			if($this->_account->is_auth())
	    {
				$url = $this->_account->uri('users', 'info');
		    if($result = $this->diafan->_client->request($url, $this->_account->token))
		    {
					$this->diafan->attributes($result, 'name', 'fio', 'mail', 'avatar', 'created', 'cash', 'site_info', 'add_money', 'files_buy');
			    $this->diafan->attributes($result["files_buy"], 'buy', 'subscription');
					if(! empty($result["files_buy"]["buy"]))
					{
						foreach($result["files_buy"]["buy"] as &$buy)
						{
							$this->diafan->attributes($buy, 'id', 'name', 'link', 'img', 'file_rewrite');
						}
					}
					if(! empty($result["files_buy"]["subscription"]))
					{
						foreach($result["files_buy"]["subscription"] as &$subscription)
						{
							$this->diafan->attributes($subscription, 'id', 'name', 'link', 'img', 'file_rewrite', 'subscription', 'auto_subscription', 'price_month');
						}
					}
		    }
	    }
			if(empty($this->diafan->_client->errors) && $result)
			{
				$this->diafan->_cache->save($result, $cache_meta, self::MODULE_NAME, CACHE_GLOBAL);
			}
		}
		if(! $result) $result = new stdClass();
		else $result = (object) $result;
		return $result;
	}
}
