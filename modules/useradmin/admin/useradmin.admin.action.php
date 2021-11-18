<?php
/**
 * Обработка запроса на редактирование данных из пользовательской части
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
 * Useradmin_admin_action
 */
class Useradmin_admin_action extends Action_admin
{
	/**
	 * Вызывает обработку POST-запросов
	 *
	 * @return void
	 */
	public function init()
	{
		if (! empty($_POST['module']) && $_POST['module'] == 'useradmin')
		{
			if (! empty($_POST["type"]) && $_POST["type"] == "image")
			{
				if (empty($_POST["path"]))
				{
					$this->result["errors"][0] = 'ERROR';
					return;
				}
				if(! preg_match('/'.preg_quote(USERFILES).'\/([^\/]+)(\/)*/', $_POST["path"], $m))
				{
					$this->result["errors"][0] = 'ERROR';
					return false;
				}
				$ps = explode('/', $_POST["path"]);
				foreach($ps as $p)
				{
					if($ps == '..' || $p == '.' || empty(trim($p)))
					{
						$this->result["errors"][0] = 'ERROR';
						return false;
					}
				}
				if(in_array($m[1], $this->diafan->installed_modules) && ! $this->diafan->_users->roles('edit', $m[1]))
				{
					$this->result["errors"][0] = 'ERROR';
					return false;
				}
				if (isset($_FILES["file"]) && is_array($_FILES["file"]) && $_FILES["file"]['name'] != '')
				{
					$tmp_name = 'useradmin'.rand(0, 99999);
					File::copy_file($_FILES["file"]['tmp_name'], 'tmp/'.$tmp_name);
					
					$tmp_name = 'tmp/'.$tmp_name;
					$info = @getimagesize(ABSOLUTE_PATH.$tmp_name);
					if ($info == false)
					{
						$this->result["errors"][0] =$this->diafan->_('Неверный формат файла. Изображения загружаются только в форматах  GIF, JPEG, PNG.');
						unlink(ABSOLUTE_PATH.$tmp_name);
						return;
					}
					$mimes = array(
						'image/gif' => 'gif',
						'image/jpeg' => 'jpg',
						'image/png' => 'png',
						'image/pjpeg' => 'jpg',
						'image/x-png'=> 'png'
					);
					if(empty($info['mime']) || ! in_array($info['mime'], array_keys($mimes)))
					{
						$this->result["errors"][0] =$this->diafan->_('Неверный формат файла. Изображения загружаются только в форматах  GIF, JPEG, PNG.');
						unlink(ABSOLUTE_PATH.$tmp_name);
						return;
					}
					File::upload_file(ABSOLUTE_PATH.$tmp_name, $_POST["path"]);
				}
				$this->result["result"] = 'success';
				return;
			}
			if (empty($_POST["module_name"]) || empty($_POST["name"]))
			{
				$this->result["errors"][0] = 'ERROR';
				return;
			}
			if ($_POST["module_name"] != "languages" && empty($_POST["element_id"]))
			{
				$this->result["errors"][0] = 'ERROR';
				return;
			}
			if (! $this->diafan->_users->checked)
			{
				$this->result["errors"][0] = 'ERROR';
				return;
			}
			list($module_name) = explode('_', $_POST["module_name"]);
			if ($this->diafan->_users->useradmin != 1
			    || ! $this->diafan->_users->roles('edit', $module_name))
			{
				$this->result["errors"][0] = 'ERROR';
				return;
			}
			Custom::inc('includes/model.php');
			if (! empty($_POST["is_lang"]))
			{
				$lang_id = $this->diafan->filter($_POST, "int", "lang_id");
				$name = trim(urldecode($_POST["name"]));
				$lang_module_name = $this->diafan->filter($_POST, "string", "lang_module_name");
				if($id = DB::query_result("SELECT id FROM {languages_translate} WHERE `text`='%s' AND module_name='%h' AND type='site' AND lang_id=%d LIMIT 1", $name, $lang_module_name, $lang_id))
				{
					DB::query("UPDATE {languages_translate} SET text_translate='%s' WHERE id=%d", $_POST["value"], $id);
				}
				else
				{
					DB::query("INSERT INTO {languages_translate} (`text`, text_translate, module_name, lang_id, type) VALUES ('%s', '%s', '%h', %d, 'site')", $name, $_POST["value"], $lang_module_name, $lang_id);
				}
			}
			else
			{
				$type = ! empty($_POST["type"]) ? $_POST["type"] : $this->diafan->_useradmin->type($_POST["name"]);
				switch($type)
				{
					case 'editor':
						if(! empty($_POST["typograf"]))
						{
							Custom::inc("plugins/remotetypograf.php");

							$remoteTypograf = new RemoteTypograf();

							$remoteTypograf->htmlEntities();
							$remoteTypograf->br (false);
							$remoteTypograf->p (true);
							$remoteTypograf->nobr (3);
							$remoteTypograf->quotA ('laquo raquo');
							$remoteTypograf->quotB ('bdquo ldquo');

							$_POST["value"] = $remoteTypograf->processText ($_POST["value"]);
						}
						// ссылки заменяем на id
						$_POST["value"] = $this->diafan->_route->replace_link_to_id($_POST["value"]);

						// копирование внешних изображений
						if ($this->diafan->_users->copy_files && ! IS_DEMO)
						{
							if(preg_match_all('/\<img[^\>+]src=\"http*:\/\/([^\"]+)\"/', $_POST["value"], $m))
							{
								foreach ($m[1] as $i => $src)
								{
									$src = 'http://'.$src;
									$url = parse_url($src);
									if ($url["host"] != getenv("HTTP_HOST"))
									{
										$extension = substr(strrchr($src, '.'), 1);
										$name = md5($src).'.'.$extension;
										try
										{
											File::copy_file($src, USERFILES.'/upload/'.$name);
											$err_copy_file = false;
										}
										catch(Exception $e)
										{
											$err_copy_file = true;
										}
										if(! $err_copy_file)
										{
											$_POST["value"] = str_replace('src="'.$src.'"', 'src="BASE_PATH'.USERFILES.'/upload/'.$name.'"', $_POST["value"]);
										}
									}
								}
							}
						}
						$mask = "'%s'";
						break;
					case 'date':
						$_POST["value"] = $this->diafan->unixdate($_POST["value"]);
						$mask = "'%d'";
						break;
					case 'text':
					case 'textarea':
						$mask = "'%h'";
						break;
					case 'numtext':
						$mask = "'%d'";
						break;
				}
				$lang_id = $this->diafan->filter($_POST, "int", "lang_id");

				DB::query("UPDATE {%h} SET `%h".($lang_id ? $lang_id : '')."`=".$mask." WHERE id=%d",$_POST["module_name"],  $_POST["name"], $_POST["value"], $_POST["element_id"]);
				$module = explode('_', $_POST["module_name"]);
				if($_POST["module_name"] == 'shop_price')
				{
					$good_id = DB::query_result("SELECT good_id FROM {shop_price} WHERE id=%d", $_POST["element_id"]);
					$this->diafan->_shop->price_calc($good_id);
				}
				$this->diafan->_cache->delete("", $module[0]);
			}

			$this->result["result"] = 'success';
		}
	}
}
