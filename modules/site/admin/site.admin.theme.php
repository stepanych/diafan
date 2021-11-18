<?php
/**
 * Редактирование настроек шаблона сайта
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2020 OOO «Диафан» (http://www.diafan.ru/)
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
 * Site_admin_theme
 */
class Site_admin_theme extends Frame_admin
{
	/**
	 * @var array поля  для редактирования
	 */
	public $variables = array (
		'base' => array (
			'edit' => array(
				'type' => 'function',
			),
		)
	);

	/**
	 * @var array настройки модуля
	 */
	public $config = array (
		'only_edit', // модуль состоит только из формы редактирования
	);

	/**
	 * Задает значения полей для формы
	 *
	 * @return array
	 */
	public function get_values()
	{
		return array('edit' => 'true');
	}


	/**
	 * Выводит контент модуля
	 * @return void
	 */
	public function edit_variable_edit()
	{
		echo '<p>'.$this->diafan->_('Параметры настроек шаблона задаются веб-мастером при создании сайта');
		echo ((defined("MOD_DEVELOPER") && MOD_DEVELOPER) ? $this->diafan->_(' в файле').' <b>'.BASE_PATH.Custom::path('modules/site/admin/site.admin.theme_custom.php').'</b>':'');
		echo $this->diafan->_(' и выводятся с помощью шаблонного тега %s. <a href="https://www.diafan.ru/dokument/full-manual/sysmodules/site/#Nastroyki-shablona" target="_blank">Документация по  настройкам шаблона.</a>', '<code><span style="color: #000000"><span style="color: #0000BB">&lt;insert</span> <span style="color: #007700">name=</span><span style="color: #DD0000">&quot;show_theme&quot;</span> <span style="color: #007700">module=</span><span style="color: #DD0000">&quot;site&quot;</span> <span style="color: #007700">tag=</span><span style="color: #DD0000">&quot;...&quot;</span><span style="color: #0000BB">&gt;</span></span></code>').'</p>';
		
		
		$old = DB::query_fetch_key_array("SELECT * FROM {site_theme}", "name");
		Custom::inc('modules/site/admin/site.admin.theme_custom.php');
		
		$config = new Site_admin_theme_custom($this->diafan);
		
		foreach ($config->variables as $name => $row)
		{
			if(! empty($old[$name]))
			{
				foreach($old[$name] as $i => $o)
				{
					$old[$name][$i]["value"] = (! empty($row["multilang"]) ? $o["value"._LANG] : $o["value".$this->diafan->_languages->site]);
				}
			}
			$value = (! empty($old[$name]) ? $old[$name][0]["value"] : '');
			$values = (! empty($old[$name]) ? $this->diafan->array_column($old[$name][0], "value") : '');
			
			$help = (! empty($row["help"]) ? $this->diafan->help($row["help"]) : '').(
			(defined("MOD_DEVELOPER") && MOD_DEVELOPER AND $row["type"]!='title')?
			'<br>
			<code><span style="color: #000000"><span style="color: #0000BB">&lt;insert</span> <span style="color: #007700">name=</span><span style="color: #DD0000">&quot;show_theme&quot;</span> <span style="color: #007700">module=</span><span style="color: #DD0000">&quot;site&quot;</span> <span style="color: #007700">tag=</span><span style="color: #DD0000">&quot;'.$name.'&quot;</span><span style="color: #0000BB">&gt;</span></span></code>':'');
			$multilang = (! empty($row["multilang"]) ? true : false);
			if (is_callable(array(&$config, "edit_variable_".$name)))
			{
				call_user_func_array(array(&$config, "edit_variable_".$name), array($values, $old));
			}
			else
			{
				switch($row["type"])
				{
					case 'title':
						$this->diafan->show_table_tr_title("theme_".$name, $row["name"], $help);
						break;
	
					case 'text':
					case 'url':
						$this->diafan->show_table_tr_text("theme_".$name, $row["name"], $value, $help);
						break;
	
					case 'textarea':
						$this->diafan->show_table_tr_textarea("theme_".$name, $row["name"], $value, $help);
						break;
	
					case "editor":
						$value = $this->diafan->_tpl->htmleditor($value);
						$this->diafan->show_table_tr_editor("theme_".$name, $row["name"], $value, $help);
						break;
	
					case 'email':
						$this->diafan->show_table_tr_email("theme_".$name, $row["name"], $value, $help);
						break;
	
					case 'phone':
						$this->diafan->show_table_tr_phone("theme_".$name, $row["name"], $value, $help);
						break;
	
					case 'date':
						$value = (! empty($value) ? $this->diafan->unixdate($this->diafan->formate_from_date($value)) : '');
						$this->diafan->show_table_tr_date("theme_".$name, $row["name"], $value, $help);
						break;
	
					case 'datetime':
						$value = (! empty($value) ? $this->diafan->unixdate($this->diafan->formate_from_datetime($value)) : '');
						$this->diafan->show_table_tr_datetime("theme_".$name, $row["name"], $value, $help);
						break;
	
					case 'numtext':
						$this->diafan->show_table_tr_numtext("theme_".$name, $row["name"], $value, $help);
						break;
	
					case 'floattext':
						$this->diafan->show_table_tr_floattext("theme_".$name, $row["name"], $value, $help);
						break;
	
					case 'checkbox':
						$this->diafan->show_table_tr_checkbox("theme_".$name, $row["name"], $value, $help);
						break;
	
					case 'radio':
						$opts = array(array('name' => $this->diafan->_('Нет'), 'id' => ''));
						if(! empty($options[$name]))
						{
							$opts = array_merge($opts, $options[$name]);
						}
						$this->diafan->show_table_tr_radio("theme_".$name, $row["name"], $value, $help, false, $opts);
						break;
	
					case 'select':
						$opts = array(array('name' => $this->diafan->_('Нет'), 'id' => ''));
						if(! empty($options[$name]))
						{
							$opts = array_merge($opts, $options[$name]);
						}
						$this->diafan->show_table_tr_select("theme_".$name, $row["name"], $value, $help, false, $opts);
						break;
	
					case 'multiple':
						$this->diafan->show_table_tr_multiple("theme_".$name, $row["name"], $values, $help, false, (! empty($options[$name]) ? $options[$name] : array()));
						break;
	
					case 'image':
						echo '
						<div class="unit">
							<div class="infofield">
								'.$row["name"].$help.'
							</div>';
						if ($value && file_exists(ABSOLUTE_PATH.USERFILES.'/site/theme/'.$value))
						{
							echo '<img src="'.BASE_PATH.USERFILES.'/site/theme/'.$value.'?'.rand(0, 99).'" style="max-width:100%">'
							.'<input type="checkbox" name="delete_'.$name.'" id="input_delete_'.$name.'" value="1"> <label for="input_delete_'.$name.'">'.$this->diafan->_('Удалить текущее изображение')
							.'</label>';
						}
						echo '
							<input type="file" name="theme_'.$name.'" class="file">
						</div>';
						break;
				}
			}
		}
	}
	
	/**
	 * Сохраняет настройки шаблона
	 *
	 * @return boolean
	 */
	public function save()
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->diafan->redirect(URL);
			return false;
		}

