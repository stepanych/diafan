<?php
/**
 * Подключение модуля «Онлайн касса» для работы с базой данных
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
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
 * Cashregister_inc_db
 */
class Cashregister_inc_db extends Diafan
{
	const TABLE_NAME = 'shop_cashregister';

	/**
	 * Добавляет чек
	 *
	 * @param string $type тип чека: *presell* - предоплата, *sell* - полная оплата, *return* - возврат
	 * @param integer $order_id идентификатор заказа
	 * @param string $payment бэкенд
	 * @param boolean $auto метод отправки уведомления: *false* – ручной, *true* – автоматический
	 */
	public function add($type, $order_id, $payment, $auto = true)
	{
		$auto = $auto ? 1 : 0;

		return $this->diafan->_db_ex->add_new("{".self::TABLE_NAME."}",
			array("`type`", "`order_id`", "`auto`", "`timesent`", "`status`", "`payment`", "`error`", "`trace`", "`timeedit`", "`important`"),
			array("'%h'", "%d", "'%h'", "%d", "'%h'", "'%h'", "'%s'", "'%s'", "%d", "'%d'"),
			array($type, $order_id, $auto, 0, '0', $payment, '', '', time(), 1)
		);
	}

	/**
	 * Возвращает количество чеков, требующих отправки
	 *
	 * @return integer
	 */
	public function count_sent()
	{
		return DB::query_result("SELECT COUNT(*) FROM {".self::TABLE_NAME."} WHERE timesent=%d AND status='%h' AND auto='%h'", 0, 0, 1);
	}
}
