<?php
/**
 * Подключение модуля «Доставка Saferoute»
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

class Delivery_saferoute_inc extends Diafan
{
    /*
	 * @var array данные о заказе (высота, ширина, длина, сумма, вес), идентификационный номер способа доставки ("id"), настройки способа доставки (array "params")
	 */
    public $data;
    
    /*
	 * @var string ошибка выбора опций доставки
	 */
    public $error;
    
    /*
     * Подключает способ доставки
     *
     * @return void
     */
    public function get()
    {
        if(! empty($this->data['weight']) && $this->data['weight_unit'] != 'kg')
        {
            $this->data['weight'] = $this->data['weight'] / 1000;
        }
        $this->data["products"] = array();

        foreach($this->data["cart"]["rows"] as $row)
        {
           if(empty($row["good"]['weight']))
           {
                $row["good"]['weight'] = 0;
           }
            
            $this->data["products"][] = array(
                "name" => $row["good"]["name"._LANG],
                "count" => $row["count"],
                "price" => $row["price"],
                "width" => $row["good"]["width"],
                "height" => $row["good"]['height'],
                "length" => $row["good"]['length'],
                "weight" => ($this->data['weight_unit'] != 'kg' ? $row["good"]['weight'] / 1000 : $row["good"]['weight']),
                "vendorCode" => $row["good"]["article"],
            );
        }
        if(! empty($this->data["cart"]["summ_additional_cost"]))
        {
            $this->data["products"][] = array(
                "name" => $this->diafan->_('Дополнительные услуги', false),
                "count" => 1,
                "price" => $this->data["cart"]["summ_additional_cost"],
            );
        }
        foreach($this->data["rows_param"] as $p)
        {
            if(empty($this->data["user"]["p".$p["id"]]))
            {
                continue;
            }
            $v = $this->data["user"]["p".$p["id"]];
            switch($p["info"])
            {
                case 'street':
                    $this->data["street"] = $v;
                    break;
                case 'building':
                    $this->data["building"] = $v;
                    break;
                case 'suite':
                    $this->data["bulk"] = $v;
                    break;
                case 'flat':
                    $this->data["apartment"] = $v;
                    break;
                case 'phone':
                    $this->data["phone"] = $v;
                    break;
                case 'phone-extra':
                    $this->data["add_phone"] = $v;
                    break;
                case 'email':
                    $this->data["email"] = $v;
                    break;
                case 'city':
                    $this->data['regionName'] = $v;
                    break;
                case "name":
                case "firstname":
                case "lastname":
                case "fathersname":
                    $this->data["fio"] = (! empty($this->data["fio"]) ? $this->data["fio"].' ' : '').$v;
                    break;
            }
        }
        if(empty($_POST["ajax"]) ||  ! empty($_POST["saferoute_kladr"]))
        {
            $this->data["kladr"] = ! empty($this->data["history"]["data"]["city"]["kladr"]) ? $this->data["history"]["data"]["city"]["kladr"] : '';
        }
        if(empty($_POST["ajax"]) ||  ! empty($_POST["saferoute_fias"]))
        {
            $this->data["fias"] = ! empty($this->data["history"]["data"]["city"]["fias"]) ? $this->data["history"]["data"]["city"]["fias"] : '';
        }
        
        $this->diafan->_site->js_view[] = 'https://widgets.saferoute.ru/cart/api.js?new';
    }

    /*
     * Получает стоимость доставки
     *
     * @return mixed
     */
    public function calculate()
    {
        if(! empty($this->data["history"]["summ"]))
        {
            return $this->data["history"]["summ"];
        }
        else
        {
            return false;
        }
    }

    /*
     * Получает данные, введенные пользователем в интерфейсе службы доставки
     *
     * @return string
     */
    public function info()
    {
        if(! empty($this->data["history"]["data"]["_meta"]["fullDeliveryAddress"]))
        {
            return $this->data["history"]["data"]["_meta"]["fullDeliveryAddress"];
        }
    }

    /*
     * Проверяет заданы ли опции доставки
     *
     * @return boolean
     */
    public function valid()
    {
        /*
        if(empty($this->data["history"]["summ"]))
        {
            $this->error = 'Пожалуйста, выберите службу доставки.';
            return false;
        }*/
        
        if($this->data["history"]["status"] != 'complete')
        {
            $this->error = 'Пожалуйста, закончите оформление доставки.';
            return false;
        }
        return true;
    }

	/**
	 * Действие при смене статуса заказа
	 * 
	 * @param integer $status_id
	 * @return void
	 */
	public function set_status($status_id)
	{
		if(empty($this->data["params"]["status"])
           || ! is_array($this->data["params"]["status"])
           || ! in_array($status_id, $this->data["params"]["status"]))
		{
			return;
		}

        $header = array("Authorization:Bearer ".$this->data["params"]["token"], "shop-id:".$this->data["params"]["shop_id"]);
        $data = array(
          "id" =>  $this->data["history"]["service_id"],
          'nppOption' => false,
          "cmsId" => $this->data["history"]["order_id"],
          "status" => $status_id,
        );
        $result = json_decode($this->diafan->fast_request("https://api.saferoute.ru/v2/widgets/update-order", $data, $header, false, ( REQUEST_POST | REQUEST_ANSWER )));
        
        if(! empty($result->cabinetId))
        {
            DB::query("UPDATE {shop_delivery_history} SET service_id=%d WHERE id=%d", $result->cabinetId, $this->data["history"]["id"]);
        }
	}
}
