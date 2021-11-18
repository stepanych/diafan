<?php
/**
 * Подключение модуля «Доставка»
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

class Delivery_inc extends Diafan
{
	/**
	 * Получает список способов доставки
	 * @param integer $select_delivery_id выбранный способ доставки
	 * @param float $summ стоимость товаров в корзине
	 * @return array
	 */
	public function get_all(&$select_delivery_id, $summ)
	{
		if($this->diafan->_site->module == 'cart')
		{
			$this->diafan->_site->js_view[] = BASE_PATH.Custom::path('modules/delivery/js/delivery.get_all.js');
		}

		$rows = DB::query_fetch_all("SELECT id, [name], [text], service, params FROM {shop_delivery} WHERE [act]='1' AND trash='0' ORDER BY sort ASC");

		$thresholds = DB::query_fetch_key_array("SELECT delivery_id, price, amount FROM {shop_delivery_thresholds} ORDER BY amount ASC", "delivery_id");

		// если способ доставки не выбран или выбран не существующий, выбираем первый
		if($rows && (! $select_delivery_id || ! in_array($select_delivery_id, $this->diafan->array_column($rows, "id"))))
		{
			$select_delivery_id = $rows[0]["id"];
			$this->diafan->_cart->edit(array("delivery_id" => $select_delivery_id));
		}

		foreach ($rows as &$d)
		{
			$d['price'] = 0;
			$d["service_view"] = '';
			if ($d['id'] == $select_delivery_id)
			{
				$d["selected"] = true;
			}
			else
			{
				$d["selected"] = false;
			}
			if($d["service"])
			{
				$this->get($d);
			}
			$d["thresholds"] = (! empty($thresholds[$d["id"]]) ? $thresholds[$d["id"]] : array());
			foreach ($d["thresholds"] as &$d_th)
			{
				if($d_th['amount'] <= $summ)
				{
					$d['price'] = $d_th["price"];
				}
			}
		}

		return $rows;
	}

	/**
	 * Подключает способ доставки
	 *
	 * @param array $row массив данных о запрашиваемом модуле доставки из таблицы {shop_delivery}
	 * @return void
	 */
	private function get(&$row)
	{
		if($this->diafan->_site->module != 'cart')
		{
			return;
		}
		$inc = $this->backend($row["service"]);
		// обновленный формат
		if(! empty($this->cache["inc"][$row["service"]]))
		{
			$js = Custom::path('modules/delivery/backend/'.$row["service"].'/delivery.'.$row["service"].'.js');
			if($js)
			{
				$this->diafan->_site->js_view[] = BASE_PATH.$js;
			}
			$inc->data = $this->get_data();
			$inc->data["id"] = $row["id"];
			$inc->data["params"] = unserialize($row["params"]);
			$inc->data["history"] = $this->get_history($row["service"]);

			$inc->get();
			$row["price"] = $inc->calculate();
			$row["service_info"] = $inc->info();

			$row["service_view"] = '';
			if(! isset($this->cache["diafan_delivery_config"]))
			{
				$row["service_view"] .= '<script type="text/javascript">var diafan_delivery_config = {};</script>';
				$this->cache["diafan_delivery_config"] = true;
			}

			if($row["selected"])
			{
				$row["service_view"] .= $this->view($row["service"], $inc->data);
			}

			$js = Custom::path('modules/delivery/backend/'.$row["service"].'/delivery.'.$row["service"].'.widget.js');
			if($js)
			{
				$this->diafan->_site->js_view[] = BASE_PATH.$js;

				if($row["selected"])
				{
					$row["service_view"] .= '<div id="delivery_'.$row["service"].'_place"></div>
					<script type="text/javascript">
					document.dispatchEvent(new CustomEvent("delivery_'.$row["service"].'_ready"));
					</script>';
				}
			}
		}
		// поддержка старого формата до перехода всех бэкендов на новый формат
		else
		{
			$result = $this->get_data();
			$result["id"] = $row["id"];
			$result["params"] = unserialize($row["params"]);
			$result["history"] = $this->get_history($row["service"]);

			$js = Custom::path('modules/delivery/backend/'.$row["service"].'/delivery.'.$row["service"].'.js');
			if($js)
			{
				$this->diafan->_site->js_view[] = $js;
			}

			$inc->get($result);
			$row["price"] = $inc->calculate($result["params"]);
			$row["service_info"] = $inc->get_info($result["params"]);
			$row["service_view"] = '';

			if($row["selected"])
			{
				$row["service_view"] = $this->view($row["service"], $result);
			}
		}
	}

	/**
	 * Подключает шаблон службы доставки
	 *
	 * @param string $service служба доставки
	 * @param array $result данные, передаваемые в шаблон
	 * @return void
	 */
	private function view($service, $result)
	{
		if(! Custom::exists('modules/delivery/backend/'.$service.'/delivery.'.$service.'.view.php'))
			return;

		ob_start();
		include(Custom::path('modules/delivery/backend/'.$service.'/delivery.'.$service.'.view.php'));
		$text = ob_get_contents();
		ob_end_clean();
		return $text;
	}

	/**
	 * Результат выбора сервиса доставки при оформлении заказа
	 *
	 * @param integer $select_delivery_id выбранный способ доставки
	 * @param float $summ стоимость товаров в корзине
	 * @return array
	 */
	public function order($select_delivery_id, $summ)
	{
		if(! $select_delivery_id)
		{
			return;
		}
		$delivery = DB::query_fetch_array("SELECT * FROM {shop_delivery} WHERE [act]='1' AND trash='0' AND id=%d LIMIT 1", $select_delivery_id);
		if(! $delivery)
		{
			return;
		}
		// стоимость доставки рассчитывается сторонним скриптом
		if($delivery["service"])
		{
			$inc = $this->backend($delivery["service"]);

			// обновленный формат
			if(! empty($this->cache["inc"][$delivery["service"]]))
			{
				$inc->data = $this->get_data();
				$inc->data["id"] = $delivery["id"];
				$inc->data["params"] = unserialize($delivery["params"]);
				$inc->data["history"] = $this->get_history($delivery["service"]);

				$inc->get();
				$delivery["price"] = $inc->calculate();
				$delivery["service_info"] = $inc->info();
				if(! $inc->valid())
				{
					$delivery["error"] = $inc->error;
					return $delivery;
				}
			}
			// поддержка старого формата до перехода всех бэкендов на новый формат
			else
			{
				$result = $this->get_data();
				$result["id"] = $delivery["id"];
				$result["params"] = unserialize($delivery["params"]);
				$result["history"] = $this->get_history($delivery["service"]);

				$inc->get($result);
				$delivery["price"] = $inc->calculate($result["params"]);
				$delivery["service_info"] = $inc->get_info($result["params"]);

				if(is_null($delivery['price']))
				{
					$delivery["error"] = 'Выбранный способ доставки не доступен в вашем городе.';
				}
				elseif($delivery['price'] === false)
				{
					$delivery["error"] = 'Пожалуйста, задайте дополнительные опции доставки.';
				}
			}

			// стоимость доставки может быть равна 0 при определенной сумме заказа
			// тогда при достижении этой суммы, стоимость доставки не рассчитывается
			$threshold = DB::query_fetch_array("SELECT * FROM {shop_delivery_thresholds} WHERE delivery_id=%d AND price=0 AND amount>0", $select_delivery_id);
			if($threshold)
			{
				if($threshold["amount"] <= $summ)
				{
					$delivery['price'] = 0;
				}
			}
		}
		else
		{
			$delivery["price"] = DB::query_result("SELECT price FROM {shop_delivery_thresholds} WHERE delivery_id=%d AND amount<=%f ORDER BY amount DESC LIMIT 1", $select_delivery_id, $summ);
		}
		return $delivery;
	}

	/**
	 * Запись в историю заказов
	 *
	 * @param string $status статус заказа на доставку
	 * @param float $summ стоимость доставки
	 * @param string $service служба доставки
	 * @param string $service_id идентификатор заказа в системе службы доставки
	 * @param string|array $data данные о заказе, используемые бэкендом
	 * @return void
	 */
	public function set_history($status, $summ, $service, $service_id, $data)
	{
		if(! empty($_SESSION["delivery_history_id"]))
		{
			$fields = array("created=%d");
			$values = array(time());
			if($status !== false)
			{
				$fields[] = "status='%s'";
				$values[] = $status;
			}
			if($summ !== false)
			{
				$fields[] = "summ=%f";
				$values[] = $summ;
			}
			if($service !== false)
			{
				$fields[] = "service='%h'";
				$values[] = $service;
			}
			if($service_id !== false)
			{
				$fields[] = "service_id='%h'";
				$values[] = $service_id;
			}
			if($data !== false)
			{
				if(is_array($data))
				{
					$data = serialize($data);
				}
				$fields[] = "data='%s'";
				$values[] = $data;
			}
			$values[] = $_SESSION["delivery_history_id"];
			DB::query("UPDATE {shop_delivery_history} SET ".implode(",", $fields)." WHERE id=%d", $values);
		}
		else
		{
			$fields = array("created");
			$mask = array("%d");
			$values = array(time());
			if($status !== false)
			{
				$fields[] = "status";
				$mask[] = "'%s'";
				$values[] = $status;
			}
			if($summ !== false)
			{
				$fields[] = "summ";
				$mask[] = "%f";
				$values[] = $summ;
			}
			if($service !== false)
			{
				$fields[] = "service";
				$mask[] = "'%h'";
				$values[] = $service;
			}
			if($service_id !== false)
			{
				$fields[] = "service_id";
				$mask[] = "'%h'";
				$values[] = $service_id;
			}
			if($data !== false)
			{
				if(is_array($data))
				{
					$data = serialize($data);
				}
				$fields[] = "data";
				$mask[] = "'%s'";
				$values[] = $data;
			}
			$_SESSION["delivery_history_id"] = DB::query("INSERT INTO {shop_delivery_history} (".implode(",", $fields).") VALUES (".implode(",", $mask).")", $values);
		}
	}

	/**
	 * Получает данные о заказе на доставку для выбранного способа доставки
	 *
	 * @param string $service служба доставки
	 * @return array|boolaen false
	 */
	public function get_history($service)
	{
		if(! empty($_SESSION["delivery_history_id"]) && ! isset($this->cache["history"]))
		{
			$this->cache["history"] = DB::query_fetch_array("SELECT * FROM {shop_delivery_history} WHERE id=%d", $_SESSION["delivery_history_id"]);
		}
		if(! empty($this->cache["history"]) && $this->cache["history"]["service"] == $service)
		{
			if($this->cache["history"]["data"] && ! is_array($this->cache["history"]["data"]))
			{
				$this->cache["history"]["data"] = unserialize($this->cache["history"]["data"]);
			}
			return $this->cache["history"];
		}
		return false;
	}

	/**
	 * Окончание оформления заказа
	 *
	 * @param integer $order_id
	 * @return void
	 */
	public function done($order_id)
	{
		if (isset($_SESSION["delivery_history_id"])) {
			if ($_SESSION["delivery_history_id"]) {
				DB::query("UPDATE {shop_delivery_history} SET order_id=%d WHERE id=%d", $order_id, $_SESSION["delivery_history_id"]);
			}
	        unset($_SESSION["delivery_history_id"]);
		}

		$inc = $this->backend();

		if (is_callable(array(&$inc, 'done')))
		{
			call_user_func_array(array(&$inc, 'done'), array($order_id));
		}
	}

	/**
	 * Действие при смене статуса заказа
	 *
	 * @param integer $order_id
	 * @param integer $status_id
	 * @return void
	 */
	public function set_status($order_id, $status_id)
	{
		if(! $history = DB::query_fetch_array("SELECT * FROM {shop_delivery_history} WHERE order_id=%d", $order_id))
		{
			return;
		}

		$inc = $this->backend($history["service"]);

		if (is_callable(array(&$inc, 'set_status')))
		{
			$inc->data["history"] = $history;
			$inc->data["history"]["data"] = unserialize($inc->data["history"]["data"]);
			$inc->data["params"] = unserialize(DB::query_result("SELECT params FROM {shop_delivery} WHERE service='%s' AND trash='0'", $history["service"]));

			call_user_func_array(array(&$inc, 'set_status'), array($status_id));
		}
	}

	/**
	 * Подключает службу доставку
	 *
	 * @param string $service название службы доставки
	 * @return obj
	 */
	private function backend($service = '')
	{
		if(! $service)
		{
			return (! empty($this->cache["current_inc"]) ? $this->cache["current_inc"] : false);
		}
		if(! isset($this->cache["inc"][$service]))
		{
			$this->cache["inc"][$service] = false;
			if(Custom::exists('modules/delivery/backend/'.$service.'/delivery.'.$service.'.inc.php'))
			{
				Custom::inc('modules/delivery/backend/'.$service.'/delivery.'.$service.'.inc.php');

				$name = 'Delivery_'.$service.'_inc';
				$this->cache["inc"][$service] = new $name($this->diafan);
			}
		}
		if(empty($this->cache["inc"][$service]) && ! isset($this->cache["model"][$service]))
		{
			$this->cache["model"][$service] = false;
			if(Custom::exists('modules/delivery/backend/'.$service.'/delivery.'.$service.'.model.php'))
			{
				Custom::inc('modules/delivery/backend/'.$service.'/delivery.'.$service.'.model.php');

				$name = 'Delivery_'.$service.'_model';
				$this->cache["model"][$service] = new $name($this->diafan);
			}
		}
		if(! empty($this->cache["inc"][$service]))
		{
			$this->cache["current_inc"] = $this->cache["inc"][$service];
		}
		else
		{
			$this->cache["current_inc"] = $this->cache["model"][$service];
		}
		return $this->cache["current_inc"];
	}

	/**
	 * Получает информацию о заказе для шаблона службы доставки: высоту, ширину, длину, вес, сумму, поля формы оформления заказа, товары в корзине
	 *
	 * @return array
	 */
	private function get_data()
	{
		if(isset($this->cache["info"]))
		{
			return $this->cache["info"];
		}
		$result = $this->diafan->_cart->get_form_param();

		$result["cart"] = $this->diafan->_cart->get();
		$result['summ'] = $result["cart"]["summ"];

		$weight = 0;
		$owidth = 0;
		$olength = 0;
		$oheight = 0;
		foreach ($result["cart"]["rows"] as $c)
		{
			$row = $c["good"];
			if (empty($row["weight"]))
			{
				$row["weight"] = 0;
			}
			if (empty($row["width"]))
			{
				$row["width"] = 0;
			}
			if (empty($row["length"]))
			{
				$row["length"] = 0;
			}
			if (empty($row["height"]))
			{
				$row["height"] = 0;
			}
			for ($i = 0; $i < $c['count']; $i++)
			{
				$weight += $row["weight"];
				if (min($olength, $owidth, $oheight) == $olength)
				{
					if ($row["width"] > $owidth)
					{
						$owidth = $row["width"];
					}
					if ($row["height"] > $oheight)
					{
						$oheight = $row["height"];
					}
					$olength += $row["length"];
				}
				elseif (min($olength, $owidth, $oheight) == $owidth)
				{
					if($row["length"] > $olength)
					{
						$olength = $row["length"];
					}
					if($row["height"] > $oheight)
					{
						$oheight = $row["height"];
					}
					$owidth += $row["width"];
				}
				elseif (min($olength, $owidth, $oheight) == $oheight)
				{
					if($row["width"] > $owidth)
					{
						$owidth = $row["width"];
					}
					if($row["length"] > $olength)
					{
						$olength = $row["length"];
					}
					$oheight += $row["height"];
				}
			}
			$result['weight'] = $weight;
			$result['width'] = $owidth;
			$result['length'] = $olength;
			$result['height'] = $oheight;
		}
		$result['weight_unit'] = $this->diafan->configmodules('weight_unit', 'shop');
		if (! $result['weight_unit'])
		{
			$result['weight_unit'] = 'g';
		}
		$this->cache["info"] = $result;
		return $result;
	}
}

// поддержка старого формата до перехода всех бэкендов на новый формат

/**
 * Delivery_model_interface
 *
 * Интерфейс модели модуля службы доставки
 */
interface Delivery_model_interface
{
	/*
	 * Подключает способ доставки. Данные о заказе и способе доставки, переданные аргументом могут быть дополнены и использованы в дальнейшем в шаблоне бэкенда
	 *
	 * @param array $result данные о заказе (высота, ширина, длина, сумма, вес), идентификационный номер способа доставки ("id"), настройки способа доставки (array "params")
	 * @return array
	 */
	public function get(&$result);

	/*
	 * Получает стоимость доставки
	 *
	 * @param array $params настройки способа доставки
	 * @return mixed
	 */
	public function calculate($params);

	/*
	 * Получает данные, введенные пользователем в интерфейсе службы доставки
	 *
	 * @param array $params настройки способа доставки
	 * @return mixed
	 */
	public function get_info($params);
}
