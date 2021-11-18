<?php
/**
 * Редактирование настроек онлайн-кассы
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
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

class Cashregister_admin_config extends Frame_admin
{
	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'config' => array (
			'backend' => array(
				'type' => 'function',
				'name' => 'Тип',
				'help' => 'Выбор сервиса онлайн-кассы. Добавить новый сервис можно в папке modules/cashregister/backend.',
				'addons_tag' => array(
					'tag' => 'cashregister',
					'title' => 'Добавить тип',
				),
			),
			'test' => array(
				'type' => 'function',
				'no_save' => true,
			),
			'payments' => array(
				'type' => 'function',
				'name' => 'Методы оплаты, при которых формируются чеки',
			),
			'status_presell' => array(
				'type' => 'function',
				'name' => 'Статусы заказа, на которые формируется чек 100% предоплаты',
				'help' => 'Рекомендуется выбрать статусы, на которые установлено действие «оплата».',
			),
			'status_sell' => array(
				'type' => 'none',
				'name' => 'Статусы заказа, на которые формируется чек полной оплаты',
				'help' => 'Рекомендуется выбрать статусы, на которые установлено действие «выполнение».',
				'no_save' => true,
			),
			'status_refund' => array(
				'type' => 'none',
				'name' => 'Статусы заказа, на которые формируется чек возврата',
				'help' => 'Рекомендуется выбрать статусы, на которые установлено действие «отмена».',
				'no_save' => true,
			),
			'auto_send' => array(
				'type' => 'checkbox',
				'name' => 'Автоматическая отправка чеков',
				'help' => 'Если отмечено, отправка чеков пройдет в автоматическом режиме. Иначе требуется инициализация отправки чеков в административной панели сайта.',
			),
			'del_after_send' => array(
				'type' => 'checkbox',
				'name' => 'Удалять отправленные чеки',
				'help' => 'Автоматическое удаление чеков после отправки.',
			),
			'defer' => array(
				'type' => 'checkbox',
				'name' => 'Включить отложенную отправку для чеков',
				'help' => 'Отправка чеков будет проходить независимо от основных процессов.',
				'depend' => 'auto_send',
			),
		),
	);

	/**
	 * @var array настройки модуля
	 */
	public $config = array (
		'config', // файл настроек модуля
	);

	/**
	 * Редактирование поля "Проверочный чек"
	 * @return void
	 */
    public function edit_config_variable_test() 
	{
        echo '<div class="unit" id="test">'
        .'<p><button class="btn js_btn_test" type="button">'.$this->diafan->_('Создать проверочный чек').'</button></p>'
        .'<div id="test_check"></div>'
        .'</div>';
    }

	/**
	 * Редактирование поля "Статусы заказа"
	 * @return void
	 */
    public function edit_config_variable_status_presell() 
	{
		$rows = DB::query_fetch_all("SELECT id, [name] FROM {shop_order_status} WHERE trash='0' ORDER BY sort ASC");
		
		echo '
		<div class="unit" id="status_presell">
			<div class="infofield">
				'.$this->diafan->variable_name().$this->diafan->help().'
			</div>';
		$values = explode(',', $this->diafan->value);
		foreach($rows as $row)
		{
			echo '<p><input name="status_presell[]" type="checkbox" value="'.$row["id"].'" id="status_presell_'.$row["id"].'"'.(in_array($row["id"], $values) ? ' checked' : '').'><label for="status_presell_'.$row["id"].'">'.$row["name"].'</label></p>';
		}
		echo '</select>';
		echo '</div>
		<div class="unit" id="status_sell">
			<div class="infofield">
				'.$this->diafan->variable_name("status_sell").$this->diafan->help("status_sell").'
			</div>';
		
		$values = explode(',', $this->diafan->values("status_sell"));
		foreach($rows as $row)
		{
			echo '<p><input name="status_sell[]" type="checkbox" value="'.$row["id"].'" id="status_sell_'.$row["id"].'"'.(in_array($row["id"], $values) ? ' checked' : '').'><label for="status_sell_'.$row["id"].'">'.$row["name"].'</label></p>';
		}
		echo '</select>';
		echo '</div>
		
		<div class="unit" id="status_refund">
			<div class="infofield">
				'.$this->diafan->variable_name("status_refund").$this->diafan->help("status_refund").'
			</div>';
		$values = explode(',', $this->diafan->values("status_refund"));
		foreach($rows as $row)
		{
			echo '<p><input name="status_refund[]" type="checkbox" value="'.$row["id"].'" id="status_refund_'.$row["id"].'"'.(in_array($row["id"], $values) ? ' checked' : '').'><label for="status_refund_'.$row["id"].'">'.$row["name"].'</label></p>';
		}
		echo '</div>';
    }

	/**
	 * Сохранение поля "Статусы заказа"
	 * @return void
	 */
    public function save_config_variable_status_presell() 
	{
		$this->diafan->set_query("status_presell='%s'");
		if(! empty($_POST["status_presell"]))
		{
			$this->diafan->set_value(implode(',', $this->diafan->filter($_POST["status_presell"], "integer")));
		}
		else
		{
			$this->diafan->set_value('');
		}
		
		$this->diafan->set_query("status_sell='%s'");
		if(! empty($_POST["status_sell"]))
		{
			$this->diafan->set_value(implode(',', $this->diafan->filter($_POST["status_sell"], "integer")));
		}
		else
		{
			$this->diafan->set_value('');
		}
		
		$this->diafan->set_query("status_refund='%s'");
		if(! empty($_POST["status_refund"]))
		{
			$this->diafan->set_value(implode(',', $this->diafan->filter($_POST["status_refund"], "integer")));
		}
		else
		{
			$this->diafan->set_value('');
		}
	}

	/**
	 * Редактирование поля "Методы оплаты"
	 * @return void
	 */
    public function edit_config_variable_payments() 
	{
		$rows = DB::query_fetch_all("SELECT id, [name], payment FROM {payment} WHERE trash='0' ORDER BY sort ASC");
		
		echo '
		<div class="unit" id="payments">
			<div class="infofield">
				'.$this->diafan->variable_name().$this->diafan->help().'
			</div>';
		$values = explode(',', $this->diafan->value);
		
		$payments_backend = $this->diafan->values("payments_backend");
		$payments_backend = $payments_backend ? unserialize($payments_backend) : array();
		
		foreach($rows as $row)
		{
			echo '<p class="payments_backend"><input name="payments[]" type="checkbox" value="'.$row["id"].'" id="payments_'.$row["id"].'"'.(in_array($row["id"], $values) ? ' checked' : '').'><label for="payments_'.$row["id"].'">'.$row["name"].'</label>';
			if(Custom::exists('modules/payment/backend/'.$row["payment"].'/payment.'.$row["payment"].'.cashregister.php'))
			{
				echo ' <input name="payments_backend['.$row["id"].']" type="checkbox" value="'.$row["payment"].'" id="payments_backend_'.$row["id"].'"'.(! empty($payments_backend[$row["id"]]) ? ' checked' : '').'><label for="payments_backend_'.$row["id"].'">'.$this->diafan->_('чеки в платежном модуле').'</label>';
			}
			echo'</p>';
		}
		echo '</div>';
    }

	/**
	 * Сохранение поля "Методы оплаты"
	 * @return void
	 */
    public function save_config_variable_payments() 
	{
		$this->diafan->set_query("payments='%s'");
		if(! empty($_POST["payments"]))
		{
			$this->diafan->set_value(implode(',', $this->diafan->filter($_POST["payments"], "integer")));
		}
		else
		{
			$this->diafan->set_value('');
		}
		
		$this->diafan->set_query("payments_backend='%s'");
		$payments_backend = array();
		if(! empty($_POST["payments_backend"]))
		{
			foreach($_POST["payments_backend"] as $p_id => $payment)
			{
				$payments_backend[$this->diafan->filter($p_id, "integer")] = $this->diafan->filter($payment, "string");
			}
		}
		$this->diafan->set_value($payments_backend ? serialize($payments_backend) : '');
	}
}
