<?php
/**
 * История заказов на доставку
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
 * Delivery_admin_history
 */
class Delivery_admin_history extends Frame_admin
{
	/**
	 * @var string таблица в базе данных
	 */
	public $table = 'shop_delivery_history';
	
	/**
	 * @var string тип элемантов
	 */
	public $element_type = 'cat';

	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'main' => array (
			'id' => array(
				'type' => 'function',
				'name' => 'Идентификатор',
				'no_save' => true,
			),
			'created' => array(
				'type' => 'datetime',
				'name' => 'Дата и время платежа',
				'no_save' => true,
				'disabled' => true,
			),
			'summ' => array(
				'type' => 'floattext',
				'name' => 'Сумма',
				'no_save' => true,
				'disabled' => true,
			),
			'order_id' => array(
				'type' => 'function',
				'name' => 'Заказ',
				'no_save' => true,
			),
			'tracknumber' => array(
				'type' => 'text',
				'name' => 'Трек-номер заказа',
			),
			'status' => array(
				'type' => 'select',
				'name' => 'Статус',
				"select" => array(
					"init" => 'инициация',
					"error" => 'ошибка',
					"complete" => 'сформирован',
				),
			),
			'service' => array(
				'type' => 'select',
				'name' => 'Способ доставки',
				'select_db' => array(
					'table' => 'shop_delivery',
					"id" => "service",
					"name" => "[name]",
					"where" => "trash='0'",
					"order" => 'sort ASC',
				),
			),
			'service_id' => array(
				'type' => 'text',
				'name' => 'ID во внешней системе',
				'no_save' => true,
				'disabled' => true,
			),
			'data' => array(
				'type' => 'text',
				'name' => 'Полученные данные',
				'no_save' => true,
				'disabled' => true,
			),
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
			'name' => 'Название',
			'variable' => 'id',
			'text' => 'Идентификатор %d',
		),
		'status' => array(
			'type' => 'select',
			'name' => 'Статус',
			'sql' => true,
			'no_important' => true,
		),
		'summ' => array(
			'name' => 'Сумма',
			'sql' => true,
		),
		'order_id' => array(
			'type' => 'select',
			'name' => 'Заказ',
			'sql' => true,
			'no_important' => true,
		),
		'service' => array(
			'type' => 'text',
			'name' => 'Служба доставки',
			'sql' => true,
			'no_important' => true,
		),
		'adapt' => array(
			'class_th' => 'item__th_adapt',
		),
		'separator' => array(
			'class_th' => 'item__th_seporator',
		),				
		'actions' => array(
			'del' => true,
		),
	);

	/**
	 * @var array поля для фильтра
	 */
	public $variables_filter = array (
		'created' => array(
			'name' => 'Период',
			'type' => 'datetime_interval',
		),
		'summ' => array(
			'name' => 'Искать по сумме',
			'type' => 'numtext_interval',
		),
		'status' => array(
			'name' => 'Статус',
			'type' => 'select',
		),
		'service' => array(
			'name' => 'Служба доставки',
			'type' => 'select',
		),
		'service_id' => array(
			'name' => 'ID во внешней системе',
			'type' => 'text',
		),
	);

	/**
	 * Выводит список заказов
	 * @return void
	 */
	public function show()
	{
		$this->diafan->list_row();
	}

	/**
	 * Выводит номер заказа
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_order_id($row, $var)
	{
		$text = '<div class="no_important">';
		if($row["order_id"])
		{
			$text .= '<a href="'.BASE_PATH_HREF.'order/edit'.$row["order_id"].'/">'.$this->diafan->_('Заказ №%s', $row["order_id"]).'</a>';
		}
		else
		{
			$text .= $this->diafan->_('Не оформлен', $row["order_id"]);
		}
		$text .= '</div>';
		return $text;
	}

	/**
	 * Выводит сумму платежа
	 *
	 * @param array $row информация о текущем элементе списка
	 * @param array $var текущее поле
	 * @return string
	 */
	public function list_variable_summ($row)
	{
		$text = '<div class="sum">';
		if($row["summ"])
		{
			$text .= $row["summ"].' '.$this->diafan->configmodules("currency", "balance");
		}
		$text .= '</div>';
		return $text;
	}
	
	/**
	 * Редактирование поля "Заказ"
	 * @return void
	 */
	public function edit_variable_order_id()
	{
		echo '
		<div class="unit">
			<b>
				'.$this->diafan->variable_name().':
			</b>';
				echo '<a href="'.BASE_PATH_HREF.'order/edit'.$this->diafan->value.'/">'.$this->diafan->_('Заказ №%s', $this->diafan->value).'</a>';
		echo '
			'.$this->diafan->help().'
		</div>';
	}
	
	/**
	 * Редактирование поля "Данные"
	 * @return void
	 */
	public function edit_variable_data()
	{
		echo '
		<div class="unit">
			<b>
				'.$this->diafan->variable_name().':
			</b><pre>';
				print_r(unserialize($this->diafan->value));
		echo '</pre>
			'.$this->diafan->help().'
		</div>';
	}
}