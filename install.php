<?php
/**
 * @package    DIAFAN.CMS
 *
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */
 
 
/**
 *
 * Файл-установщик DIAFAN.CMS. Не требует правок. Запускается и удаляется после установки автоматически.
 *
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

define('IS_INSTALL', true);
define('INSTALL_DEMO', false);

/**
 * Installation
 *
 * Установка DIAFAN.CMS
 */
class Installation extends Diafan
{
	/**
	 * @var string номер минорной версии DIAFAN.CMS
	 */
	private $version = '7.0';

	/**
	 * @var array модули ядра в порядке установки
	 */
	private $core_tables = array('service', 'admin', 'config', 'images', 'attachments', 'menu', 'site');

	/**
	 * @var array шаги установки
	 */
	private $steps = array(
		'step1' => 'Проверка системы',
		'step2' => 'Лицензия',
		'step3' => 'Параметры',
		'step4' => 'Администратор сайта',
		'step5' => 'Конфигурация',
		'step6' => 'Окончание'
	);

	/**
	 * @var object данные для шаблона
	 */
	private $view;

	/**
	 * Инициализация установки
	 *
	 * @return void
	 */
	public function init()
	{
		header('Content-Type: text/html; charset=utf-8');

		if (empty($_GET["rewrite"]) || ! preg_match('/^installation/', $_GET["rewrite"]))
		{
			header("Cache-Control: no-cache");

			$dir_url_path = preg_replace('/index\.php$/', '', getenv('SCRIPT_NAME'));

			header('Location: http'.(IS_HTTPS ? "s" : '').'://'.getenv("HTTP_HOST").$dir_url_path."installation/", true, 302);
			exit;
		}
		$_GET["rewrite"] = preg_replace('/^install.php[\/]*/', '', $_GET["rewrite"]);
		$_GET["rewrite"] = preg_replace('/^installation[\/]*/', '', $_GET["rewrite"]);

		$this->config();
		$this->view = new stdClass();
		$this->route();
		$this->template();
	}

	/**
	 * Определяет констранты из файла config.php, если файл еще не записан
	 *
	 * @return void
	 */
	private function config()
	{
		if (! defined("MOD_DEVELOPER"))
		{
			define('MOD_DEVELOPER', false);
		}
		if (! defined("REVATIVE_PATH"))
		{
			$dir_url_path = preg_replace('/^(\/*)(.*?)(\/*)(index\.php)$/', '$2', getenv('SCRIPT_NAME'));
			define('REVATIVE_PATH', $dir_url_path);
		}
		define('BASE_PATH', "http".(IS_HTTPS ? "s" : '')."://".getenv("HTTP_HOST")."/".(REVATIVE_PATH ? REVATIVE_PATH.'/' : ''));
	}

	/**
	 * Подключение шага установки
	 *
	 * @return void
	 */
	private function route()
	{
		if(function_exists('set_time_limit'))
		{
			$disabled = explode(',', ini_get('disable_functions'));
			if(! in_array('set_time_limit', $disabled))
			{
				set_time_limit(0);
			}
		}
		if (empty($_GET["rewrite"]))
		{
			$_GET["rewrite"] = "step1";
		}
		elseif(! in_array($_GET["rewrite"], array_keys($this->steps)))
		{
			include_once ABSOLUTE_PATH.'includes/404.php';
		}

		ini_set("session.save_path", ABSOLUTE_PATH.'tmp');
		session_name('SESS'.md5(getenv('HTTP_HOST')));
		session_start();

		switch ($_GET["rewrite"])
		{
			case 'step3':
				$this->step3();
				break;

			case 'step4':
				$this->step4();
				break;

			case 'step5':
				$this->step5();
				break;

			case 'step6':
				$this->step6();
				break;
		}
	}

