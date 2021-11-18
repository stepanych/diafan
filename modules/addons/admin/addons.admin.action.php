<?php
/**
 * Обработка POST-запросов в административной части модуля
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
 * Addons_admin_action
 */
class Addons_admin_action extends Action_admin
{

	/**
	 * Вызывает обработку Ajax-запросов
	 *
	 * @return void
	 */
	public function init()
	{
		if (! empty($_POST["action"]))
		{
			switch($_POST["action"])
			{
				case 'check_update':
					$this->check_update();
					break;

				case 'delete_return':
					$this->delete_return();
					break;

				case 'group_action':
				case 'group_no_action':
				case 'group_addon_update':
					$this->group_option();
					break;

				case 'buy':
				case 'subscription':
					$this->buy();
					break;
				case 'no_subscription':
					$this->no_subscription();
					break;

				case 'more':
					$this->more();
					break;
			}
		}
	}

	/**
	 * Удаляет резервные копии обновленных дополнений
	 *
	 * @return void
	 */
	private function delete_return()
	{
		if(is_dir(ABSOLUTE_PATH.$this->diafan->_addons->return_path))
		{
			File::rm($this->diafan->_addons->return_path);
		}
		$message = $this->diafan->_('Резервные копии обновленных дополнений удалены.');
		$this->result["errors"]["message"] = $message;
		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}

	/**
	 * Проверить обновления для дополнений
	 *
	 * @return void
	 */
	private function check_update()
	{
		$this->diafan->_addons->update(true);
		$count = DB::query_result("SELECT COUNT(*) FROM {%s} WHERE custom_timeedit>0 AND timeedit<>custom_timeedit", $this->diafan->table);
		$message = '';
		if($count)
		{
			$message = $this->diafan->_('Доступно обновление для дополнений: %d.', $count);
		}
		else
		{
			$message = $this->diafan->_('Доступных обновлений для дополнений пока нет. Попробуйте проверить чуть позже.');
		}
		$this->result["errors"]["message"] = $message;
		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}

	/**
	 * Групповая операция "Установка дополнения", "Отключение дополнения" и др.
	 *
	 * @return void
	 */
	private function group_option()
	{
		if(! empty($_POST["ids"]))
		{
			$ids = array();
			foreach ($_POST["ids"] as $id)
			{
				$id = intval($id);
				if($id)
				{
					$ids[] = $id;
				}
			}
		}
		elseif(! empty($_POST["id"]))
		{
			$ids = array(intval($_POST["id"]));
		}
		if(! empty($ids))
		{
			switch ($_POST["action"])
			{
				case 'group_action':
					$this->group_action($ids);
					break;

				case 'group_no_action':
					$this->group_no_action($ids);
					break;

				case 'group_addon_update':
					$this->group_addon_update($ids);
					break;
			}
		}
	}

