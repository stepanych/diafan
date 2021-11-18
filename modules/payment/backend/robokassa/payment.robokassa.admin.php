<?php
/**
 * Настройки платежной системы Robokassa для административного интерфейса
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

class Payment_robokassa_admin
{
	public $config;

	public function __construct()
	{
		$this->config = array(
			"name" => 'Robokassa',
			"params" => array(
                'info' => array('name' => '<p><a href="https://partner.robokassa.ru/Reg/Register?PromoCode=01diafan&culture=ru" target="_blank"><img src="https://robokassa.com/local/templates/main/frontend/images/logo.svg"></a></p><p>Универсальный эквайринг для приёма всех видов платежей: Visa, MasterCard, Мир, Apple Pay, Samsung Pay, Qiwi, WebMoney и многие другие. Можно подключиться юридическим и физическим лицам. Вам не нужна онлайн-касса, Robokassa формирует и отправляет чеки вашим клиентам, а также в налоговую в строгом соответствии с 54-ФЗ! </p><p><a href="https://www.diafan.ru/userfiles/images/faq/d9eade0f881.jpg" class="btn btn_black btn_small" data-fancybox="galleryimage">Как настроить робокассу</a> Для начала работы <a href="https://partner.robokassa.ru/Reg/Register?PromoCode=01diafan&culture=ru"  class="btn btn_blue btn_small" target="_blank">зарегистрируйтесь в Robokassa</a>.</p>', 'type' => 'info',),
                'robokassa_login' => 'Идентификатор магазина',
                'robokassa_pass_1' => 'Пароль #1',
                'robokassa_pass_2' => 'Пароль #2',
				'robokassa_test' => array('name' => 'Активировать тестовые платежи', 'type' => 'checkbox'),
				'robokassa_receipt' => array('name' => 'Фискализация Robokassa. Облачная онлайн-касса ФЗ-54', 'type' => 'checkbox'),
				'robokassa_sno' => array(
					'name' => 'Система налогообложения 
магазина',
					'type' => 'select',
					'select' => array(
						'osn' => 'общая СН',
						'usn_income' => 'упрощенная СН (доходы)',
						'usn_income_outcome' => 'упрощенная СН (доходы минус расходы)',
						'envd' => 'единый налог на вмененный доход',
						'esn' => 'единый сельскохозяйственный налог',
						'patent' => 'патентная СН',
					),
					'help' => 'Нужен, только если у вас несколько систем налогообложения. В остальных случаях не передается.',
				),
				'robokassa_tax' => array(
					'name' => 'Ставка НДС',
					'type' => 'select',
					'select' => array(
						'none' => 'без НДС',
						'vat0' => 'НДС по ставке 0%',
						'vat10' => 'НДС чека по ставке 10%',
						'vat20' => 'НДС чека по ставке 20%',
						'vat110' => 'НДС чека по расчетной ставке 10/110',
						'vat120' => 'НДС чека по расчетной ставке 20/120',
					),
				),
			)
		);
	}
}