	/**
	 * Действия на третьем шаге установки
	 *
	 * @return void
	 */
	private function step3()
	{
		$this->view->site_name = (! empty($_SESSION["install_name"]) ? str_replace('"', '&quot;', $_SESSION["install_name"]) : '');
		$this->view->db_host = (! empty($_SESSION["install_db_host"]) ? str_replace('"', '&quot;', $_SESSION["install_db_host"]) : 'localhost');
		$this->view->db_user = (! empty($_SESSION["install_db_user"]) ? str_replace('"', '&quot;', $_SESSION["install_db_user"]) : '');
		$this->view->db_pass = (! empty($_SESSION["install_db_pass"]) ? str_replace('"', '&quot;', $_SESSION["install_db_pass"]) : '');
		$this->view->db_name = (! empty($_SESSION["install_db_name"]) ? str_replace('"', '&quot;', $_SESSION["install_db_name"]) : '');
		$this->view->db_prefix = (! empty($_SESSION["install_db_prefix"]) ? str_replace('"', '&quot;', $_SESSION["install_db_prefix"]) : 'diafan_');
	}

	/**
	 * Действия на четвертом шаге установки
	 *
	 * @return void
	 */
	private function step4()
	{
		if(! empty($_POST["form"]))
		{
			$_SESSION["install_name"] = $this->diafan->filter($_POST, 'string', "name");
			$_SESSION["install_db_host"] = $this->diafan->filter($_POST, 'string', "db_host");
			$_SESSION["install_db_user"] = $this->diafan->filter($_POST, 'string', "db_user");
			$_SESSION["install_db_pass"] = $this->diafan->filter($_POST, 'string', "db_pass");
			$_SESSION["install_db_name"] = $this->diafan->filter($_POST, 'string', "db_name");
			$_SESSION["install_db_prefix"] = $this->diafan->filter($_POST, 'string', "db_prefix");

			if (empty($_POST['db_host']) || empty($_POST['db_user']) || empty($_POST['db_name']))
			{
				$this->error('Вами введены неверные данные о БД MySQL или не заполнены необходимые поля формы.');
			}
			$sheme = function_exists('extension_loaded') && extension_loaded('mysqli') ? 'mysqli' : 'mysql';

			$db_url = $sheme.'://'.$this->diafan->filter($_POST, 'string', "db_user");
			if($_POST["db_pass"])
			{
				$db_url .= ":".urlencode($this->diafan->filter($_POST, 'string', "db_pass"));
			}
			$db_url .= "@".$this->diafan->filter($_POST, 'string', "db_host")
			."/".$this->diafan->filter($_POST, 'string', "db_name");

			if(! defined('DB_PREFIX'))
			{
				define('DB_PREFIX', '');
			}
			if(! defined('DB_CHARSET'))
			{
				define('DB_CHARSET', 'utf8mb4');
			}
			if(! defined('DB_URL'))
			{
				define('DB_URL', $db_url);
			}
			if (! DB::connect($db_url, true))
			{
				$this->error('Введены неверные параметры подключения к базе данных MySQL!');
			}
			if (preg_match('/[^0-9a-zA-Z\_+]/', $_POST["db_prefix"]))
			{
				$this->error('Префикс может содержать только латинские буквы, цифры и нижнее подчеркивание!');
			}
			if (preg_match('/^[0-9+]/', $_POST["db_prefix"]))
			{
				$this->error('Префикс не должен начинаться с цифры!');
			}
			if(empty($_POST["db_clear_prefix"]))
			{
				$db = $this->diafan->filter($_POST, 'string', "db_name");
				$rows = DB::query_fetch_all("SHOW TABLES FROM `".$db."`");
				foreach ($rows as $row)
				{
					if ($row["Tables_in_".$db] == $this->diafan->filter($_POST, 'string', "db_prefix")."users")
					{
						$this->error('Префикс занят!');
					}
				}
			}

			$new_values = array(
					'TIT1' => str_replace('\\"', '&quot;', $this->diafan->filter($_POST, 'string', "name")),
					'DB_URL' => $db_url,
					'DB_PREFIX' => str_replace('"', '&quot;', $this->diafan->filter($_POST, 'string', "db_prefix")),
					'DB_CHARSET' => 'utf8mb4',
					'LANGUAGE_BASE' => 'ru',
					'VERSION_CMS' => $this->version,
					'MOD_DEVELOPER' => false,
					'MOD_DEVELOPER_CACHE' => false,
					'MOD_DEVELOPER_MINIFY' => false,
					'MOD_DEVELOPER_PROFILING' => false,
					'MOD_DEVELOPER_PROFILER' => false,
					'MOD_DEVELOPER_POST' => false,
					'MOD_PROTECTED' => false,
					'ROUTE_END' => '/',
					'ROUTE_AUTO_MODULE' => true,
					'USERADMIN' => true,
					'MOBILE_VERSION' => false,
					'MOBILE_PATH' => 'm',
					'MOBILE_SUBDOMAIN' => false,
					'SOURCE_JS' => 1,
					'ADMIN_FOLDER' => 'admin',
					'USERFILES' => 'userfls',
				);
			include_once(ABSOLUTE_PATH.'includes/config.php');
			Config::save($new_values, array(0 => array('id' => 1)), INSTALL_DEMO);
			if(INSTALL_DEMO)
			{
				$this->diafan->redirect(BASE_PATH.'installation/step6/');
			}
		}
		$this->view->fio = (! empty($_SESSION["install_admin_fio"]) ? str_replace('"', '&quot;', $_SESSION["install_admin_fio"]) : '');
		$this->view->admin_name = (! empty($_SESSION["install_admin_name"]) ? str_replace('"', '&quot;', $_SESSION["install_admin_name"]) : '');
		$this->view->mail = (! empty($_SESSION["install_admin_mail"]) ? str_replace('"', '&quot;', $_SESSION["install_admin_mail"]) : '');
		$this->view->folder = (! empty($_SESSION["install_admin_folder"]) ? str_replace('"', '&quot;', $_SESSION["install_admin_folder"]) : 'admin');
		$this->view->userfiles = (! empty($_SESSION["install_userfiles"]) ? str_replace('"', '&quot;', $_SESSION["install_userfiles"]) : 'userfls');
	}

