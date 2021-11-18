<?php
/**
 * Подключение модуля «Онлайн касса» для работы с чеками
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
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
 * Cashregister_inc_receipt
 */
class Cashregister_inc_receipt extends Diafan
{
	/**
	 * Установка статуса заказа
	 * 
	 * @param array $order данные о заказе
	 * @param integer $status_id идентификатор устанавливаемого статуса заказа
	 * @return boolean
	 */
	public function set_status($order, $status_id)
	{
		if(! $this->diafan->configmodules("payments", "cashregister") && ! $this->diafan->configmodules("payments_backend", "cashregister"))
		{
			return false;
		}
		
		$payment_id = DB::query_result("SELECT payment_id FROM {payment_history} WHERE module_name='cart' AND element_id=%d", $order["id"]);
		
		// для платежных систем, отправляющих чеки
		$payments_backend = $this->diafan->configmodules("payments_backend", "cashregister") ? unserialize($this->diafan->configmodules("payments_backend", "cashregister")) : array();
		$payment_backend = (! empty($payments_backend[$payment_id]) ? $payments_backend[$payment_id] : '');
		
		if(! $payment_id || ! in_array($payment_id, explode(',', $this->diafan->configmodules("payments", "cashregister"))) && ! $payment_backend)
		{
			return false;
		}

		$type = '';
		if(in_array($status_id, explode(',', $this->diafan->configmodules("status_sell", "cashregister"))))
		{
			$type = 'sell';
		}
		elseif(in_array($status_id, explode(',', $this->diafan->configmodules("status_presell", "cashregister"))))
		{
			$type = 'presell';
		}
		elseif(in_array($status_id, explode(',', $this->diafan->configmodules("status_refund", "cashregister"))))
		{
			$type = 'refund';
		}

		if($payment_backend && Custom::exists('modules/payment/backend/'.$payment_backend.'/payment.'.$payment_backend.'.cashregister.php'))
		{
			Custom::inc('modules/payment/backend/'.$payment_backend.'/payment.'.$payment_backend.'.cashregister.php');
			
			$name_class = 'Payment_'.$payment_backend.'_cashregister';
			$class = new $name_class($this->diafan);
			if (! is_callable(array(&$class, $type)))
			{
				return;
			}
		}

		if($type)
		{
			if(DB::query_result("SELECT COUNT(*) FROM {shop_cashregister} WHERE important='1' AND order_id=%d AND type='%s'", $order["id"], $type))
			{
				return false;
			}
			if(! $id = $this->diafan->_cashregister->db_add($type, $order["id"], $payment_backend))
			{
				return true;
			}

			if($this->diafan->configmodules('auto_send', 'cashregister'))
			{
				if(! $this->diafan->configmodules('defer', 'cashregister'))
				{
					$this->send($id);
				}
				else
				{
					$this->diafan->_cashregister->defer_init();
				}
			}
		}
		return false;
	}

	/**
	 * Тест
	 *
	 * @return string|void
	 */
	public function test()
	{
		$status = explode(',', $this->diafan->configmodules("status_sell", "cashregister"));
		$status_id = $status[0];
		if(! $status_id)
		{
			return $this->diafan->_('Не указан статус полной оплаты.');
		}
		
		$c = DB::query_result("SELECT COUNT(*) FROM {shop_order}");
		$orders = DB::query_fetch_all("SELECT id FROM {shop_order} LIMIT ".rand(0,$c).',1');
		
		$this->set_status($orders[0], $status_id);
	}

