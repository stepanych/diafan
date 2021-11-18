<?php
/**
 * Лог
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

class Log_admin extends Frame_admin
{
	/**
	 * @var object вспомогательный объект модуля
	 */
	private $log = null;

	/**
	 * @var array массив ошибок
	 */
	private $errors = array();

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'name' => array(
				'type' => 'function',
				'name' => 'Ошибка',
			),
		),
	);

	/**
	 * @var array поля в списка элементов
	 */
	public $variables_list = array (
		'checkbox' => '',
		'icon' => array(
			'name' => '',
			'type' => 'function',
		),
		'datetime' => array(
			'name' => 'Дата и время',
			'type' => 'datetime',
		),
		'name' => array(
			'name' => 'Ошибка',
		),
		'uri' => array(
			'name' => 'URI',
			'type' => 'string',
		),
		'actions' => array(
			'trash' => true,
		),
	);

	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(&$diafan)
	{
		parent::__construct($diafan);
		Custom::inc('modules/log/admin/log.admin.inc.php');
		$this->log = new Log_admin_inc($this->diafan);
		$this->errors = $this->log->errors();
	}

	/**
	 * Подготавливает конфигурацию модуля
   *
	 * @return void
	 */
	public function prepare_config()
	{

	}

	/**
	 * Формирует SQL-запрос для списка элементов
	 *
	 * @param integer $id родитель
	 * @return array
	 */
	public function sql_query($id)
	{
		return array_slice($this->errors, $this->diafan->polog, $this->diafan->nastr, true);
	}

	/**
	 * Формирует постраничную навигацию
	 *
	 * @return array
	 */
	public function prepare_paginator($id)
	{
		if (! $id || ! $this->diafan->variable_list('plus'))
		{
			$this->diafan->_paginator->navlink = ( $this->diafan->_admin->rewrite ? $this->diafan->_admin->rewrite.'/' : '' ).( $this->diafan->_route->site ? 'site'.$this->diafan->_route->site.'/' : '' ).( $this->diafan->_route->cat ? 'cat'.$this->diafan->_route->cat.'/' : '' );
			$this->enterlink = $this->diafan->_paginator->navlink.'parent%d/'.( $this->diafan->_paginator->page ? 'page'.$this->diafan->_paginator->page.'/' : '' ).'?';
			$this->diafan->_paginator->get_nav = $this->diafan->get_nav;
			$navlink = $this->diafan->_paginator->navlink.'parent%d/'.( $this->diafan->_paginator->page ? 'page'.$this->diafan->_paginator->page.'/' : '' ).( $this->diafan->get_nav ? $this->diafan->get_nav.'&' : '?' );
		}
		elseif ($this->diafan->variable_list('plus'))
		{
			$this->diafan->_paginator->page = ! empty($_GET["page".$id]) ? intval($_GET["page".$id]) : 0;
			$this->diafan->_paginator->urlpage = '?page'.$id.'=%d';
			$navlink = ( $this->diafan->_admin->rewrite ? $this->diafan->_admin->rewrite.'/' : '' ).( $this->diafan->_route->site ? 'site'.$this->diafan->_route->site.'/' : '' ).( $this->diafan->_route->cat ? 'cat'.$this->diafan->_route->cat.'/' : '' ). 'parent%d/';
			$this->diafan->_paginator->navlink = sprintf($navlink, $id);
		}

		$this->diafan->_paginator->nen = count($this->errors);

		$links = $this->diafan->_paginator->get();
		$this->diafan->polog = $this->diafan->_paginator->polog;
		$this->diafan->nastr = $this->diafan->_paginator->nastr;

		return $links;
	}

	/**
	 * Выводит список файлов
	 * @return void
	 */
	public function show()
	{
		if(defined('IS_DEMO') && IS_DEMO)
		{
			echo '<br /><div class="error">'.$this->diafan->_('Лог ошибок в демо-версии не доступен.').'</div>';
			return;
		}
		if($this->diafan->_users->id<>1)
		{
			echo '<br /><div class="error">'.$this->diafan->_('Нет доступа. Лог ошибок доступен только администратору, устанавливавшему DIAFAN.CMS').'</div>';
			return;
		}

		// if($this->diafan->_users->roles('edit', $this->diafan->_admin->rewrite))
		// {
		//
		// }

		if($this->diafan->_users->roles('del') && ! empty($this->errors))
		{
			echo '<p>
			<form action="'.URL.'" method="post">
			<input name="module" type="hidden" value="log">
			<input name="action" type="hidden" value="delete">
			<input name="check_hash_user" type="hidden" value="'.$this->diafan->_users->get_hash().'">
			<input type="button" class="log_clear button" value="'.$this->diafan->_('Очистить лог').'" confirm="'.$this->diafan->_('Вы действительно хотите удалить все ошибки из лога?').'">
			</form>
			</p>';
		}

		$this->diafan->list_row();

		if(empty($this->diafan->rows))
		{
			echo '<br /><div class="ok">'.$this->diafan->_('Лог ошибок пуст').'</div>';
			return;
		}
	}

	/**
	 * Выводит иконки в списке элементов
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_icon($row, $var)
	{
		return '<span class="item__icon"><i class="fa fa-exclamation-triangle"></i></span>';
	}

	/**
	 * Выводит URI в списке элементов
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_uri($row, $var)
	{
		return '<div'.(! empty($var["class"]) ? ' class="'.$var["class"].'"' : '').'>'
			.(! empty($row["uri"]) ? '<a href="'.$row["uri"].'">'.$row["uri"].'</a>' : '&nbsp;')
			.'</div>';
	}

	/**
	 * Задает значения полей для формы
	 *
	 * @return array
	 */
	public function get_values()
	{
		$id = $this->diafan->filter($this->diafan->id, "integer");
		$array = (! empty($this->errors[$id]) ? $this->errors[$id] : array());
		$this->diafan->attributes($array, 'id', 'group_id', 'datetime', 'uri', 'client', 'host', 'agent', 'referer', 'name', 'message', 'trace');
		return $array;
	}

	/**
	 * Выводит список файлов
	 * @return void
	 */
	public function edit()
	{
		if(defined('IS_DEMO') && IS_DEMO)
		{
			Custom::inc('includes/404.php');
		}
		if($this->diafan->_users->id<>1)
		{
			Custom::inc('includes/404.php');
		}
		if($this->diafan->is_new)
		{
			Custom::inc('includes/404.php');
		}
		if($this->diafan->_route->addnew) $id = 0;
		else $id = $this->diafan->_route->edit;
		if(! isset($this->errors[$id]))
		{
			Custom::inc('includes/404.php');
		}

		echo parent::__call('edit', array()); // parent::edit();
	}

	/**
	 * Редактирование поля "Ошибка"
	 * @return void
	 */
	public function edit_variable_name()
	{
		$values = $this->diafan->get_values();
		if($values['trace'] && is_array($values['trace']))
		{
			$trace = array();
			foreach($values['trace'] as $key => $value)
			{
				$trace[] = sprintf('#%d: %s', $key, $value);
			}
			$values['trace'] = implode(PHP_EOL, $trace);
		}

		echo '
			<div class="unit" id="initialization">
				<div class="infofield">'.$this->diafan->_('Инициализация').'</div>
				<table>
					<caption></caption>
					<thead>
						<tr>
							<th></th><th></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><b>'.$this->diafan->_('Дата').':</b></td><td>'.($values["datetime"] ? date("d.m.Y H:i", $values["datetime"]) : '').'</td>
						</tr>
						<tr>
							<td><b>'.$this->diafan->_('URI').':</b></td><td><a href="'.$values["uri"].'">'.$values["uri"].'</a></td>
						</tr>
						<tr>
							<td><b>'.$this->diafan->_('Client').':</b></td><td>'.$values["client"].'</td>
						</tr>
						<tr>
							<td><b>'.$this->diafan->_('Host').':</b></td><td>'.$values["host"].'</td>
						</tr>
						<tr>
							<td><b>'.$this->diafan->_('Agent').':</b></td><td>'.$values["agent"].'</td>
						</tr>
						<tr>
							<td><b>'.$this->diafan->_('Referer').':</b></td><td><a href="'.$values["referer"].'">'.$values["referer"].'</a></td>
						</tr>
					</tbody>
				</table>
			</div>';

		if(! empty($values["message"]))
		{
			echo '
			<div class="unit" id="message">
				<div class="infofield">'.$this->diafan->_('Содержание ошибки').'</div>
				<p>'.$values["message"].'</p>
			</div>';
		}
		if(! empty($values["trace"]))
		{
			echo '
			<div class="unit" id="message">
				<div class="infofield">'.$this->diafan->_('Стек вызовов функций').'</div>
				<pre>'.$values['trace'].'</pre>
			</div>';
		}
	}

	/**
	 * Удаляет элемент
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

		//проверка прав пользователя на удаление
		if (! $this->diafan->_users->roles('del', $this->diafan->_admin->rewrite))
		{
			$this->diafan->redirect(URL);
			return;
		}

		if (! empty($_POST["id"]))
		{
			$ids = array($_POST["id"]);
		}
		else
		{
			$ids = $_POST["ids"];
		}
		foreach($ids as $id)
		{
			$id = intval($id);
			if($id)
			{
				$del_ids[] = $id;
			}
		}
		if(! empty($del_ids))
		{
			$this->log->delete($del_ids);
		}

		$this->diafan->redirect(URL.$this->diafan->get_nav);
	}
}
