<?php
/**
 * Обработка POST-запросов «Доставка Saferoute»
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

class Delivery_saferoute_action extends Action
{
    /**
     * Определение цены доставки
     */
    public function update()
    {
        $this->diafan->_delivery->set_history('init', $_POST["data"]["delivery"]["totalPrice"], 'saferoute', false, $_POST["data"]);
        
        $this->result["result"] = "success";
    }

    /**
     * Завершение оформления доставки
     */
    public function done()
    {
        $this->diafan->_delivery->set_history('complete', false, 'saferoute', $_POST["data"]["id"], false);
        
        $this->result["result"] = "success";
    }

    /**
     * Ошибка при оформлении
     */
    public function errors()
    {
        $this->diafan->_delivery->set_history('error', false, 'saferoute', false, false);
        $this->result["result"] = "success";
    }
}
