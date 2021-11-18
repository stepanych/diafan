<?php
/**
 * Точки возврата
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
 * Update_admin
 */
class Update_admin extends Frame_admin
{
	/**
	 * @var string таблица в базе данных
	 */
	public $table = 'update_return';

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'name' => array(
				'type' => 'text',
				'name' => 'Название',
				'help' => 'Пример: «Установка», «Обновление».',
				'no_save' => true,
			),
			'created' => array(
				'type' => 'datetime',
				'name' => 'Дата',
				'help' => 'Вводится в формате дд.мм.гггг чч:мм.',
				'no_save' => true,
			),
			'text' => array(
				'type' => 'textarea',
				'name' => 'Примечание',
				'no_save' => true,
			),
			'files' => array(
				'type' => 'function',
				'name' => 'Файлы',
				'help' => 'Список файлов, измененных в данной точке.',
				'no_save' => true,
			)
		),
	);

	/**
	 * @var array поля в списка элементов
	 */
	public $variables_list = array (
		'checkbox' => '',
		'created' => array(
			'name' => 'Дата и время',
			'type' => 'datetime',
			'sql' => true,
			'no_important' => true,
		),
		'name' => array(
			'name' => 'Название'
		),
		'version' => array(
			'name' => 'Версия',
			'sql' => true,
			'type' => 'text',
			'no_important' => true,
		),
		'text' => array(
			'name' => 'Примечание',
			'sql' => true,
			'type' => 'text',
			'no_important' => true,
		),
		'current' => array(
			'sql' => true,
		),
		'actions' => array(
			'del' => true,
		),
	);

	/**
	 * Выводит контент модуля
	 * @return void
	 */
	public function show()
	{
		if(_LANG != $this->diafan->_languages->admin)
		{
			$this->diafan->redirect(BASE_PATH.ADMIN_FOLDER.'/update/');
		}
		if(! class_exists('ZipArchive'))
		{
			echo '<div class="error">'.$this->diafan->_('Не доступно PHP-расширение ZipArchive. Обратитесь в техническую поддержку хостинга.').'</div>';
		}
		echo '<span class="btn btn_small btn_checkrf" id="update">
			<span class="fa fa-refresh"></span>
			'.$this->diafan->_('Проверить обновления').'
		</span>';
		if(IS_DEMO)
		{
			echo ' ('.$this->diafan->_('не доступно в демонстрационном режиме').')';
		}

		echo '<div class="head-box head-box_warning">
<i class="fa fa-warning"></i>'.$this->diafan->_('Точка возврата создается при каждом обновлении, чтобы можно было вернуть некастомизированные файлы в предыдущее состояние. Первая точка возврата создается при установке DIAFAN.CMS. При удалении точки возврата файлы из этой точки присоединяются к предыдущей точке. Нельзя удалить последнюю текущую точку.').'</div>';
		$this->diafan->list_row();
	}

	/**
	 * Проверяет можно ли выполнять действия с текущим элементом строки
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param string $action действие
	 * @return boolean
	 */
	public function check_action($row, $action = '')
	{
		if(! isset($this->cache["current_id"]))
		{
			if($row["current"])
			{
				$this->cache["current_id"] = $row["id"];
			}
			else
			{
				$this->cache["current_id"] = DB::query_result("SELECT id FROM {update_return} WHERE current='1' LIMIT 1");
			}
		}
		// нельзя удалить текущую точку или еще не примененную
		if($row["current"] || $row["id"] > $this->cache["current_id"])
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Выводит кнопку "Сделать текущей" в списке
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_current($row, $var)
	{
		$this->cache["i"] = (! empty($this->cache["i"]) ? $this->cache["i"] + 1 : 1);
		if(! $row["current"])
		{
			$text = '<div class="item__btns"><span class="btn btn_blue btn_small action" action="current" module="update">';
			if($this->cache["i"] == 1 && $this->diafan->_route->page < 2)
			{
				$text .= $this->diafan->_('Применить все новые');
			}
			else
			{
				$text .= $this->diafan->_('Применить');
			}
			$text .= '</span></div>';
		}
		else
		{
			$text = '<div class="item__btns"><i class="fa fa-check-circle" style="color: #acd373"></i> '.$this->diafan->_('Текущее обновление').'</div>';
		}
		return $text;
	}

	/**
	 * Редактирование поля "Файлы"
	 *
	 * @return void
	 */
	public function edit_variable_files()
	{
		$files = $this->diafan->_update->get_files($this->diafan->id);

		if($files)
		{
			ksort($files);
			echo '
			<div class="unit" id="files">
				<b>'.$this->diafan->variable_name().':</b>'.$this->diafan->help().'<br>';
				foreach($files as $file => $content)
				{
					$mark = array();
					if(! in_array($file, array('upgrade.php', 'downgrade.php')))
					{
						if(! file_exists(ABSOLUTE_PATH.$file) || $this->diafan->_custom->is_diff(file_get_contents(ABSOLUTE_PATH.$file), $content))
						{
							$mark[] = $this->diafan->_('содержимое отличается от текущего файла');
						}
						if(Custom::path($file) != $file)
						{
							$mark[] = $this->diafan->_('файл заменен из темы');
						}
					}
					if($mark)
					{
						echo '<b>';
					}
					echo $file;
					if($mark)
					{
						echo '</b>';
						echo ' – '.implode(', ', $mark);
					}
					echo '<br>';
				}
				echo '
			</div>';
		}
	}

	/**
	 * Удаление точки
	 *
	 * @return void
	 */
	public function del()
	{
		// Прошел ли пользователь проверку идентификационного хэша
		if (! $this->diafan->_users->checked)
		{
			$this->diafan->redirect(URL);
			return;
		}

		if (! $this->diafan->_users->roles('del', $this->diafan->_admin->rewrite))
		{
			$this->diafan->redirect(URL);
		}

		if (! empty($_POST["id"]))
		{
			$ids = array($_POST["id"]);
		}
		else
		{
			$ids = $_POST["ids"];
		}
		if(! empty($ids))
		{
			$ids = DB::query_fetch_value("SELECT id FROM {update_return} WHERE id IN (%s) ORDER BY id ASC", preg_replace('/[^0-9,+]/', '', implode(',', $ids)), "id");
		}
		if(empty($ids))
		{
			$this->diafan->redirect(URL);
		}

		if(DB::query_result("SELECT id FROM {update_return} WHERE current='1' AND id IN (%s) LIMIT 1", implode(',', $ids)))
		{
			// throw new Exception('Нельзя удалить текущую точку.');
			$this->diafan->set_one_shot('<div class="error">'.$this->diafan->_('Нельзя удалить текущую точку.').'</div>');
			$this->diafan->redirect(URL);
			return;
		}
		if(DB::query_result("SELECT id FROM {update_return} WHERE `hash`='' AND id<>1 AND id IN (%s) LIMIT 1", implode(',', $ids)))
		{
			// throw new Exception('Нельзя удалить тестовую точку.');
			$this->diafan->set_one_shot('<div class="error">'.$this->diafan->_('Нельзя удалить тестовую точку.').'</div>');
			$this->diafan->redirect(URL);
			return;
		}

		$error = false;
		foreach ($ids as $id)
		{
			if($id && ! file_exists(ABSOLUTE_PATH."return/".$id.".zip"))
			{
				if(! $this->diafan->_update->recover_return($id))
				{
					$error = true;
					$this->diafan->set_one_shot('<div class="error">'.$this->diafan->_('Не найдена точка № %s', $id).'</div>');
					$id = false;
				}
			}
			// ищет следующую точку
			$next_id = false;
			if($row = DB::query_fetch_array("SELECT id, `hash` FROM {update_return} WHERE id>%d ORDER BY id ASC LIMIT 1", $id))
			{
				if($row["hash"] && $row["id"] && (file_exists(ABSOLUTE_PATH."return/".$row["id"].".zip") || $this->diafan->_update->recover_return($row["id"])))
				{
					$next_id = $row["id"];
				}
				else
				{
					$error = true;
					if(! $row["hash"] && $row["id"])
					{
						$this->diafan->set_one_shot('<div class="error">'.$this->diafan->_('Нельзя объединить текущую точку № %s с тестовой точкой № %s', $id, $row["id"]).'</div>');
					}
					else
					{
						$this->diafan->set_one_shot('<div class="error">'.$this->diafan->_('Не найдена точка слияния%s для точки № %s', ($row["id"] ? ' № '.$row["id"] : ''), $id).'</div>');
					}
				}
			}
			// если точка найдена, присоединяет файлы удаляемой точки к следующей точке, пропуская обновленные в следующей точке файлы
			if($id && $next_id)
			{
				if(! $this->diafan->_update->merge_return("return/".$next_id.".zip", "return/".$id.".zip"))
				{
					$error = true;
					$this->diafan->set_one_shot('<div class="error">'.$this->diafan->_('Ошибка при слиянии точек №№ %s и %s', $id, $next_id).'</div>');
					continue;
				}
				chmod(ABSOLUTE_PATH."return/".$next_id.".zip", 0777);
				File::delete_file('return/'.$id.'.zip');
				DB::query("DELETE FROM {update_return} WHERE id=%d", $id);
			}
		}
		if(! $error) $this->diafan->set_one_shot('<div class="ok">'.$this->diafan->_('Изменения сохранены!').'</div>');
		$this->diafan->redirect(URL);
	}
}
