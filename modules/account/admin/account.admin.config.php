<?php
/**
 * Настройки модуля
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
 * Account_admin_config
 */
class Account_admin_config extends Frame_admin
{
	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'base' => array (
			'hr1' => array(
				'type' => 'title',
				'name' => 'Основные',
			),
			'api_domain' => array(
				'type' => 'text',
				'name' => 'Доменное имя API',
				'help' => 'API-хост (Например, user.diafan.ru).',
			),
			'api_source' => array(
				'type' => 'text',
				'name' => 'Имя источника API',
				'help' => 'API-источник (Например, api).',
			),
			'token' => array(
				'type' => 'none',
				'name' => 'Токен API',
				'help' => 'Электронный ключ.',
				'no_save' => true,
			),
		),
	);

	/**
	 * @var array названия табов
	 */
	public $tabs_name = array(
		'base' => 'Основные настройки',
	);

	/**
	 * @var array настройки модуля
	 */
	public $config = array (
		'tab_card', // использование вкладок
		'config', // файл настроек модуля
	);

	/**
	 * Подготавливает конфигурацию модуля
	 *
	 * @return void
	 */
	public function prepare_config()
	{
		if(! defined('MOD_DEVELOPER') || ! MOD_DEVELOPER)
		{
			$this->diafan->variable_disabled("api_domain", true);
			$this->diafan->variable_disabled("api_source", true);
		}
		else
		{
			$this->diafan->variable_disabled("api_domain", false);
			$this->diafan->variable_disabled("api_source", false);
		}
	}

	/**
	 * Получает значение поля
	 * @param string $field название поля
	 * @param mixed $default значение по умолчанию
	 * @param boolean $save записать значение по умолчанию
	 * @return mixed
	 */
	public function values($field, $default = false, $save = false)
	{
		$value = parent::__call('values', array($field, $default, $save));
		if(! defined('MOD_DEVELOPER') || ! MOD_DEVELOPER)
		{
			if($field == 'api_domain') $value = $this->diafan->_account->api_domain();
			if($field == 'api_source') $value = $this->diafan->_account->api_source();
		}
		elseif(empty($value))
		{
			if($field == 'api_domain') $value = $this->diafan->_account->api_domain();
			if($field == 'api_source') $value = $this->diafan->_account->api_source();
		}
		return $value;
	}
}
