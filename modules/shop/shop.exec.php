<?php
/**
 * Обрабатывает полученные данные из формы
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

class Shop_exec extends Exec
{
	/**
	 * Инициализация API модуля
	 *
	 * @return void
	 */
	public function init()
	{
		switch($this->method)
		{
			case 'price_calc':
				$this->price_calc();
				break;

			default:
				$this->set_error();
				break;
		}
	}

	/**
	 * Пересчёт всех цен
	 *
	 * @return void
	 */
	private function price_calc()
	{
		$limit = 1024; // TO_DO: лимит обработки элементов за одну итерацию

		$good_id = $this->diafan->filter($_POST, 'integer', 'good_id');
		$discount_id = $this->diafan->filter($_POST, 'integer', 'discount_id');
		$currency_id = $this->diafan->filter($_POST, 'integer', 'currency_id');
		if(! $good_id)
		{
			$last_id = $this->diafan->filter($_POST, 'integer', 'last_id');
			$this->max_iteration = (int) DB::query_result("SELECT COUNT(*) FROM {shop} WHERE trash='%d'", 0);
			$this->iteration = (int) DB::query_result("SELECT COUNT(*) FROM {shop} WHERE trash='%d' AND id<=%d", 0, $last_id);

			if($ids = DB::query_fetch_value(
				"SELECT id FROM {shop} WHERE trash='%d' AND id>%d GROUP BY id ORDER BY id ASC LIMIT %d",
				0, $last_id, $limit, "id")
			)
			{
				foreach ($ids as $id)
				{
					$this->iteration = $this->iteration + 1;
					$last_id = $id;
					$this->diafan->_shop->price_calc($id, $discount_id, $currency_id);
				}
				$this->post = $_POST;
				$this->post["last_id"] = $last_id;
				$this->repeat = $this->iteration < $this->max_iteration;
			}
		}
		else $this->diafan->_shop->price_calc($good_id, $discount_id, $currency_id);
	}
}