	/**
	 * Активация элемента
	 *
	 * @param array $ids идентификаторы дополнений
	 * @return void
	 */
	public function group_action($ids)
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->result["redirect"] = URL;
			return;
		}

		//проверка прав пользователя на активацию/блокирование
		if (! $this->diafan->_users->roles('edit', $this->diafan->_admin->rewrite))
		{
			$this->result["redirect"] = URL;
			return;
		}

		$question = true; // $question = ! empty($_POST["question"]) ? true : false;
		$result = $this->diafan->_addons->install($ids, $question);

		if($result === true)
		{
			$this->diafan->set_one_shot(
				'<div class="ok">'
				.(count($ids) > 1
					? $this->diafan->_('Дополнения установлены.')
					: $this->diafan->_('Дополнение установлено.'))
				.'</div>'
			);
			$this->result["redirect"] = URL.$this->diafan->get_nav;
			return;
		}
		$message = is_array($result) ? implode("<br>", $result) : (is_string($result) ? $result : '');
		$this->diafan->set_one_shot(
			'<div class="error">'
			.(count($ids) > 1
				? $this->diafan->_('Некоторые дополнения не установлены.')
				: $this->diafan->_('Дополнение не установлено.'))
			.($message ? "<br>".$message : '')
			.'</div>'
		);
		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}

	/**
	 * Блокировка элемента
	 *
	 * @param array $ids идентификаторы дополнений
	 * @return void
	 */
	public function group_no_action($ids)
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->result["redirect"] = URL;
			return;
		}

		//проверка прав пользователя на активацию/блокирование
		if (! $this->diafan->_users->roles('edit', $this->diafan->_admin->rewrite))
		{
			$this->result["redirect"] = URL;
			return;
		}

		$question = true; // $question = ! empty($_POST["question"]) ? true : false;
		$this->diafan->_addons->uninstall($ids, $question);

		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}

	/**
	 * Обновляет элемент
	 *
	 * @param array $ids идентификаторы дополнений
	 * @return void
	 */
	public function group_addon_update($ids)
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->result["redirect"] = URL;
			return;
		}

		//проверка прав пользователя на активацию/блокирование
		if (! $this->diafan->_users->roles('edit', $this->diafan->_admin->rewrite))
		{
			$this->result["redirect"] = URL;
			return;
		}

		$result = $this->diafan->_addons->reload($ids);

		if($result === true)
		{
			$this->diafan->set_one_shot(
				'<div class="ok">'
				.(count($ids) > 1
					? $this->diafan->_('Дополнения обновлены.')
					: $this->diafan->_('Дополнение обновлено.'))
				.'</div>'
			);
			$this->result["redirect"] = URL.$this->diafan->get_nav;
			return;
		}
		$message = is_array($result) ? implode("<br>", $result) : (is_string($result) ? $result : '');
		$this->diafan->set_one_shot(
			'<div class="error">'
			.(count($ids) > 1
				? $this->diafan->_('Некоторые дополнения не обновлены.')
				: $this->diafan->_('Дополнение не обновлено.'))
			.($message ? "<br>".$message : '')
			.'</div>'
		);
		$this->result["redirect"] = URL.$this->diafan->get_nav;
	}

	/**
	 * Покупка дополнения
	 *
	 * @return void
	 */
	private function buy()
	{
		if(! in_array($_POST["action"], array('buy', 'subscription')))
		{
			return;
		}
		$id = DB::query_result("SELECT addon_id FROM {addons} WHERE id=%d LIMIT 1", $this->diafan->filter($_POST, "int", "id"));
		$subscription = $_POST["action"] == 'subscription';
		$result = $this->diafan->_addons->buy($id, $subscription);
		if($result === true)
		{
			$this->diafan->set_one_shot(
				'<div class="ok">'
				.$this->diafan->_('Спасибо за Ваш заказ!')."<br>"
					.($_POST["action"] == 'subscription'
						? $this->diafan->_('Подписка на дополнение оформлена.')
						: $this->diafan->_('Покупка дополнения оформлена.'))
				.'</div>'
			);
			if($_POST["action"] == 'subscription')
			{
				$fields = ", IFNULL(c.id, 0) as `custom.id`, IFNULL(c.name, '') as `custom.name`";
				$join = " LEFT JOIN {custom} AS c ON c.id=e.custom_id";
				$row = DB::query_fetch_array(
					"SELECT e.*".$fields." FROM {addons} as e".$join." WHERE e.id=%d LIMIT 1",
					$this->diafan->filter($_POST, "int", "id")
				);
				if($row && ! empty($row["id"]) && empty($row["custom.id"]))
				{
					$question = true; // $question = ! empty($_POST["question"]) ? true : false;
					$rslt = $this->diafan->_addons->install($row["id"], $question);
					if($rslt === true)
					{
						$this->diafan->set_one_shot(
							'<div class="ok">'
							.$this->diafan->_('Дополнение установлено.')
							.'</div>'
						);
					}
					else
					{
						$message = is_array($rslt) ? implode("<br>", $rslt) : (is_string($rslt) ? $rslt : '');
						$this->diafan->set_one_shot(
							'<div class="error">'
							.$this->diafan->_('Дополнение не установлено.')
							.($message ? "<br>".$message : '')
							.'</div>'
						);
					}
				}
			}
			// Удаляет кэш модуля
			// $this->diafan->_cache->delete("", $this->diafan->_admin->module);
			$this->diafan->_cache->delete("", 'addons');
			return;
		}
		$message = is_array($result) ? implode("\n", $result) : (is_string($result) ? $result : '');
		$this->diafan->set_one_shot(
			'<div class="error">'
			.$this->diafan->_('Заказ отменен.').($message ? "\n".$message : '')
			.'</div>'
		);
		$this->result["redirect"] = $this->diafan->_route->current_admin_link();
	}

	/**
	 * Отмена подписки на дополнение
	 *
	 * @return void
	 */
	private function no_subscription()
	{
		if(! in_array($_POST["action"], array('no_subscription')))
		{
			return;
		}
		$id = DB::query_result("SELECT addon_id FROM {addons} WHERE id=%d LIMIT 1", $this->diafan->filter($_POST, "int", "id"));
		$result = $this->diafan->_addons->no_subscription($id);
		if($result === true)
		{
			$this->diafan->set_one_shot(
				'<div class="ok">'
				.$this->diafan->_('Подписка на дополнение отменена.')
				.'</div>'
			);
			// Удаляет кэш модуля
			// $this->diafan->_cache->delete("", $this->diafan->_admin->module);
			$this->diafan->_cache->delete("", 'addons');
			return;
		}
		$message = is_array($result) ? implode("<br>", $result) : (is_string($result) ? $result : '');
		$this->diafan->set_one_shot(
			'<div class="error">'
			.$this->diafan->_('Отмена подписки на дополнение не выполнена.').($message ? "<br>".$message : '')
			.'</div>'
		);
		$this->result["redirect"] = $this->diafan->_route->current_admin_link();
	}

	/**
	 * Показывает ещё элементы в списке
	 *
	 * @return void
	 */
	private function more()
	{
		$module_contents = '';
		if($cat_name = DB::query_result("SELECT cat_name FROM {addons} WHERE id=%d", $this->diafan->filter($_POST, "int", "id")))
		{
			$polog = $this->diafan->filter($_POST, "int", "polog");
			$nastr = $this->diafan->filter($_POST, "int", "nastr");

			$action_object = $this->diafan->get_action_object();
			Custom::inc("adm/includes/show.php");
			$this->diafan->set_action_object(new Show_admin($this->diafan));
			$this->diafan->set_get_nav();
			if($this->diafan->_users->id)
			{
				ob_start();
				$this->diafan->polog = $polog;
				$this->diafan->nastr = $nastr;
				$this->diafan->where .= " AND cat_name='".$cat_name."'";
				$this->diafan->prepare_variables();

				$id = 0;
				$this->diafan->rows = $this->diafan->sql_query($id);
				$this->diafan->rows($this->diafan->rows);

				$module_contents = ob_get_contents();
				ob_end_clean();
			}
			$this->diafan->set_action_object($action_object);
		}

		$this->result["result"] = $module_contents;
	}

	/**
	 * Формирует SQL-запрос для списка элементов
	 *
	 * @param integer $polog номер первой строки
	 * @param integer $polog номер последний строки
	 * @return array
	 */
	private function sql_query($polog, $nastr)
	{
		Custom::inc('modules/addons/admin/addons.admin.php');
		$object = new Addons_admin($action_object);

		$fields = '';
		if($object->variables_list)
		{
			foreach ($object->variables_list as $name => $var)
			{
				if(empty($var["sql"]))
					continue;

				$fields .= ', e.'.($object->variable_multilang($name) ? '['.$name.']' : $name);
			}
		}

		if($cat_names = DB::query_fetch_value("SELECT DISTINCT cat_name FROM {addons} WHERE 1 ORDER BY sort DESC", "cat_name"))
		{
			$object->cat_names = array_flip($cat_names);
			$cat_name_ids = DB::query_fetch_value("SELECT id FROM {addons} WHERE custom_id<>0 OR buy='1' OR subscription>=%d ORDER BY sort DESC", $object->timemarker, "id");
			if(! empty($cat_name_ids)) $object->where .= " AND e.id NOT IN (".implode(',', $cat_name_ids).")";
		}

		$order = '';
		$themes = $object->sql_query_themes();
		$cat_names = ! empty($object->cat_names) ? array_keys($object->cat_names) : array();
		$order_field = '';
		if(! empty($cat_names))
		{
			foreach($cat_names as $key => $value) $cat_names[$key] = "'".$value."'";
			$order_field .= ", FIELD(e.cat_name, ".implode(", ", $cat_names).") ASC";
		}
		if(! empty($themes))
		{
			$order_field .= ", FIELD(c.name, ".implode(", ", $themes).") ASC";
		}
		$order = " ORDER BY "
			." act DESC".$order_field.", `custom.id` DESC, e.buy DESC, e.subscription DESC, e.auto_subscription DESC, e.sort DESC, e.addon_id DESC"
			.(! empty($order) ? ", ".$order : "");

		$rows = DB::query_fetch_all("SELECT e.id"
			.$fields
			.$object->fields . $object->sql_query_act()
			." FROM {".$object->table."} as e"
			.$object->join
			. " WHERE 1=1".( $object->where ? " ".$object->where : '' )
			." GROUP BY e.id"
			.$order
			." LIMIT %d, %d", $polog, $nastr);

		foreach($rows as $key => $row)
		{
			$modules = ! empty($row["custom.name"]) ? $this->diafan->_custom->get_modules($row["custom.name"]) : array();
			$rows[$key]["modules"] = '';
			foreach($modules as $module) $rows[$key]["modules"] .= (! empty($rows[$key]["modules"]) ? ', ' : '') . $module["name"];
		}

		unset($object);
		return $rows;
	}

	/**
	 * Формирует список элементов
	 *
	 * @return void
	 */
	private function list_row($rows)
	{
		Custom::inc('modules/addons/admin/addons.admin.php');
		$object = new Addons_admin($action_object);
		foreach ($rows as $row)
		{
			echo '<li class="item';
			if ($object->is_variable("readed") && ! $row["readed"])
			{
				echo ' item_no_readed';
			}
			if ($object->is_variable("no_buy") && $row["no_buy"])
			{
				echo ' item_no_buy';
			}
			if ($object->variable_list('actions', 'act') && ! $row['act'])
			{
				echo ' item_disable';
			}
			echo '" row_id="'.$row['id'].'"'
			.($object->variable_list('plus') ? ' parent_id="'.$row['parent_id'].'"' : '')
			.($object->variable_list('sort') ? ' sort_id="'.$row['sort'].'"' : '');

			$func = 'list_row_attr';
			$result = $object->$func($row);
			if ($result !== 'fail_function')
			{
				echo ' '.$result;
			}

			echo '>
		    <div class="item__in'.$object->list_row_class($row).'">';
			foreach($object->variables_list as $name => $var)
			{
				if(! is_array($var))
				{
					$var = array();
				}
				$var["class"] = ! empty($var["class"]) ? $var["class"] : '';
				$var["class"] .= $name != 'created' && ! empty($var["type"]) && ($var["type"] == 'datetime' || $var["type"] == 'date') ? (! empty($var["class"]) ? ' ' : '').'date' : '';
				$var["class"] .= ! empty($var['no_important']) ? (! empty($var["class"]) ? ' ' : '').'no_important' : '';
				$func = 'list_variable_'.preg_replace('/[^a-z_]+/', '', $name);
				$result = $object->$func($row, $var);
				if ($result !== 'fail_function')
				{
					echo $result;
				}
				elseif(! empty($var["type"]) && $var["type"] != 'none')
				{
					echo '<div'.(! empty($var["class"]) ? ' class="'.$var["class"].'"' : '').'>';
					if(! empty($var["fast_edit"]))
					{
						echo '
						<div class="item__field fast_edit">';
						switch($var["type"])
						{
							case 'text':
								echo '<i class="fa fa-check-circle"></i>
								<div class="item__field__cover"><span></span></div>
								<input type="text" row_id="'.$row['id'].'" name="'.$name.'" value="'.str_replace('"', '&quot;', $row[$name]).'">';
								break;

							case 'numtext':
								echo '<i class="fa fa-check-circle"></i>
								<div class="item__field__cover"><span></span></div>
								<input type="text" row_id="'.$row['id'].'" name="'.$name.'" value="'.$row[$name].'" class="number">';
								break;

							case 'floattext':
								echo '<i class="fa fa-check-circle"></i>
								<div class="item__field__cover"><span></span></div>
								<input type="text" class="number" row_id="'.$row['id'].'" name="'.$name.'" value="'.number_format($row[$name], 2, ',', '').'">';
								break;

							case 'editor':
							case 'textarea':
								echo ' <textarea name="'.$name.'" row_id="'.$row['id'].'" cols="40" rows="3">'.str_replace(array ( '<',
									'>', '"' ), array ( '&lt;', '&gt;', '&quot;' ), $row[$name]).'</textarea>';
								break;

							case 'datetime':
								if($name != 'created')
								{
									echo '<i class="fa fa-check-circle"></i>
									<div class="item__field__cover"><span></span></div>
									<input type="text" row_id="'.$row['id'].'" name="'.$name.'" value="'.date("d.m.Y H:i", $row[$name]).'" class="timecalendar" showTime="true">';
								}
								break;

							case 'date':
								if($name != 'created')
								{
									echo '<i class="fa fa-check-circle"></i>
									<div class="item__field__cover"><span></span></div>
									<input type="text" row_id="'.$row['id'].'" name="'.$name.'" value="'.date("d.m.Y H:i", $row[$name]).'" class="timecalendar" showTime="false">';
								}
								break;
						}
						echo '
							<div class="info-box success">'.$object->_('Сохранено!').'</div>
							<div class="info-box change">'.$object->_('Для сохранения нажмите Enter.').'</div>
						</div>';
					}
					else
					{
						switch($var["type"])
						{
							case 'editor':
							case 'text':
								echo (! empty($row[$name]) ? $object->short_text($row[$name]) : '');
								break;

							case 'select':
								if(! isset($var["select"]))
								{
									if(! empty($var["select_db"]))
									{
										$var["select"] = $object->get_select_from_db($var["select_db"]);
										if(! empty($var["select"]) && is_array($var["select"]) && ($list = $object->array_column($var["select"], "name"))) $var["select"] = $list;
									}
									else
									{
										$var["select"] = $object->variable($name, 'select');
										if(! $var["select"] && $object->variable($name, 'select_db'))
										{
											$var["select"] = $object->get_select_from_db($object->variable($name, 'select_db'));
											if(! empty($var["select"]) && is_array($var["select"]) && ($list = $object->array_column($var["select"], "name"))) $var["select"] = $list;
										}
									}
									$object->variable_list($name, 'select', $var["select"]);
								}
								if(! empty($var["select"][$row[$name]]))
								{
									echo $object->_($var["select"][$row[$name]]);
								}
								break;

							case 'numtext':
							case 'floattext':
							case 'string':
								echo (! empty($row[$name]) ? $row[$name] : '&nbsp;');
								break;

							case 'datetime':
								if($name != 'created')
								{
									echo (! empty($row[$name]) ? date("d.m.Y H:i", $row[$name]) : '&nbsp;');
								}
								break;

							case 'date':
								if($name != 'created')
								{
									echo (! empty($row[$name]) ? date("d.m.Y", $row[$name]) : '&nbsp;');
								}
								break;
						}
					}
					echo '</div>';
				}
			}

			echo '</div>';
			//выводит вложенные элементы
			// if ($object->variable_list('plus') && in_array($row["id"], $this->parent_parents))
			// {
			// 	$object->list_row($row["id"], false);
			// }
			echo  '</li>';
		}
	}
}