	/**
	 * Действия на пятом шаге установки
	 *
	 * @return void
	 */
	private function step5()
	{
		if(! empty($_POST["form"]))
		{
			$userfiles = $this->diafan->filter($_POST, 'string', "userfiles", 'userfls');
			$_SESSION["install_admin_mail"] = $this->diafan->filter($_POST, 'string', "mail");
			$_SESSION["install_admin_name"] = $this->diafan->filter($_POST, 'string', "name");
			$_SESSION["install_admin_pass"] = $this->diafan->filter($_POST, 'string', "pass");
			$_SESSION["install_admin_fio"] = $this->diafan->filter($_POST, 'string', "fio");
			$_SESSION["install_admin_folder"] = $this->diafan->filter($_POST, 'string', "folder", 'admin');
			$_SESSION["install_userfiles"] = $userfiles;

			if(! File::is_writable($userfiles))
			{
				$_SESSION["install_userfiles"] = '';
				$this->error('Папка '.$userfiles.' должна быть доступна на запись!');
			}

			include_once(ABSOLUTE_PATH.'includes/validate.php');
			if ($mes = Validate::password($_POST["pass"], true))
			{
				$this->error($mes);
			}
			if ($mes = Validate::mail($_POST['mail']))
			{
				$this->error($mes);
			}

			if (empty($_POST['fio']) || empty($_POST['name']) || empty($_POST['pass'])
			   || empty($_POST['mail']) || empty($_POST['folder']))
			{
				$this->error('Заполните, пожалуйста, все поля.');
			}

			$new_values = array(
					'ADMIN_FOLDER' => $this->diafan->filter($_POST, 'string', "folder"),
					'USERFILES' => $userfiles,
				);
			include_once(ABSOLUTE_PATH.'includes/config.php');
			Config::save($new_values, array(0 => array('id' => 1)));
		}

		include_once(ABSOLUTE_PATH."includes/install.php");

		if ($dir = opendir(ABSOLUTE_PATH.'modules'))
		{
			while (($module = readdir($dir)) !== false)
			{
				if ($module != '.' && $module != '..')
				{
					if (file_exists(ABSOLUTE_PATH.'modules/'.$module.'/'.$module.'.install.php'))
					{
						include_once(ABSOLUTE_PATH.'modules/'.$module.'/'.$module.'.install.php');
						$name = Ucfirst($module).'_install';
						$class = new $name($this->diafan);

						if(! $class->is_core)
						{
							// сортировка модулей как в меню административной части
							$sort = 99;
							if(! empty($class->admin[0]["sort"]))
							{
								$sort = $class->admin[0]["sort"];
							}
							while(isset($this->view->modules[$sort]))
							{
								$sort++;
							}
							$this->view->modules[$sort] = array("name" => $module, "title" => $class->title);
						}
					}
				}
			}
			closedir($dir);
		}
		ksort($this->view->modules);
	}

