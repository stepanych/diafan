<?php
/**
 * API-скрипт для виджета «Доставка Saferoute»
 *
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

class Delivery_saferoute_api extends Diafan
{
	/**
	 * Обновление статуса
	 *
	 * @return void
	 */
	public function update_status()
	{
		$params = unserialize(DB::query_result("SELECT params FROM {shop_delivery} WHERE service='saferoute'"));
			  
		if(empty($params['token']) || empty($params['shop_id']) ||  empty($params['api_key']))
		{
			$this->to_json(array('status' => 'error', 'error' => 'Не заданы настройки способа доставки в CMS.'));
		}
		Custom::inc('includes/header.php');
		if(trim($params['token']) != Header::value('Token') || trim($params['shop_id']) != Header::value('Shop-Id') || trim($params['api_key']) != Header::value('Api-Key'))
		{
			$this->to_json(array('status' => 'error', 'error' => 'Заголовки не соответствуют параметрам в настройках.'));
		}
		if(empty($_POST["id"]))
		{
			$this->to_json(array('status' => 'error', 'error' => 'Переданы не все параметры.'));
		}
		$row = DB::query_fetch_array("SELECT * FROM {shop_delivery_history} WHERE service='saferoute' AND service_id='%s'", $_POST["id"]);
		if(! $row)
		{
			$this->to_json(array('status' => 'error', 'error' => 'Заказ на доставку не найден в системе.'));
		}
		if(! empty($_POST["trackNumber"]))
		{
			DB::query("UPDATE {shop_delivery_history} SET tracknumber='%h' WHERE id=%d", $_POST["trackNumber"], $row["id"]);
		}
		if(! empty($_POST["statusCMS"]))
		{
			$status = DB::query_fetch_array("SELECT * FROM {shop_order_status} WHERE trash='0' AND id=%d", $_POST["statusCMS"]);
			if(! $status)
			{
				$this->to_json(array('status' => 'error', 'error' => 'Статус отсутствует в системе.'));
			}
			$order = DB::query_fetch_array("SELECT * FROM {shop_order} WHERE trash='0' AND id=%d", $row["order_id"]);
			if(! $order)
			{
				$this->to_json(array('status' => 'error', 'error' => 'Заказ отсутствует в системе.'));
			}
			$this->diafan->_order->set_status($order, $status);
		}
		$this->to_json(array('status' => 'ok'));
	}

	/**
	 * Список статусов
	 *
	 * @return void
	 */
	public function status()
	{
		$rows = DB::query_fetch_key_value("SELECT id, [name] FROM {shop_order_status} WHERE trash='0'", "id", "name");
		$this->to_json($rows);
	}

	/**
	 * Список платежных методов
	 *
	 * @return void
	 */
	public function payment()
	{
		$rows = DB::query_fetch_key_value("SELECT id, [name] FROM {payment} WHERE trash='0' AND [act]='1'", "id", "name");
		echo $this->to_json($rows);
	}

	/**
	 * Провека API
	 *
	 * @return void
	 */
	public function init()
	{
		include_once "SafeRouteWidgetApi.php";
		
		
		$widgetApi = new SafeRouteWidgetApi();
		
		$params = unserialize(DB::query_result("SELECT params FROM {shop_delivery} WHERE service='saferoute'"));
			  
		// Укажите здесь свой токен
		$widgetApi->setToken(! empty($params['token']) ? $params['token'] : '');
		// А здесь ID магазина
		$widgetApi->setShopId(! empty($params['shop_id']) ? $params['shop_id'] : '');
		
		
		$request = ($_SERVER['REQUEST_METHOD'] === 'POST')
			? json_decode(file_get_contents('php://input'), true)
			: $_REQUEST;
		
		
		$widgetApi->setMethod($_SERVER['REQUEST_METHOD']);
		$widgetApi->setData(isset($request['data']) ? $request['data'] : []);
		
		echo $widgetApi->submit(! empty($request['url']) ? $request['url'] : '');
	}


	/**
	 * Преобразует массив в формат JSON
	 *
	 * @param array $data исходный массив
	 * @return void
	 */
	private function to_json($data)
	{
		header('Content-Type: application/json; charset=utf-8');
		$php_version_min = 50400; // PHP 5.4
		if($this->diafan->version_php() < $php_version_min)
		{
			// TO_DO: кириллица в ответе JSON - JSON_UNESCAPED_UNICODE
			$json = preg_replace_callback(
				"/\\\\u([a-f0-9]{4})/",
				function($matches) {
					return iconv('UCS-4LE','UTF-8',pack('V', hexdec('U' . $matches[0])));
				},
				json_encode($data)
			);
			$json = str_replace('&', '&amp;', $json);
			$json = str_replace(array('<', '>'), array('&lt;', '&gt;'), $json);
		}
		else $json = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		echo $json;
		exit;
	}
}

$api = new Delivery_saferoute_api($this->diafan);
switch($_GET["rewrite"])
{
	case "saferoute/api/statuses.json":
		$api->status();
		break;
		
	case "saferoute/api/payment-methods.json":
		$api->payment();
		break;
	
	case "saferoute/api/order-status-update":
		$api->update_status();
		break;
	
	default:
		$api->init();
		break;
}
exit;