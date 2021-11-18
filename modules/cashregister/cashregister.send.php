<?php
/**
 * Отправка чеков
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

/**
 * Cashregister_send
 */
class Cashregister_send extends Diafan
{
	/**
	 * Максимальное количество отсылаемых чеков за один проход
	 */
	const LIMIT = 1;

	/**
	 * Инициирует отправку чеков
	 *
	 * @return void
	 */
	public function init()
	{
		$this->diafan->set_time_limit();

		while ($this->diafan->_cashregister->db_count_sent() > 0)
		{
			$timesent = time();
			try
			{
				// резервируем чек за текущим процессом
				DB::query("UPDATE {shop_cashregister} SET timesent=%d WHERE timesent=%d AND status='%h' AND auto='%h' ORDER BY master_id ASC, slave_id ASC LIMIT %d", $timesent, 0, 0, 1, self::LIMIT);
				// отправляем зарезервированные чеки
				$ids = DB::query_fetch_value("SELECT id FROM {shop_cashregister} WHERE timesent=%d AND status='%h' AND auto='%h'", $timesent, 0, 1, "id");
				foreach ($ids as $id)
				{
					$this->diafan->_cashregister->receipt_send($id);
				}
			}
			catch (Exception $e)
			{
				// снимаем резерв в случае ошибки
				DB::query("UPDATE {shop_cashregister} SET timesent=%d WHERE timesent=%d AND status='%h' AND auto='%h' ORDER BY master_id DESC, slave_id DESC LIMIT %d", 0, $timesent, 0, 1, self::LIMIT);
				break;
			}
		}
		Custom::inc('includes/404.php');
	}
}

$class = new Cashregister_send($this->diafan);
$class->init();
exit;
