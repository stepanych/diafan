<?php
/**
 * Модель модуля «Корзина товаров, оформление заказа»
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

class Cart_model extends Model
{
	/**
	 * Генерирует данные для формы оформления заказов
	 *
	 * @return void
	 */
	public function form()
	{
		$this->result['form_tag'] = 'cart';
		$this->form_errors($this->result, $this->result['form_tag'], array(''));

		$this->form_table();
		$this->form_registration();
		$this->form_param();

		$this->result["payments"] = $this->diafan->_payment->get_all();
		if($this->result["payments"])
		{
			foreach($this->result["payments"] as $i => $row)
			{
				if($row["payment"] == 'balance')
				{
					if($this->diafan->_balance->get() < ceil($this->result["summ"]))
					{
						unset($this->result["payments"][$i]);
						continue;
					}
				}
				if(! empty($this->result["discounts"]["payments"][$row["id"]]))
				{
					$this->result["payments"][$i]["discount_total"] = $this->result["discounts"]["payments"][$row["id"]];
					$this->result["payments"][$i]["discount_total"]["currency"] = $this->diafan->configmodules("currency", "shop");
				}
				$this->result["payments"][$i]["selected"] = (! $i ? true : false);
			}
		}
		$this->yandex_fast_order();
		$this->result["view"] = 'form';
	}

	/**
	 * Генерирует таблицу купленных товаров
	 *
	 * @param integer $payment_id идентификатор текущего метода оплаты
	 * @return array
	 */
	public function form_table($payment_id = 0)
	{
		$this->result["currency"] = $this->diafan->configmodules("currency", "shop");
		$this->result["summ_goods"] = 0;
		$this->result["summ"] = 0;
		$this->result["count"] = 0;
		$this->result["discount"] = false;

		// корзина
		$cart = $this->diafan->_cart->get($payment_id);
		if (empty($cart["rows"]))
		{
			$this->result["shop_link"] = $this->diafan->_route->module('shop');
			return $this->result;
		}
		foreach($cart as $k => $v)
		{
			$this->result[$k] = $v;
		}

		$this->prepare_form_data();

		$share_link = array();

		foreach ($this->result["rows"] as $i => &$row)
		{
			$row["name"] = $row["good"]["name"._LANG];
			$row["good_name"] = $row["name"];
			$row["article"] = $row["good"]["article"];
			$row["measure_unit"] = $row["good"]["measure_unit"._LANG];
			if($row["measure_unit"])
			{
				$this->result["measure_unit"] = true;
			}

			// харатеристики, учитываемые при заказе
			$query = array();
			foreach ($row["params"] as $p)
			{
				if(empty($this->cache["params_select"][$p]))
					continue;

				$p_id = $this->cache["params_select"][$p]["param_id"];

				if(empty($this->cache["params"][$p_id]))
					continue;

				$query[] = 'p'.$p_id.'='.$p;
				$row["params_name"][] = array(
					"name" => $this->cache["params"][$p_id]["name"],
					"value" => $this->cache["params_select"][$p]["name"],

				);
				$row["name"] .= ', '.$this->cache["params"][$p_id]["name"].': '.$this->cache["params_select"][$p]["name"];
			}
			$share_link[] = $row["good"]["id"]
			.'|pr='.$row["price_id"]
			.($row["params"] ? '|p='.implode(',', $row["params"]) : '')
			.($row["additional_costs"] ? '|a='.implode(',', $row["additional_costs"]) : '')
			.($row["count"] <> 1 ? '|c='.$row["count"] : '');

			// ссылка
			$row["link"] = $this->diafan->_route->link($row["good"]["site_id"], $row["good"]["id"], "shop").(! empty($query) ? '?'.implode('&amp;', $query) : '');

			// категория
			$row["cat"] = ($row["good"]["cat_id"] && ! empty($this->cache["cats"][$row["good"]["cat_id"]]) ? $this->cache["cats"][$row["good"]["cat_id"]] : false);

			$row["cats"] = ($row["good"]["cat_id"] && ! empty($this->cache["parent_cats"][$row["good"]["cat_id"]]) ? $this->cache["parent_cats"][$row["good"]["cat_id"]] : false);

			// производитель
			$row["brand"] = ($row["good"]["brand_id"] && ! empty($this->cache["brands"][$row["good"]["brand_id"]]) ? $this->cache["brands"][$row["good"]["brand_id"]] : false);

			// изображения
			if($img = $this->diafan->_images->get('medium', $row["good"]["id"], 'shop', 'element', $row["good"]["site_id"], $row["good"]["name"._LANG]))
			{
				if(! empty($this->cache["prices_image_rel"][$row["price_id"]]))
				{
					foreach ($img as $i)
					{
						if($i["id"] == $this->cache["prices_image_rel"][$row["price_id"]])
						{
							$row["img"] = $i;
						}
					}
				}
				if(empty($row["img"]))
				{
					$row["img"] = $img[0];
				}
			}

			// скидка на товар
			if(! empty($row["discount_summ"]))
			{
				$this->result["discount"] = true;
			}
		}
		$this->result["share_link"] = $this->diafan->_route->current_link().'?share='.base64_encode(implode('&', $share_link).($cart["cart_additional_cost"] ? '&a|'.implode('|', $cart["cart_additional_cost"]) : ''));

		$this->result["delivery"] = $this->diafan->_delivery->get_all($cart["cart_delivery"], $this->result["summ"]);
		$this->result["cart_delivery"] = $cart["cart_delivery"];
		
		foreach ($this->result["delivery"] as &$d)
		{
			if ($d["selected"])
			{
				$this->result["summ"] += $d['price'];
			}
			if(! empty($this->result["discounts"]["delivery"][$d["id"]]))
			{
				$d["discount_total"] = $this->result["discounts"]["delivery"][$d["id"]];
				$d["discount_total"]["currency"] = $this->diafan->configmodules("currency", "shop");
			}
		}
		// налог
		if($this->diafan->configmodules('tax', 'shop'))
		{
			$tax = $this->diafan->configmodules('tax', 'shop');
			$this->result["tax"] = $this->result["summ"] * $tax / (100 + $tax);
			$this->result["tax_name"] = $this->diafan->configmodules('tax_name', 'shop');
		}
		$this->format_prices();
		return $this->result;
	}

	/**
	 * Подготоваливает данные из базы данных для формирования списка товаров в корзине
	 *
	 * @return void
	 */
	private function prepare_form_data()
	{
		// все товары одним запросом
		$good_ids = array();
		$cat_ids = array();
		$brand_ids = array();
		foreach($this->result["rows"] as $row)
		{
			if(! in_array($row["good"]["id"], $good_ids))
			{
				$good_ids[] = $row["good"]["id"];
			}
			if(! in_array($row["good"]["cat_id"], $cat_ids))
			{
				$cat_ids[] = $row["good"]["cat_id"];
			}
			if(! in_array($row["good"]["cat_id"], $brand_ids))
			{
				$brand_ids[] = $row["good"]["brand_id"];
			}
		}

		// все родители категорий
		$cat_parents = DB::query_fetch_value("SELECT parent_id FROM {shop_category_parents} WHERE element_id IN (%s) AND trash='0'", implode(",", $cat_ids), "parent_id");
		$cat_ids = array_unique(array_merge($cat_parents, $cat_ids));

		// все категории одним запросом
		$this->cache["cats"] = DB::query_fetch_key("SELECT id, [name], site_id, parent_id FROM {shop_category} WHERE [act]='1' AND id IN (%s) AND trash='0'", implode(",", $cat_ids), "id");

		// все производители одним запросом
		$this->cache["brands"] = DB::query_fetch_key("SELECT id, [name], site_id FROM {shop_brand} WHERE [act]='1' AND id IN (%s) AND trash='0'", implode(",", $brand_ids), "id");

		// все значения характеристик одним запросом
		$param_select_ids = $this->diafan->filter(array_unique(explode(',', implode(',', $this->diafan->array_column($this->result["rows"], "param")))), "int");
		$this->cache["params_select"] = DB::query_fetch_key("SELECT id, param_id, [name] FROM {shop_param_select} WHERE id IN (%s) AND trash='0'", implode(",", $param_select_ids), "id");

		// все характеристики одним запросом
		$param_ids = array_unique($this->diafan->array_column($this->cache["params_select"], "param_id"));
		if($param_ids)
		{
			$this->cache["params"] = DB::query_fetch_key("SELECT id, [name] FROM {shop_param} WHERE id IN (%s) AND trash='0'", implode(",", $param_ids), "id");
		}

		// все связи цен и изображений одним запросом
		$price_ids = array_unique($this->diafan->array_column($this->result["rows"], "price_id"));
		$this->cache["prices_image_rel"] = DB::query_fetch_key_value("SELECT price_id, image_id FROM {shop_price_image_rel} WHERE price_id IN (%s)", implode(",", $price_ids), "price_id", "image_id");

		// подготовка ссылок для категории
		foreach($this->cache["cats"] as &$cat)
		{
			$this->diafan->_route->prepare($cat["site_id"], $cat["id"], "shop", 'cat');
		}

		// подготовка ссылок для производителей
		foreach($this->cache["brands"] as &$brand)
		{
			$this->diafan->_route->prepare($brand["site_id"], $brand["id"], "shop", 'brand');
		}
		// подготовка данных о товаре
		foreach($this->result["rows"] as $row)
		{
			$this->diafan->_route->prepare($row["good"]["site_id"], $row["good"]["id"], "shop");
			$this->diafan->_images->prepare($row["good"]["id"], 'shop', 'element');
		}

		// подготовка ссылок для категории
		foreach($this->cache["cats"] as &$cat)
		{
			$cat["link"] = $this->diafan->_route->link($cat["site_id"], $cat["id"], "shop", 'cat');
		}
		foreach($this->cache["cats"] as &$cat)
		{
			$i = 0;
			$c = $cat;
			$cats = array();
			while($c["parent_id"] && $i < 5)
			{
				$cats[] = $c;
				$c = $this->cache["cats"][$c["parent_id"]];
				$i++;
			}
			$cats[] = $c;
			$this->cache["parent_cats"][$cat["id"]] = array_reverse($cats);
		}

		// подготовка ссылок для производителей
		foreach($this->cache["brands"] as &$brand)
		{
			$brand["link"] = $this->diafan->_route->link($brand["site_id"], $brand["id"], "shop", 'brand');
		}
	}

	/**
	 * Форматирует цены как указано в настройках магазина
	 *
	 * @return void
	 */
	private function format_prices()
	{
		foreach($this->result["rows"] as &$row)
		{
			$row["summ"] = $this->diafan->_shop->price_format($row["price"] * $row["count"]);
			$row["price"] = $this->diafan->_shop->price_format($row["price"]);
			$row["old_price"] = ! empty($row["old_price"]) ? $this->diafan->_shop->price_format($row["old_price"]) : 0;
			foreach($row["additional_cost"] as &$a)
			{
				$a["format_summ"] = ($a["summ"] ? $this->diafan->_shop->price_format($a["summ"]) : 0);
			}
			$row["discount"] = '';
			if(! empty($row["discount_summ"]))
			{
				$row["discount_summ"] = $this->diafan->_shop->price_format($row["discount_summ"]);

				// поддержка старого формата
				if($row["percent"])
				{
					$row["discount"] = $row["percent"].'%';
				}
				else
				{
					$row["discount"] = $row["discount_summ"].' '.$this->result["currency"];
				}
			}
		}
		foreach ($this->result["additional_cost"] as &$a)
		{
			$a["summ"] = $this->diafan->_shop->price_format($a["summ"]);
		}
		foreach ($this->result["delivery"] as &$d)
		{
			if($d['price'])
			{
				$this->diafan->_shop->price_format($d['price']);
			}
			foreach ($d["thresholds"] as &$d_th)
			{
				$d_th['price'] = $this->diafan->_shop->price_format($d_th['price'], true);
			}
		}
		// следующая скидка
		if(! empty($this->result["discount_next"]))
		{
			$this->result["discount_next"]["summ"] = $this->diafan->_shop->price_format($this->result["discount_next"]["summ"]);
			$this->result["discount_next"]["discount_summ"] = (! empty($this->result["discount_next"]["discount_summ"]) ? $this->diafan->_shop->price_format($this->result["discount_next"]["discount_summ"]) : 0);
			// поддержка старого формата
			if(! empty($this->result["discount_next"]["percent"]))
			{
				$this->result["discount_next"]["discount"] = $this->result["discount_next"]["percent"].'%';
			}
			else
			{
				$this->result["discount_next"]["discount"] = $this->result["discount_next"]['discount_summ'].' '.$this->result['currency'];
			}
		}
		if(! empty($this->result["discount_total"]))
		{
			$this->result["discount_total"]["discount_summ"] = (! empty($this->result["discount_total"]["discount_summ"]) ? $this->diafan->_shop->price_format($this->result["discount_total"]["discount_summ"]) : 0);
			// поддержка старого формата
			if(! empty($this->result["discount_total"]["percent"]))
			{
				$this->result["discount_total"]["discount"] = $this->result["discount_total"]["percent"].'%';
			}
			else
			{
				$this->result["discount_total"]["discount"] = $this->result["discount_total"]['discount_summ'].' '.$this->result['currency'];
			}
		}
		// налог
		if(! empty($this->result["tax"]))
		{
			$this->result["tax"] = $this->diafan->_shop->price_format($this->result["tax"]);
		}
		$this->result["summ"] = $this->diafan->_shop->price_format($this->result["summ"]);
		$this->result["summ_goods"] = ! empty($this->result["summ_goods"]) ? $this->diafan->_shop->price_format($this->result["summ_goods"]) : 0;
		$this->result["old_summ_goods"] = ! empty($this->result["old_summ_goods"]) ? $this->diafan->_shop->price_format($this->result["old_summ_goods"]) : 0;
	}

	/**
	 * Генерирует форму регистрации и авторизации
	 *
	 * @return void
	 */
	private function form_registration()
	{
		$this->result["show_auth"] = true;
		if ($this->diafan->_users->id || ! DB::query_result("SELECT id FROM {site} WHERE module_name='registration' AND [act]='1' AND trash='0' LIMIT 1"))
		{
			$this->result["show_auth"] = false;
		}
		else
		{
			Custom::inc('modules/registration/registration.model.php');
			$reg = new Registration_model($this->diafan);
			$reg->form();
			$this->result["registration"] = $reg->result;
			$this->result["registration"]["action"] = BASE_PATH_HREF.$this->diafan->_route->module("registration");
			$show_login = array("error" => $this->diafan->_users->errauth ? $this->diafan->_('Неверный логин или пароль.', false) : '', "action" => '', "user" => '', 'hide' => true);

			$this->result["show_login"] = $show_login;
		}
	}

	/**
	 * Генерирует поля формы, созданные в конструкторе
	 *
	 * @param boolean $one_click сокращенная форма для быстрого заказа
	 * @return void
	 */
	private function form_param($one_click = false)
	{
		$result = $this->diafan->_cart->get_form_param($one_click);
		$this->result["rows_param"] = $result["rows_param"];
		$this->result["fields"] = $result["fields"];
		$this->result["user"] = $result["user"];

		if($this->diafan->configmodules('subscribe_in_order', 'subscription'))
		{
			$this->result['subscribe_in_order'] = true;
		}
		if($this->result["fields"] && ! empty($this->result['form_tag']))
		{
			$this->form_errors($this->result, $this->result['form_tag'], $this->result["fields"]);
		}
	}

	/**
	 * Интеграция с серсивом "Яндекс.Быстрый заказ"
	 *
	 * @return void
	 */
	private function yandex_fast_order()
	{
		if(! $this->diafan->configmodules('yandex_fast_order', 'shop'))
		{
			return;
		}
		$this->result["yandex_fast_order"] = true;
		$this->result["yandex_fast_order_link"] =  'http'.(IS_HTTPS ? "s" : '').'://market.yandex.ru/addresses.xml?callback='
		.urlencode(BASE_PATH_HREF.$this->diafan->_route->current_link().'?yandex_fast_order=true')
		.'&size=mini';
		if(! empty($_POST["operation_id"]))
		{
			foreach ($this->result["rows_param"] as $i => $row)
			{
				if(! $row["info"])
				{
						continue;
				}
				switch($row["info"])
				{
					case 'address':
						if(! empty($_POST["street"]) || ! empty($_POST["building"]) || ! empty($_POST["suite"]) || ! empty($_POST["flat"]) || ! empty($_POST["entrance"]) || ! empty($_POST["intercom"]) || ! empty($_POST["city"]) || ! empty($_POST["country"]) || ! empty($_POST["zip"]) || ! empty($_POST["metro"]))
						{
							$this->result["user"]['p'.$row["id"]] = '';
							if(! empty($_POST["zip"]))
							{
								$this->result["user"]['p'.$row["id"]] = $this->diafan->filter($_POST, "string", "zip");
							}
							if(! empty($_POST["country"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->filter($_POST, "string", "country");
							}
							if(! empty($_POST["city"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->filter($_POST, "string", "city");
							}
							if(! empty($_POST["metro"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->_('станция метро', false).' '.$this->diafan->filter($_POST, "string", "metro");
							}
							if(! empty($_POST["street"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->filter($_POST, "string", "street");
							}
							if(! empty($_POST["suite"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->filter($_POST, "string", "suite");
							}
							if(! empty($_POST["building"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->_('дом', false).' '.$this->diafan->filter($_POST, "string", "building");
							}
							if(! empty($_POST["suite"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->_('корпус', false).' '.$this->diafan->filter($_POST, "string", "suite");
							}
							if(! empty($_POST["flat"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->_('кв.', false).' '.$this->diafan->filter($_POST, "string", "flat");
							}
							if(! empty($_POST["entrance"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->_('этаж', false).' '.$this->diafan->filter($_POST, "string", "entrance");
							}
							if(! empty($_POST["intercom"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? "\n" : '').$this->diafan->_('домофон', false).' '.$this->diafan->filter($_POST, "string", "intercom");
							}
						}
						break;
					case 'name':
						if(! empty($_POST["firstname"]) || ! empty($_POST["lastname"]) || ! empty($_POST["fathersname"]))
						{
							$this->result["user"]['p'.$row["id"]] = '';
							if(! empty($_POST["firstname"]))
							{
								$this->result["user"]['p'.$row["id"]] = $this->diafan->filter($_POST, "string", "firstname");
							}
							if(! empty($_POST["fathersname"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? ' ' : '').$this->diafan->filter($_POST, "string", "fathersname");
							}
							if(! empty($_POST["lastname"]))
							{
								$this->result["user"]['p'.$row["id"]] = ($this->result["user"]['p'.$row["id"]] ? ' ' : '').$this->diafan->filter($_POST, "string", "lastname");
							}
						}
						break;
					default:
						if(! empty($_POST[$row["info"]]))
						{
							$this->result["user"]['p'.$row["id"]] = $this->diafan->filter($_POST, "string", $row["info"]);
						}
						break;
					}
			}
		}
	}

	/**
	 * Генерирует данные для второго шага в оформлении заказа: оплата
	 *
	 * @return void
	 */
	public function payment()
	{
		if(empty($_GET["code"]))
		{
			Custom::inc('includes/404.php');
		}
		$this->result = $this->diafan->_payment->get_pay($this->diafan->_route->show, 'cart', $_GET["code"]);
		$this->result["view"] = "payment";
	}

	/**
	 * Генерирует данные для третьего шага в оформлении заказа: результат оплаты
	 *
	 * @return void
	 */
	public function result()
	{
		if ($this->diafan->_route->step == 3)
		{
			if($this->diafan->configmodules('order_redirect', 'shop'))
			{
				if(preg_match("/^([0-9]+)$/", $this->diafan->configmodules('order_redirect', 'shop')))
				{
					$this->result["redirect"] = BASE_PATH_HREF.$this->diafan->_route->link($this->diafan->configmodules('order_redirect', 'shop'));
				}
				else
				{
					$this->result["redirect"] = BASE_PATH_HREF.$this->diafan->configmodules('order_redirect', 'shop').ROUTE_END;
				}
			}
			$this->result["text"] = $this->diafan->configmodules("payment_success_text", "shop");
		}
		else
		{
			$this->result["text"] = $this->diafan->configmodules("payment_fail_text", "shop");
		}
		$this->result["view"] = "result";
	}

	/**
	 * Генерирует страницу корзину, доступную по ссылке
	 *
	 * @return void
	 */
	public function share()
	{
		$this->result["shop_link"] = $this->diafan->_route->module('shop');
		$this->result["view"] = 'share';
		$this->diafan->_site->text = '';
		$this->diafan->_site->name = $this->diafan->_('Сохраненная корзина', false);

		

		$share = $this->diafan->_cart->prepare_share_rows($_GET["share"]);
		if(empty($share["rows"]))
		{
			return;
		}
		$this->result["rows"] = $share["rows"];
		$this->result["additional_costs"] = $share["additional_costs"];
		$this->result["share"] = $share["share"];

		$this->result["currency"] = $this->diafan->configmodules("currency", "shop");
		$this->result["summ_goods"] = 0;
		$this->result["count"] = 0;
		$this->result["discount"] = false;

        $this->share_prepare_get_data();

        foreach ($this->result["rows"] as &$row)
        {
            $row["good"] = $this->cache["share"]["goods"][$row["good_id"]];

            // цена
            $row["price"] = 0; $row["old_price"] = 0;
            if($row_price = $this->diafan->_shop->price_get_id($row["price_id"]))
            {
                $row_price["price"] = $this->diafan->_shop->price_format($row_price["price"], true);
                $row_price["old_price"] = $this->diafan->_shop->price_format($row_price["old_price"], true);
                $row["price_count"] = $this->diafan->configmodules('use_count_goods', 'shop') ? $row_price["count_goods"] : 0;
                $row["price"] = $row_price["price"];
                $row["old_price"] = $row_price["old_price"];
            }
            if($row["good"]["is_file"])
            {
                $row["price_count"] = 1;
            }

            // сопутствующие услуги для товара
            $row["additional_cost"] = array();
            foreach($row["additional_costs"] as $a)
            {
                if(empty($this->cache["share"]["additional_cost_rels"][$a.'_'.$row["good_id"]]))
                    continue;

                $a_c_rel = $this->cache["share"]["additional_cost_rels"][$a.'_'.$row["good_id"]];
                $a_c_rel["price"] = $this->diafan->_shop->price_format($a_c_rel["price"], true);
                if($a_c_rel["amount"] && $a_c_rel["amount"] <= $row["price"])
                {
                    $a_c_rel["summ"] = 0;
                }
                elseif($a_c_rel["percent"])
                {
                    $a_c_rel["summ"] = ($row_price["price"] * $a_c_rel["percent"]) / 100;
                }
                elseif(! $a_c_rel["summ"])
                {
                    $a_c_rel["summ"] = $a_c_rel["price"];
                }
                if($a_c_rel["summ"])
                {
                    $a_c_rel["summ"] = $this->diafan->_shop->price_format($a_c_rel["summ"], true);
                    $row["price"] += $a_c_rel["summ"];
                    if ($row["old_price"]) $row["old_price"] += $a_c_rel["summ"];
                }
                $row["additional_cost"][] = $a_c_rel;
            }

            // скидка на товар
            $row["discount_id"] = $row_price["discount_id"];
            $row["discount_summ"] = 0;
            $row["percent"] = 0;
            if($row_price["discount_id"] && ! empty($this->cache["share"]["discounts"][$row_price["discount_id"]]))
            {
                $discount = $this->cache["share"]["discounts"][$row_price["discount_id"]];
                if(! empty($discount["deduction"]))
                {
                    $row["percent"] = 0;
                    $row["discount_summ"] = $discount["deduction"];
                }
                else
                {
                    $row["percent"] = $discount["discount"];
                    $row["discount_summ"] = $row_price["price"] / (100 - $discount["discount"]) * $discount["discount"];
                }
            }
            elseif($row_price["old_price"] && $row_price["old_price"] > $row_price["price"])
            {
                $row["discount_summ"] = $row_price["old_price"] - $row_price["price"];
            }

            $this->result["summ_goods"] += $row["price"] * $row["count"];
            $this->result["count"] += $row["count"];
        }
        if($this->diafan->configmodules('method_count', 'order') == '1')
        {
            $this->result["count"] = count($this->result["rows"]);
        }
        $this->result["summ"] = $this->result["summ_goods"];

        // сопутствующие услуги
        $this->result["summ_additional_cost"] = 0;
        $this->result["additional_cost"] = $this->cache["share"]["additional_costs"];
        foreach ($this->result["additional_cost"] as &$a)
        {
            $a['price'] = $this->diafan->_shop->price_format($a['price'], true);
            $a["summ"] = $a['price'];
            if($a['percent'])
            {
                $a["summ"] = $this->result["summ_goods"] * $a['percent'] / 100;
            }
            if (! empty($a['amount']))
            {
                if ($a['amount'] <= $this->result["summ_goods"])
                {
                    $a["summ"] = 0;
                }
            }
            $this->result["summ_additional_cost"] += $a['summ'];
        }
        $this->result["summ"] += $this->result["summ_additional_cost"];

		if (empty($this->result["rows"]))
		{
			return;
		}

		$this->prepare_form_data();

		foreach ($this->result["rows"] as &$row)
		{
			$row["name"] = $row["good"]["name"._LANG];
			$row["good_name"] = $row["name"];
			$row["article"] = $row["good"]["article"];
			$row["measure_unit"] = $row["good"]["measure_unit"._LANG];
			if($row["measure_unit"])
			{
				$this->result["measure_unit"] = true;
			}

			// харатеристики, учитываемые при заказе
			$query = array();
			foreach ($row["params"] as $p)
			{
				if(empty($this->cache["params_select"][$p]))
					continue;

				$p_id = $this->cache["params_select"][$p]["param_id"];

				if(empty($this->cache["params"][$p_id]))
					continue;

				$query[] = 'p'.$p_id.'='.$p;
				$row["params_name"][] = array(
					"name" => $this->cache["params"][$p_id]["name"],
					"value" => $this->cache["params_select"][$p]["name"],

				);
				$row["name"] .= ', '.$this->cache["params"][$p_id]["name"].': '.$this->cache["params_select"][$p]["name"];
			}

			// ссылка
			$row["link"] = $this->diafan->_route->link($row["good"]["site_id"], $row["good"]["id"], "shop").(! empty($query) ? '?'.implode('&amp;', $query) : '');

			// категория
			$row["cat"] = ($row["good"]["cat_id"] && ! empty($this->cache["cats"][$row["good"]["cat_id"]]) ? $this->cache["cats"][$row["good"]["cat_id"]] : false);

			$row["cats"] = ($row["good"]["cat_id"] && ! empty($this->cache["parent_cats"][$row["good"]["cat_id"]]) ? $this->cache["parent_cats"][$row["good"]["cat_id"]] : false);

			// производитель
			$row["brand"] = ($row["good"]["brand_id"] && ! empty($this->cache["brands"][$row["good"]["brand_id"]]) ? $this->cache["brands"][$row["good"]["brand_id"]] : false);

			// изображения
			if($img = $this->diafan->_images->get('medium', $row["good"]["id"], 'shop', 'element', $row["good"]["site_id"], $row["good"]["name"._LANG]))
			{
				if(! empty($this->cache["prices_image_rel"][$row["price_id"]]))
				{
					foreach ($img as $i)
					{
						if($i["id"] == $this->cache["prices_image_rel"][$row["price_id"]])
						{
							$row["img"] = $i;
						}
					}
				}
				if(empty($row["img"]))
				{
					$row["img"] = $img[0];
				}
			}

			// скидка на товар
			if(! empty($row["discount_summ"]))
			{
				$this->result["discount"] = true;
			}
		}
		foreach($this->result["rows"] as &$row)
		{
			$row["summ"] = $this->diafan->_shop->price_format($row["price"] * $row["count"]);
			$row["price"] = $this->diafan->_shop->price_format($row["price"]);
			$row["old_price"] = ! empty($row["old_price"]) ? $this->diafan->_shop->price_format($row["old_price"]) : 0;
			foreach($row["additional_cost"] as &$a)
			{
				$a["format_summ"] = ($a["summ"] ? $this->diafan->_shop->price_format($a["summ"]) : 0);
			}
			$row["discount"] = '';
			if(! empty($row["discount_summ"]))
			{
				$row["discount_summ"] = $this->diafan->_shop->price_format($row["discount_summ"]);

				// поддержка старого формата
				if($row["percent"])
				{
					$row["discount"] = $row["percent"].'%';
				}
				else
				{
					$row["discount"] = $row["discount_summ"].' '.$this->result["currency"];
				}
			}
		}
		$this->result["summ_goods"] = ! empty($this->result["summ_goods"]) ? $this->diafan->_shop->price_format($this->result["summ_goods"]) : 0;

		$cart = $this->diafan->_cart->get();
		$this->result["is_cart"] = (! empty($cart["rows"]) ? true : false);
	}

	/**
	 * Подготоваливает данные из базы данных для формирования списка товаров в корзине по ссылке
	 *
	 * @return void
	 */
	private function share_prepare_get_data()
	{
		// все товары одним запросом
		$good_ids = array_unique($this->diafan->array_column($this->result["rows"], "good_id"));
		$this->cache["share"]["goods"] = DB::query_fetch_key("SELECT * FROM {shop} WHERE [act]='1' AND id IN (%s) AND trash='0'", implode(",", $good_ids), "id");

		// все связи цен и изображений одним запросом
		$price_ids = array_unique($this->diafan->array_column($this->result["rows"], "price_id"));

		// все сопутствующие услуги товаров одним запросом
		$this->cache["share"]["additional_cost_rels"] = DB::query_fetch_key("SELECT a.id, a.[name], a.percent, a.price, a.amount, r.element_id, r.summ, CONCAT(a.id, '_', r.element_id) AS `key` FROM {shop_additional_cost} AS a INNER JOIN {shop_additional_cost_rel} AS r ON r.additional_cost_id=a.id WHERE r.element_id IN (%s) AND a.trash='0'", implode(',', $good_ids), "key");

		$this->cache["share"]["additional_costs"] = array();
		if($this->result["additional_costs"])
		{
			// все сопутствующие услуги для заказа одним запросом
			$this->cache["share"]["additional_costs"] = DB::query_fetch_all("SELECT id, [name], price, percent, [text], amount FROM {shop_additional_cost} WHERE [act]='1' AND trash='0' AND shop_rel='0' AND id IN (%s) ORDER by sort ASC", implode(',', $this->result["additional_costs"]));
		}

		// подготовка цен, характеристик и услуг
		foreach ($this->result["rows"] as $row)
		{
			$this->diafan->_shop->price_prepare_id($row["price_id"]);
		}

		$discount_ids = array();

        // актуализация товаров в корзине
		foreach ($this->result["rows"] as $i => $row)
		{
			if(empty($this->cache["share"]["goods"][$row["good_id"]]))
			{
				unset($this->result["rows"][$i]);
				continue;
			}
            $row["good"] = $this->cache["share"]["goods"][$row["good_id"]];

			$row["price"] = $this->diafan->_shop->price_get_id($row["price_id"]);
            if (empty($row["price"]))
			{
                $wparam = array();
                if($row["param"])
                {
                    $wparam = DB::query_fetch_key_value("SELECT id, param_id FROM {shop_param_select} WHERE id IN(%s)", $row["param"], "param_id", "id");
                }
                if($row["price"] = $this->diafan->_shop->price_get($row["good_id"], $wparam))
                {
                    $row["price_id"] = $row["price"]["price_id"];
                }
                else
                {
                    continue;
                }
			}
            if($row["good"]["is_file"] && $row["count"] > 1)
            {
                $row["count"] = 1;
            }
			if($row["price"]["discount_id"] && ! in_array($row["price"]["discount_id"], $discount_ids))
			{
				$discount_ids[] = $row["price"]["discount_id"];
			}
			if($this->diafan->configmodules("use_count_goods", "shop"))
			{
                $count_price_id = 0;
				foreach ($this->cache["db_goods"] as $r)
                {
                    if($r["price_id"] == $row["price_id"] && $r["id"] != $row["id"])
                    {
                        $count_price_id += $r["count"];
                    }
                }
				if($row["price"]["count_goods"] < $row["count"] + $count_price_id)
				{
                    // если товара на скадале недостаточно, оставляем в корзине столько сколько есть на складе
                    if($row["price"]["count_goods"] > $count_price_id)
                    {
                        $row["count"] = $row["price"]["count_goods"] - $count_price_id;
                    }
				}
			}
		}

		// запрашиваем все скидки на товары
		if($discount_ids)
		{
			$this->cache["share"]["discounts"] = DB::query_fetch_key("SELECT id, discount, deduction FROM {shop_discount} WHERE id IN (%s)", implode(",", $discount_ids), "id");
		}
	}

	/**
	 * Генерирует данные для шаблонной функции: выводит информацию о заказанных товарах
	 *
	 * @param boolean $tag функция вызвана при генерировании шаблонного тега
	 * @return array
	 */
	public function show_block($tag = false)
	{
		$link = $this->diafan->_route->module("cart");
		if($link)
		{
			if($this->diafan->_site->module != 'cart')
			{
				$result = $this->form_table();
				$result['form_tag'] = 'cart_block_form';
				$this->form_errors($result, $result['form_tag'], array(''));
			}
			$result["count"] = $this->diafan->_cart->get_count();
			$result["summ"] = $this->diafan->_shop->price_format($this->diafan->_cart->get_summ());

			$result["link"] = BASE_PATH_HREF.$link.'?'.rand(0, 999999);
			$result["currency"] = $this->diafan->configmodules("currency", "shop");
			return $result;
		}
	}

	/**
	 * Генерирует данные для шаблонной функции: выводит информацию о последнем совершенном заказе
	 *
	 * @return array
	 */
	public function show_last_order()
	{
		if(! $order_id = $this->diafan->_cart->get_last_order())
		{
			return;
		}
		$result = $this->diafan->_order->get($order_id);
		$result["param"] = $this->diafan->_order->get_param($order_id);

		return $result;
	}

	/**
	 * Генерирует данные для формы быстрого заказа
	 *
	 * @return array
	 */
	public function one_click()
	{
		$this->result['form_tag'] = 'cart_one_click';
		$this->form_errors($this->result, $this->result['form_tag'], array(''));
		$this->form_param(true);
		return $this->result;
	}
}
