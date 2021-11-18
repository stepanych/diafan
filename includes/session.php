<?php
/**
 * @package    DIAFAN.CMS
 *
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
 * Session
 *
 * Работа с сессиями в пользовательской части
 */
class Session extends Diafan
{
	/*
	 * @var string название сессии
	 */
	public $name;

	/*
	 * @var string идентификатор сессии
	 */
	public $id;

	/**
	 * Стартует сессию
	 *
	 * @return void
	 */
	public function init()
	{
		ini_set("session.gc_divisor", 1000);
		ini_set("session.gc_probability", 1);
		ini_set('session.cookie_httponly', 1);
		if(MOBILE_VERSION && defined('MOBILE_SUBDOMAIN') && MOBILE_SUBDOMAIN)
		{
			ini_set('session.cookie_domain', '.' . $this->HTTP_HOST() );
		}

		session_cache_limiter('private_no_expire');
		//$this->name = 'SESS'.md5(getenv('HTTP_HOST').REVATIVE_PATH);
		$this->name = 'SESS'.md5( $this->HTTP_HOST() . REVATIVE_PATH );
		session_name($this->name);
		session_set_save_handler(array(&$this, 'open'), array(&$this, 'close'), array(&$this, 'read'),
		                         array(&$this, 'write'), array(&$this, 'destroy'), array(&$this, 'gc'));
		$this->duration();
		session_start();
		$this->id = session_id();
	}

	/**
	 * Открывает сессию
	 *
	 * @return boolean true
	 */
	public function open()
	{
		return true;
	}

	/**
	 * Закрывает сессию освобождает ресурсы
	 *
	 * @return boolean true
	 */
	public function close()
	{
		return true;
	}

	/**
	 * Читает сессию
	 *
	 * @param string $key идентификатор сессии
	 * @return string
	 */
	public function read($key)
	{
		Dev::register_shutdown_function('session_write_close');

		if (! isset($_COOKIE[$this->name]))
		{
			return '';
		}

		$user = DB::query_fetch_object("SELECT u.*, s.* FROM {users} u INNER JOIN {sessions} s ON u.id=s.user_id"
		    ." WHERE s.session_id='%s' AND s.user_agent='%s' AND u.trash='0' AND u.act='1'",
		    $key, getenv('HTTP_USER_AGENT'));
		if ($user && $user->id > 0)
		{
			$this->diafan->_users->set($user);
			return $user->session;
		}
		else
		{
			$session = DB::query_result("SELECT session FROM {sessions} WHERE session_id='%s' AND user_agent='%s' LIMIT 1",
				$key, getenv('HTTP_USER_AGENT'));
			return ($session ? $session : '');
		}
		return '';
	}

	/**
	 * Записывает данные в сессию
	 *
	 * @param string $key идентификатор сессии
	 * @param string $value серилизованные данные сессии
	 * @return return true
	 */
	public function write($key, $value)
	{
		$row = DB::query_fetch_array("SELECT session_id, user_agent FROM {sessions} WHERE session_id='%s'", $key);

		if(empty($row) || getenv('HTTP_USER_AGENT') != $row["user_agent"])
		{
			if (! empty($row))
			{
				DB::query("DELETE FROM {sessions} WHERE session_id='%s'", $key);
			}
			$referer = getenv('HTTP_REFERER');
			preg_match('/^(http[s]?:\/\/)?(.*?)([\/\?](.*?))?$/msiu', $referer, $matches);
			if (empty($matches[2]) || $matches[2] == $this->HTTP_HOST()) {
				if(! empty($row["referer"])) $referer = $row["referer"];
				elseif(! empty($_COOKIE['session_referer']))
				{
					$referer = $_COOKIE['session_referer'];
					setcookie("session_referer", "", 0, "/");
				}
			}
			if ($this->diafan->_users->id || $value || $referer)
			{
				DB::query("INSERT INTO {sessions} (session_id, user_id, hostname, user_agent, session, timestamp".($referer && ! $this->diafan->_users->admin? ", referer" : "").")"
				." VALUES ('%s', %d, '%s', '%s', '%s', %d".($referer && ! $this->diafan->_users->admin ? ", '%h'" : "").")",
				$key, $this->diafan->_users->id, getenv("REMOTE_ADDR"), getenv('HTTP_USER_AGENT'), $value, time(), $referer);
			}
		}
		else
		{
			if (! $this->diafan->_users->id && ! $value)
			{
				DB::query("DELETE FROM {sessions} WHERE session_id='%s'", $key);
			}
			else
			{
				DB::query("UPDATE {sessions} SET user_id=%d, session='%s', timestamp=%d, hostname='%s' WHERE session_id='%s'", $this->diafan->_users->id, $value, time(), getenv('REMOTE_ADDR'), $key);
			}
		}
        return true;
	}

	/**
	 * Чистит мусор - удаляет сессии старше $lifetime
	 * @return void
	 */
	public function gc()
	{
		//$lifetime = 1209600; // 2 weeks
		$lifetime = 604800; // 1 weeks
		DB::query("DELETE FROM {sessions} WHERE timestamp<%d", time() - $lifetime);
		return true;
	}

	/**
	 * Удаляет ссессию
	 * @param string $key идентификатор сессии
	 * @return void
	 */
	public function destroy($key = '')
	{
		if(! $key)
		{
			$key = $this->id;
		}
		DB::query("DELETE FROM {sessions} WHERE session_id='%s' AND user_agent='%s'", $key, getenv('HTTP_USER_AGENT'));
		$_SESSION = null;
		$this->diafan->_users->id = 0;
		return true;
	}

	/**
	 * Определяет продолжительность сессии
	 *
	 * @return void
	 */
	public function duration()
	{
		if (! empty($_POST['action']) && $_POST['action'] == 'auth')
		{
			if(! empty($_POST['not_my_computer']))
			{
				$duration = 0;
			}
			else
			{
				$duration = 1209600;
			}
			$params = session_get_cookie_params();
			if($params['lifetime'] != $duration)
			{
				session_set_cookie_params($duration);
			}
		}
	}

	public function prepare($config = '')
	{
		if($confg)
		{
			return $config;
		}
	}

	/**
	 * Возвращает доменное имя
	 *
	 * @return void
	 */
	public function HTTP_HOST()
	{
		$domain = getenv('HTTP_HOST');
		if(defined('MAIN_DOMAIN'))
		{
			Custom::inc('plugins/idna.php');
			$IDN = new idna_convert(array('idn_version' => '2008'));
			$domain = $IDN->encode(MAIN_DOMAIN);
			$domain = $domain ? $domain : getenv("HTTP_HOST");
		}
		return $domain;
	}
}