	/**
	 * Отправляет чек
	 *
	 * @param mixed(array|string) $id идентификатор чека
	 * @return boolean
	 */
	public function send($id)
	{
		if(! $row = $this->diafan->_db_ex->get('{shop_cashregister}', $id))
		{
			return false;
		}

		$status = false;
		$row["error"] = $row["trace"] = '';
		$this->diafan->_db_ex->update('{shop_cashregister}', $row["id"], array("timesent='%d'", "status='%h'", "error='%s'", "trace='%s'"), array(time(), (! $status ? '2' : '1'), $row["error"], $row["trace"]));

		try
		{
			if($row["payment"])
			{
				$backend = $row["payment"];
				if(! Custom::exists('modules/payment/backend/'.$backend.'/payment.'.$backend.'.cashregister.php'))
				{
					throw new Exception('Ошибка: файл modules/payment/backend/'.$backend.'/payment.'.$backend.'.cashregister.php не существует.');
				}
				Custom::inc('modules/payment/backend/'.$backend.'/payment.'.$backend.'.cashregister.php');
				
				$name_class = 'Payment_'.$backend.'_cashregister';
				$class = new $name_class($this->diafan);
				if (! is_callable(array(&$class, $row["type"])))
				{
					throw new Exception('Ошибка: в файле modules/payment/backend/'.$backend.'/payment.'.$backend.'.cashregister.php не описан метод '.$row["type"].'().');
				}
			}
			else
			{
				$backend = $this->diafan->configmodules('backend', 'cashregister');
				if(! $backend)
				{
					throw new Exception('Ошибка: бэкенд не определен. Выберите сервис онлайн-кассы в настройках модуля.');
				}
				if(! Custom::exists('modules/cashregister/backend/'.$backend.'/cashregister.'.$backend.'.php'))
				{
					throw new Exception('Ошибка: файл modules/cashregister/backend/'.$backend.'/cashregister.'.$backend.'.php не существует.');
				}
				Custom::inc('modules/cashregister/backend/'.$backend.'/cashregister.'.$backend.'.php');
				
				$name_class = 'Cashregister_'.$backend;
				$class = new $name_class($this->diafan);
				if (! is_callable(array(&$class, $row["type"])))
				{
					throw new Exception('Ошибка: в файле modules/cashregister/backend/'.$backend.'/cashregister.'.$backend.'.php не описан метод '.$row["type"].'().');
				}
			}
			$info = $this->diafan->_order->get($row["order_id"]);
			$info["email"] = $this->diafan->_order->get_email($row["order_id"]);
			$info["phone"] = $this->diafan->_order->get_phone($row["order_id"]);
			$info["summ"] = $this->parse_number($info["summ"]);
			$info["tax"] = $this->parse_number($info["tax"]);
			
			foreach ($info['rows'] as &$item)
			{
				$item["price"] = $this->parse_number($item["price"]);
				$item["summ"] = $this->parse_number($item["summ"]);
				$item["count"] = floatval($item["count"]);
				$item["name"] = $item['name'].($item["article"] ? " ".$item["article"] : "");
			}
			if (! empty($info["additional_cost"]))
			{
				foreach ($info["additional_cost"] as $row)
				{
					$info['rows'][] = array(
						"name"  => $row['name'],
						"count" => 1,
						"price" => $this->parse_number($row["summ"]),
						"summ"  => $this->parse_number($row["summ"]),
					);
				}
			}
			if (! empty($info["delivery"]))
			{
				$delivery_cost = $this->parse_number($info["delivery"]["summ"]);
				if ($delivery_cost > 0)
				{
					$info['rows'][] = array(
						"name"   => $this->diafan->_("Доставка", false),
						"count"  => 1,
						"price"  => $delivery_cost,
						"summ"   => $delivery_cost,
						"is_delivery" => true,
					);
				}
			}
			$info["cashregister_id"] = $row["id"];

			if($row['payment'])
			{
				$info["payment"] = DB::query_fetch_array("SELECT * FROM {payment_history} WHERE module_name='cart' AND element_id=%d", $row["order_id"]);
				
				$info["payment"]["params"] = unserialize(DB::query_result("SELECT params FROM {payment} WHERE id=%d", $info["payment"]["payment_id"]));
			}
			
			$external_id = call_user_func_array (array(&$class, $row["type"]), array($info));
			
			$this->diafan->_db_ex->update('{shop_cashregister}', $row["id"], array("external_id='%h'"), array($external_id));
			
			$status = true;
		}
		catch (Exception $e)
		{
			$row["error"] = $e->getMessage();
			$row["trace"] = '';
			$status = false;
		}

		if(! $this->diafan->configmodules('del_after_send', 'cashregister') || ! $status)
		{
			$this->diafan->_db_ex->update('{shop_cashregister}', $row["id"], array("timesent='%d'", "status='%h'", "error='%s'", "trace='%s'"), array(time(), (! $status ? '2' : '1'), $row["error"], $row["trace"]));
		}
		else
		{
			$this->diafan->_db_ex->delete('{shop_cashregister}', $id);
		}

		return true;
	}

    /**
     * Конвертирует в float результат работы функции number_format
     * @param string $number
     * @return float
     */
    private function parse_number($number)
	{
        $dec_point = $this->diafan->configmodules("format_price_2", "shop");
        return floatval(str_replace($dec_point, '.', preg_replace('/[^\d' . preg_quote($dec_point) . ']/', '', $number)));
    }
}