	/**
	 * Действия на шестом шаге установки
	 *
	 * @return void
	 */
	private function step6()
	{
		if(empty($_SESSION["install_admin_name"]) && ! INSTALL_DEMO)
		{
			return;
		}
		File::delete_dir('custom/my');
		if (isset($_FILES["custom"]) && is_array($_FILES["custom"]) && $_FILES["custom"]['name'] != '')
		{
			if(! class_exists('ZipArchive'))
			{
				$this->error('Не доступно обязательное PHP-расширение ZipArchive. Обратитесь в техническую поддержку хостинга и попросите установить.');
			}
			File::create_dir('custom/my');

			$paths = array();
			$zip = new ZipArchive;
			if ($zip->open($_FILES['custom']['tmp_name']) === true)
			{
				$zip->extractTo(ABSOLUTE_PATH.'custom/my');
				$zip->close();
				Custom::init('my');
			}
			else
			{
				$this->error('Некорректный архив с тематическим шаблоном дизайна.');
			}

			File::delete_dir(USERFILES.'/demo/custom/my');
			$_POST["example_yes"] = true;
		}
		if(INSTALL_DEMO  || ! empty($_POST["example_yes"]))
		{
			if(defined('USERFILES'))
			{
				$userfiles = USERFILES;
			}
			else
			{
				$userfiles = 'userfls';
			}
			if(! is_dir(ABSOLUTE_PATH.$userfiles.'/demo'))
			{
				if(! file_exists(ABSOLUTE_PATH.$userfiles.'/demo.zip'))
				{
					File::copy_file('http'.(IS_HTTPS ? "s" : '').'://www.diafan.ru/demo.zip', $userfiles.'/demo.zip');
				}
				if(! class_exists('ZipArchive'))
				{
					$this->error('На сервере не установлено расширение для распаковки ZIP-архивов. Распакуйте вручную содержимое архива '.$userfiles.'/demo.zip в папку '.$userfiles.'/demo или попросите техническую поддержку хостинга установить ZIP-распаковщик.');
				}
				$zip = new ZipArchive;
				if ($zip->open(ABSOLUTE_PATH.$userfiles.'/demo.zip') === true)
				{
					$zip->extractTo($userfiles.'/demo');
					$zip->close();
				}
			}
		}
		setcookie('useradmin', false, mktime(0, 0, 0, 1, 1, 2000), '/');

		if(INSTALL_DEMO)
		{
			include_once(ABSOLUTE_PATH.'includes/demo.php');
			$demo = new Demo($this);
			$demo->install();
		}
		else
		{
			define('_LANG', 1);

			$langs = array(1);
			if(! empty($_POST["lang_yes"]))
			{
				$langs[] = 2;
			}
			$install_modules = array();
			// если выбран шабон, устанавливаем только модули, папки которых есть в шаблоне
			if(is_dir(ABSOLUTE_PATH.'custom/my'))
			{
				if ($dir = opendir(ABSOLUTE_PATH.'custom/my/modules'))
				{
					while (($module = readdir($dir)) !== false)
					{
						if ($module != '.' && $module != '..'
						&& (file_exists(ABSOLUTE_PATH.'modules/'.$module.'/'.$module.'.install.php') || file_exists(ABSOLUTE_PATH.'custom/my/modules/'.$module.'/'.$module.'.install.php'))
						&& ! in_array($module, $this->core_tables))
						{
							$install_modules[] = $module;
						}
					}
					closedir($dir);
				}
			}
			else
			{
				$install_modules = ! empty($_POST["modules"]) ? array_keys($_POST["modules"]) : array();
			}

			include_once ABSOLUTE_PATH.'plugins/encoding.php';

			include_once(ABSOLUTE_PATH."includes/install.php");
			include_once(ABSOLUTE_PATH."includes/model.php");

			$modules = $this->core_tables;

			$dir = Custom::read_dir('modules');

			foreach($dir as $module)
			{
				if (file_exists(ABSOLUTE_PATH.'modules/'.$module.'/'.$module.'.install.php') || file_exists(ABSOLUTE_PATH.'custom/my/modules/'.$module.'/'.$module.'.install.php'))
				{
					if(file_exists(ABSOLUTE_PATH.'custom/my/modules/'.$module.'/'.$module.'.install.php'))
					{
						include_once(ABSOLUTE_PATH.'custom/my/modules/'.$module.'/'.$module.'.install.php');
					}
					else
					{
						include_once(ABSOLUTE_PATH.'modules/'.$module.'/'.$module.'.install.php');
					}
					$name = Ucfirst($module).'_install';
					$class[$module] = new $name($this->diafan);
					if($class[$module]->is_core || in_array($module, $install_modules))
					{
						$class[$module]->langs = $langs;
						$class[$module]->module = $module;
						$class[$module]->install_modules = $install_modules;
						if(! in_array($module, $this->core_tables))
						{
							$modules[] = $module;
						}
					}
				}
			}
			foreach($modules as $module)
			{
				$class[$module]->tables();
			}
			foreach($modules as $module)
			{
				$class[$module]->start(! empty($_POST["example_yes"]));
			}
			foreach($modules as $module)
			{
				$class[$module]->action_post();
			}
			$this->diafan->_cache->delete("", array());

			$this->view->admin_name = $_SESSION["install_admin_name"];
			$this->view->admin_pass = $_SESSION["install_admin_pass"];
		}
		$_SESSION = array();
		try
		{
			File::delete_file("install.php");
		}
		catch (Exception $e)
		{
			return;
		}
	}

	/**
	 * Подключение шаблона установки
	 *
	 * @return void
	 */
	private function template()
	{
		$this->view->version = $this->version;
		$this->view->steps = $this->steps;
		$this->view->rewrite = $_GET["rewrite"];
		$this->view->name = $this->steps[$_GET["rewrite"]];

		include_once(ABSOLUTE_PATH.'adm/themes/install/install.view.php');
	}

	/**
	 * Подключение шаблона контентной области
	 *
	 * @return void
	 */
	private function view($name)
	{
		include_once(ABSOLUTE_PATH.'adm/themes/install/install.view.'.$name.'.php');
	}

	/**
	 * Обработка ошибок
	 *
	 * @param string $error текст ошибки
	 * @return void
	 */
	private function error($error)
	{
		if(preg_match('/step([0-9]+)$/', $_GET["rewrite"], $m))
		{
			$step = intval($m[1]) - 1;
		}
		echo '<script>alert("'.$error.'"); document.location.href="'.BASE_PATH.'installation/'.($step > 1 ? 'step'.$step.'/' : '').'";</script>';
		exit;
	}
}

$install = new Installation($diafan);
$install->init();
exit;
