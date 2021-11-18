<?php
/**
 * Подключение модуля к административной части других модулей
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
 * Account_admin_inc
 */
class Account_admin_inc extends Diafan
{
	/**
   * @var string дефолтное доменное имя для API
   */
	const DOMAIN = 'user.diafan.ru';

	/**
   * @var string дефолтное имя источника для API
   */
	const SOURCE = 'api';

	/**
   * @var string текущее доменное имя для API
   */
	private $api_domain;

	/**
   * @var string текущее имя источника для API
   */
	private $api_source;

	/**
   * @var integer тип авторизации по умолчанию
   */
	public $auth_type = 1;

	/**
	 * @var string путь до временной директории относительно корня сайта
	 */
	public $dir_path = 'tmp/account';

	/**
	 * @var string электронный ключ
	 */
	public $token;

	/**
	 * @var string допустимая версия API
	 */
	public $v = '1';

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		$this->token = $this->diafan->configmodules("token", "account");
		File::create_dir($this->dir_path, true);
		$this->api_domain = self::DOMAIN;
		$this->api_source = self::SOURCE;
		if(defined('MOD_DEVELOPER') && MOD_DEVELOPER)
		{
			if($api_domain = $this->diafan->configmodules("api_domain", "account"))
				$this->api_domain = $api_domain;
			if($api_source = $this->diafan->configmodules("api_source", "account"))
				$this->api_source = $api_source;
		}
		$this->diafan->_client->set_valid_version($this->api_domain, $this->v);
	}

	/**
	 * Возвращает доменное имя для API
	 *
	 * @param boolean $default дефолтное имя
	 * @return string
	 */
	public function api_domain($default = false)
	{
		return $default ? self::DOMAIN : $this->api_domain;
	}

	/**
	 * Возвращает имя источника для API
	 *
	 * @param boolean $default дефолтное имя
	 * @return string
	 */
	public function api_source($default = false)
	{
		return $default ? self::SOURCE : $this->api_source;
	}

	/**
	 * Возвращает URI API
	 *
	 * @param string $module имя модуля
	 * @param string $method имя метода
	 * @param integer $page номер страницы
	 * @param string $urlpage шаблон части ссылки, отвечающей за передачу номера страницы
	 * @return array
	 */
	public function uri($module, $method, $page = false, $urlpage = 'page%d/')
	{
		return $this->diafan->_client->uri($this->api_domain, $this->api_source, $module, $method, $page, $urlpage);
	}

	/**
	 * Проверяет авторизацию API
	 *
	 * @param integer $token электронный ключ
	 * @return boolean
	 */
	public function is_auth($token = null)
	{
		$token = ! is_null($token) ? $token : $this->token;
		if(! $token)
		{
			return false;
		}
		if(isset($this->cache["token"][$token]))
		{
			return !! $this->cache["token"][$token];
		}
		$answer = $this->diafan->_client->token($this->api_domain, $this->api_source, $token);
		$this->cache["token"][$token] = !! $answer;
		if(! $this->cache["token"][$token])
		{
			return false;
		}
		$this->cache["token"][$token] = (! empty($answer["enable"]) && $answer["enable"] == 'on');
		return !! $this->cache["token"][$token];
	}

	/**
	 * Возвращает электронный ключ
	 *
	 * @param string $login имя учтной записи
	 * @param string $password пароль учетной записи
	 * @return string
	 */
	public function auth($login, $password)
	{
		return $this->diafan->_client->auth($this->api_domain, $this->api_source, $login, $password);
	}

	/**
	 * Отзывает электронный ключ
	 *
	 * @param integer $flag флаг или комбинация флагов запроса
	 * @return array
	 */
	public function revoke($flag = CLIENT_LOCAL_REVOKE)
	{
		return $this->diafan->_client->revoke($this->api_domain, $this->api_source, $this->token, $flag);
	}
}

/**
 * Account_admin_inc_exception
 *
 * Исключение для подключений модуля к административной части других модулей
 */
class Account_admin_inc_exception extends Exception{}
