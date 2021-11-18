<?php
/**
 * Онлайн касса платежного метода «Robokassa»
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

class Payment_robokassa_cashregister extends Diafan
{
    /**
     * Чек «Полная оплата»
	 *
     * @param array $info данные о заказе
     * @return string  Уникальный идентификатор чека
     * @throws RobokassaException
     */
    public function sell($info)
    {
        if (empty($info["payment"]["params"]["robokassa_receipt"]))
		{
            throw new RobokassaException('Ошибка: Отключена настройка «Фискализация для клиентов Robokassa. Облачное решение)». Отключите отправление чеков через платежный модуль в настройках модуля «Онлайн касса».', 0);
        }
        if (empty($info["payment"]["params"]["robokassa_login"]))
		{
            throw new RobokassaException('Ошибка: Не заполнена настройка «Robokassa: логин» для платежного метода «Robokassa».', 0);
        }
        if (empty($info["payment"]["params"]["robokassa_pass_1"]))
		{
            throw new RobokassaException('Ошибка: Не заполнена настройка «Robokassa: пароль 1» для платежного метода «Robokassa».', 0);
        }
        if (empty($info["payment"]["params"]["robokassa_sno"]))
		{
            throw new RobokassaException('Ошибка: Не заполнена настройка «Система налогообложения
магазина» для платежного метода «Robokassa».', 0);
        }
		
        if ($info["phone"])
		{
            $info["phone"] = preg_replace('/\D/', '', $info["phone"]);
            if (strlen($info["phone"]) == 11 && $info["phone"][0] == '8')
			{
                $info["phone"][0] = '7';
            }
        }
		
		$tax_summ = 0;
		if(! empty($info["payment"]["params"]["robokassa_tax"]))
		{
			switch($info["payment"]["params"]["robokassa_tax"] == "vat10")
			{
				case "vat10":
					$tax_summ = $info["summ"] * 0.1;
					break;
				
				case "vat20":
					$tax_summ = $info["summ"] * 0.2;
					break;
				
				case "vat110":
					$tax_summ = $info["summ"] * 0.1 / 1.1;
					break;
				
				case "vat120":
					$tax_summ = $info["summ"] * 0.2 / 1.2;
					break;
			}
			$tax_summ = $this->format($tax_summ);
		}
		
        $request = array(
			"merchantId" => $info["payment"]["params"]["robokassa_login"],
			"id" => preg_replace('/\D/', '', $info["cashregister_id"]),
			"originId" => $info["payment_id"],
			"operation" => "sell",
			"sno" => ! empty($info["payment"]["params"]["robokassa_sno"]) ? $info["payment"]["params"]["robokassa_sno"] : '',
			"url" => BASE_PATH_HREF,
			"total" => $info["summ"],
            'client' => array(
                'email' => $info["email"],
                'phone' => $info["phone"],
            ),
            'payments' => array(
				array(
					"type" => 2,
					"summ" => $info["summ"],
				),
			),
			'vats' => array(
				array(
					"type" => (! empty($info["payment"]["params"]["robokassa_tax"]) ? $info["payment"]["params"]["robokassa_tax"] : 'none'),
					"sum" => $tax_summ,
				)
			),
            'items' => array(),
        );
        $items = array();
        foreach ($info['rows'] as $row)
        {
            $items[] = array(
				"name" => $row['name'].($row["article"] ? ' '.$row["article"] : ''),
				"quantity" => $row['count'],
				"summ" => $row['price'],
				"tax" => (! empty($info["payment"]["params"]["robokassa_tax"]) ? $info["payment"]["params"]["robokassa_tax"] : 'none'),
				"payment_subject" => (! empty($row["is_delivery"]) ? 'service' : 'commodity'),
				"payment_mode" => "full_payment",
            );
        }
        $request["items"] = $items;
		if(! empty($params["robokassa_test"]))
		{
			 $request["IsTest"] = 1;
		}
		
		Custom::inc('plugins/json.php');
		$base64 = preg_replace('/=*$/', '', base64_encode(str_replace(array('+', '/'), array('-', '_'), json_encode($request, JSON_UNESCAPED_UNICODE))));
		
		$signature = preg_replace('/=*$/', '', base64_encode(strtoupper(md5($base64.$info["payment"]["params"]["robokassa_pass_1"]))));
	
		$response = $this->diafan->fast_request("https://ws.roboxchange.com/RoboFiscal/Receipt/Attach", $base64.".".$signature, $header, false, ( REQUEST_POST | REQUEST_ANSWER ));
		
		$response = json_decode($response, true);

        if (! empty($response["ResultСode"]))
		{
            throw new RobokassaException('Ошибка: '.$response["ResultDescription"], 0);
        }

        return $response['id'];
    }
	
	private function format($summ)
	{
		return number_format($summ, 2, '.', '');
	}
}

class RobokassaException extends Exception{}