		//проверка прав на сохранение
		if (! $this->diafan->_users->roles('edit', 'site/theme'))
		{
			$this->diafan->redirect(URL);
			return false;
		}
		$old = DB::query_fetch_key_array("SELECT * FROM {site_theme}", "name");
		
		$this->diafan->set_one_shot('<div class="ok">'.$this->diafan->_('Изменения сохранены!').'</div>');
		
		Custom::inc('modules/site/admin/site.admin.theme_custom.php');
		
		$config = new Site_admin_theme_custom($this->diafan);
		foreach ($config->variables as $name => $row)
		{
			if (is_callable(array(&$config, "save_variable_".$name)))
			{
				$value = call_user_func_array(array(&$config, "save_variable_".$name), array($old));
			}
			else
			{
				$multilang = (! empty($row["multilang"]) ? _LANG : $this->diafan->_languages->site);
				$value = (! empty($_POST["theme_".$name]) ? $_POST["theme_".$name] : false);
				switch($row["type"])
				{
					case 'text':
					case 'url':
					case 'radio':
					case 'select':
					case 'multiple':
					case 'email':
					case 'phone':
						$mask = '%h';
						break;
	
					case 'textarea':
						$mask = '%s';
						break;
	
					case "editor":
						$mask = '%s';
						$value = $this->diafan->save_field_editor("theme_".$name);
						break;
	
					case 'email':
						$mask = '%h';
						break;
	
					case 'phone':
						$mask = '%h';
						break;
	
					case 'numtext':
					case 'checkbox':
						$mask = '%d';
						break;
					
					case 'date':
					case 'datetime':
						$value = $this->diafan->unixdate($value);
						break;
	
					case 'floattext':
						$mask = '%f';
						$value = str_replace(',', '.', $value);
						break;
	
					case 'image':
						$mask = '%h';
						$name_file = $name.(! empty($row["multilang"]) ? _LANG : '');
						if (! empty($old[$name]) && ! empty($_POST["delete_".$name]))
						{
							foreach($old[$name] as $v)
							{
								if($v["value".$multilang])
								{
									File::delete_file(USERFILES.'/site/theme/'.str_replace(array('\\','/'), '', $v["value".$multilang]));
								}
							}
						}
						$value = (! empty($old[$name][0]["value".$multilang]) && empty($_POST["delete_".$name]) ? $old[$name][0]["value".$multilang] : false);
						if (isset($_FILES["theme_".$name]) && is_array($_FILES["theme_".$name]) && $_FILES["theme_".$name]['name'] != '')
						{
							$tmp_name = 'theme'.rand(0, 99999);
							File::copy_file($_FILES["theme_".$name]['tmp_name'], 'tmp/'.$tmp_name);
							
							$tmp_name = 'tmp/'.$tmp_name;
							try
							{
								$info = @getimagesize(ABSOLUTE_PATH.$tmp_name);
								if ($info == false)
								{
									$this->diafan->set_one_shot('<div class="error">'.$this->diafan->_('Неверный формат файла. Изображения загружаются только в форматах  GIF, JPEG, PNG.').'</div>');
									throw new Exception('');
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
									$this->diafan->set_one_shot('<div class="error">'.$this->diafan->_('Неверный формат файла. Изображения загружаются только в форматах  GIF, JPEG, PNG.').'</div>');
									throw new Exception('');
								}
								$extension = $mimes[$info['mime']];
								File::create_dir(USERFILES.'/theme');
								File::upload_file(ABSOLUTE_PATH.$tmp_name, USERFILES.'/site/theme/'.$name_file.'.'.$extension);
								$value = $name_file.'.'.$extension;
							}
							catch(Exception $e)
							{
								File::delete_file($tmp_name);
							}
						}
						break;
				}
			}
			if($value)
			{
				if(is_array($value))
				{
					if($row["multilang"] && ! empty($old[$name]))
					{
						foreach($old[$name] as $o)
						{
							if(! empty($o["value"._LANG]))
							{
								DB::query("DELETE FROM {site_theme} WHERE id=%d", $o["id"]);
							}
						}
					}
					foreach($value as $v)
					{
						if($v)
						{
							DB::query("INSERT INTO {site_theme} (name, value".$multilang.", type) VALUES ('%h', '".$mask."', '%h')", $name, $v, $row["type"]);
						}
					}
				}
				else
				{
					if(! empty($old[$name]))
					{
						DB::query("UPDATE {site_theme} SET value".$multilang."='".$mask."', type='%h' WHERE id=%d", $value, $row["type"], $old[$name][0]["id"]);
						if(count($old[$name]) > 1)
						{
							DB::query("DELETE FROM {site_theme} WHERE id<>%d AND name='%h'", $old[$name][0]["id"], $name);
						}
					}
					else
					{
						DB::query("INSERT INTO {site_theme} (name, value".$multilang.", type) VALUES ('%h', '".$mask."', '%h')", $name, $value, $row["type"]);
					}
				}
			}
			elseif(! empty($old[$name]))
			{
				if(! empty($row["multilang"]))
				{
					DB::query("UPDATE {site_theme} SET value".$multilang."='' WHERE name='%h'", $name);
					$query_values = array();
					foreach($this->diafan->_languages->all as $r)
					{
						$query_values[] = "AND value".$r["id"]."=''";
					}
					DB::query("DELETE FROM {site_theme} WHERE name='%h' ".implode(" ", $query_values), $name);
				}
				else
				{
					DB::query("DELETE FROM {site_theme} WHERE name='%h'", $name);
				}
			}
		}
		$this->diafan->redirect(URL);
		return true;
	}
}
