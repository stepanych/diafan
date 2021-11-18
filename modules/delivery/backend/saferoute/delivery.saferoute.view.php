<?php
/**
 * Вывод шаблона «Доставка Saferoute»
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

if(! empty($result["text"]))
{
	echo '<p>'.$result["text"].'</p>';
}
if(empty($result["params"]["token"]) || empty($result["params"]["api_key"]) || empty($result["params"]["shop_id"]))
{
    echo $this->diafan->_('Для работы модуля доставок необходимо <a href="https://saferoute.ru/?ref=diafan">зарегистрироваться в SafeRoute</a> и заполнить параметры подключения.');
    return;
}

echo '
<script type="text/javascript">
diafan_delivery_config["saferoute"] = {
apiScript: "'.BASE_PATH_HREF.'delivery/get/saferoute/api/",
products: [';
foreach($result["products"] as $i => $r)
{
    if($i)
    {
        echo ',';
    }
    echo '{ name: "'.$r["name"].'", count: '.$r["count"].', price: '.$r["price"];
    if(! empty($r['width']))
    {
        echo ', width: '.$r["width"];
    }
    if(! empty($r['height']))
    {
        echo ', height: '.$r["height"];
    }
    if(! empty($r['length']))
    {
        echo ', length: '.$r["length"];
    }
    if(! empty($r['weight']))
    {
        echo ', weight: '.$r["weight"];
    }
    if(! empty($r['vendorCode']))
    {
        echo ', vendorCode: "'.$r["vendorCode"].'"';
    }
    echo '}';
}
echo '],';
if(! empty($result['regionName']))
{
    echo 'regionName: "'.$result['regionName'].'",';
}
if(! empty($result['width']))
{
    echo 'width: '.$result['width'].',';
}
if(! empty($result['height']))
{
    echo 'height: '.$result['height'].',';
}
if(! empty($result['length']))
{
    echo 'length: '.$result['length'].',';
}
if(! empty($result['weight']))
{
    echo 'weight: '.$result['weight'].',';
}
if(! empty($result['fio']))
{
    echo 'userFullName : "'.$result["fio"].'",';
}
if(! empty($result['phone']))
{
    echo 'userPhone : "'.$result["phone"].'",';
}
if(! empty($result['add_phone']))
{
    echo 'userAdditPhone : "'.$result["add_phone"].'",';
}
if(! empty($result['email']))
{
    echo 'userEmail : "'.$result["email"].'",';
}
if(! empty($result['street']))
{
    echo 'userAddressStreet : "'.$result["street"].'",';
}
if(! empty($result['building']))
{
    echo 'userAddressBuilding : "'.$result["building"].'",';
}
if(! empty($result['bulk']))
{
    echo 'userAddressBulk : "'.$result["bulk"].'",';
}
if(! empty($result['apartment']))
{
    echo 'userAddressApartment : "'.$result["apartment"].'",';
}
if(! empty($result['kladr']))
{
    echo 'kladr : "'.$result["kladr"].'",';
}
if(! empty($result['fias']))
{
    echo 'fias : "'.$result["fias"].'",';
}
echo '
};
</script>';
if(! empty($result['fias']))
{
    echo '<input type="hidden" name="saferoute_fias" value="'.$result["fias"].'">';
}
if(! empty($result['kladr']))
{
    echo '<input type="hidden" name="saferoute_kladr" value="'.$result["kladr"].'">';
}