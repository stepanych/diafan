<?php
/**
 * Настройки модуля
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
 * Faq_admin_config
 */
class Faq_admin_config extends Frame_admin
{
	/**
	 * @var array поля в базе данных для редактирования
	 */
	public $variables = array (
		'config' => array (
			'hr1' => array(
				'type' => 'title',
				'name' => 'Основные',
			),
			'nastr' => array(
				'type' => 'numtext',
				'name' => 'Количество вопросов на странице',
				'help' => 'Количество одновременно выводимых вопросов в списке.',
			),
			'show_more' => array(
				'type' => 'checkbox',
				'name' => 'Включить «Показать ещё»',
				'help' => 'На странице вопросов появится кнопка «Показать ещё». Увеличивает количество одновременно выводимых вопросов в списке.',
			),
			'count_letter_list' => array(
				'type' => 'numtext',
				'name' => 'Количество символов для сокращения вопроса и ответа в списке',
				'help' => 'Если не задано, вопрос и ответ не будут сокращаться.',
			),
			'format_date' => array(
				'type' => 'select',
				'name' => 'Формат даты',
				'help' => 'Позволяет настроить отображение даты в модуле.',
				'select' => array(
					0 => '01.05.2016',
					6 => '01.05.2016 14:45',
					1 => '1 мая 2016 г.',
					2 => '1 мая',
					3 => '1 мая 2016, понедельник',
					5 => 'вчера 15:30',
					4 => 'не отображать',
				),
			),
			'only_user' => array(
				'type' => 'checkbox',
				'name' => 'Только зарегистрированные пользователи могут задавать вопросы',
				'help' => 'Вопросы могут добавлять только зарегистрированные пользователи.',
			),
			'page_show' => array(
				'type' => 'checkbox',
				'name' => 'Открывать вопрос на отдельной странице',
				'help' => 'Если не отмечена, вопросы будут одним списком без возможности открыть отдельную страницу.',
			),
			'hr2' => 'hr',
			'captcha' => array(
				'type' => 'module',
				'name' => 'Использовать защитный код (капчу)',
				'help' => 'Для добавления вопроса пользователь должен ввести защитный код.',
			),
			'hr3' => 'hr',
			'attachments' => array(
				'type' => 'module',
				'name' => 'Разрешить добавление файлов',
				'help' => 'Позволяет пользователям прикреплять файлы к вопросам.',
			),
			'max_count_attachments' => array(
				'type' => 'none',
				'name' => 'Максимальное количество добавляемых файлов',
				'help' => 'Количество добавляемых файлов. Если значение равно нулю, то форма добавления файлов не выводится.',
				'no_save' => true,
			),
			'attachments_access_admin' => array(
				'type' => 'none',
				'hide' => true,
				'no_save' => true,
			),
			'attachment_extensions' => array(
				'type' => 'none',
				'name' => 'Доступные типы файлов (через запятую)',
				'no_save' => true,
			),
			'recognize_image' => array(
				'type' => 'none',
				'name' => 'Распознавать изображения',
				'help' => 'Позволяет прикрепленные к вопросу файлы в формате JPEG, GIF, PNG отображать как изображения.',
				'no_save' => true,
			),
			'attach_big' => array(
				'type' => 'none',
				'name' => 'Размер для большого изображения',
				'help' => 'Размер изображения, отображаемый в пользовательской части сайта при увеличении изображения предпросмотра.',
				'no_save' => true,
			),
			'attach_medium' => array(
				'type' => 'none',
				'name' => 'Размер для маленького изображения',
				'help' => 'Размер изображения предпросмотра.',
				'no_save' => true,
			),
			'attach_use_animation' => array(
				'type' => 'none',
				'name' => 'Использовать анимацию при увеличении изображений',
				'help' => 'Параметр добавляет JavaScript код, позволяющий включить анимацию при увеличении изображений. Параметр выводится, если отмечена опция «Распознавать изображения».',
				'no_save' => true,
			),
			'upload_max_filesize' => array(
				'type' => 'none',
				'name' => 'Максимальный размер загружаемых файлов',
				'help' => 'Параметр показывает максимально допустимый размер загружаемых файлов, установленный в настройках хостинга.',
				'no_save' => true,
			),
			'hr4' => array(
				'type' => 'title',
				'name' => 'Категории',
			),
			'cat' => array(
				'type' => 'checkbox',
				'name' => 'Использовать категории',
				'help' => 'Позволяет включить/отключить категории вопросов.',
			),
			'nastr_cat' => array(
				'type' => 'numtext',
				'name' => 'Количество категорий на странице',
				'help' => 'Количество одновременно выводимых категорий в списке на первой страницы модуля.',
				'depend' => 'cat',
			),
			'count_list' => array(
				'type' => 'numtext',
				'name' => 'Количество вопросов в списке категорий',
				'help' => 'Количество вопросов, выводимых в списке категорий на главной странице модуля.',
				'depend' => 'cat',
			),
			'count_child_list' => array(
				'type' => 'numtext',
				'name' => 'Количество вопросов в списке вложенной категории',
				'help' => 'Для первой страницы модуля и для страницы категории.',
				'depend' => 'cat',
			),
			'children_elements' => array(
				'type' => 'checkbox',
				'name' => 'Показывать вопросы подкатегорий',
				'help' => 'Если отмечена, в списке вопросов категории будут отображатся вопросы из всех вложенных категорий.',
				'depend' => 'cat',
			),
			'hr5' => array(
				'type' => 'title',
				'name' => 'Уведомления',
			),
			'add_message' => array(
				'type' => 'textarea',
				'name' => 'Сообщение после отправки',
				'help' => 'Сообщение, получаемое пользователем при удачной загрузки вопроса, допускаются HTML-теги для оформления сообщения.',
				'multilang' => true,
			),
			'error_insert_message' => array(
				'type' => 'text',
				'name' => 'Ваше сообщение уже имеется в базе',
				'help' => 'Сообщение, получаемое пользователем при повторной попытке отправить вопрос.',
				'multilang' => true,
			),
			'subject' => array(
				'type' => 'text',
				'name' => 'Тема письма для ответа',
				'help' => "Ответ отправляется пользователю, если при редактировании вопроса заполнены поля «E-mail», «Вопрос», «Ответ» и «Отправить ответ». Можно добавлять:\n\n* %title – название сайта,\n* %url – адрес сайта (например, site.ru).",
				'multilang' => true,
			),
			'message' => array(
				'type' => 'textarea',
				'name' => 'Сообщение для ответа',
				'help' => "Можно добавлять:\n\n* %name – имя пользователя,\n* %title – название сайта,\n* %url – адрес сайта (например, site.ru),\n* %question – вопрос,\n* %answer – ответ.",
				'multilang' => true,
			),
			'emailconf' => array(
				'type' => 'select',
				'name' => 'E-mail, указываемый в обратном адресе пользователю',
				'help' => "Возможные значения:\n\n* e-mail, указанный в параметрах сайта;\n* другой (при выборе этого значения появляется дополнительное поле **впишите e-mail**).",
			),
			'email' => array(
				'type' => 'none',
				'name' => 'впишите e-mail',
				'hide' => true,
			),
			'hr6' => 'hr',
			'sendmailadmin' => array(
				'type' => 'checkbox',
				'name' => 'Уведомлять о поступлении новых вопросов на e-mail',
				'help' => 'Возможность уведомления администратора о поступлении новых вопросов из формы в пользовательской части сайта.',
			),
			'emailconfadmin' => array(
				'type' => 'function',
				'name' => 'E-mail для уведомлений администратора',
				'help' => "Возможные значения:\n\n* e-mail, указанный в параметрах сайта;\n* другой (при выборе этого значения появляется дополнительное поле **впишите e-mail**).",
				'depend' => 'sendmailadmin',
			),
			'email_admin' => array(
				'type' => 'none',
				'name' => 'впишите e-mail',
				'hide' => true,
			),
			'subject_admin' => array(
				'type' => 'text',
				'name' => 'Тема письма для уведомлений',
				'help' => "Можно добавлять:\n\n* %title – название сайта,\n* %url – адрес сайта (например, site.ru).",
				'depend' => 'sendmailadmin',
			),
			'message_admin' => array(
				'type' => 'textarea',
				'name' => 'Сообщение для уведомлений',
				'help' => "Можно добавлять:\n\n* %title – название сайта,\n* %url – адрес сайта (например, site.ru),\n* %question – вопрос,\n* %name – имя пользователя,\n* %email – e-mail пользователя,\n* %files – название прикрепляемых файлов.",
				'depend' => 'sendmailadmin',
			),
			'hr7' => 'hr',
			'sendsmsadmin' => array(
				'type' => 'checkbox',
				'name' => 'Уведомлять о поступлении новых вопросов по SMS',
				'help' => 'Возможность отправлять SMS администратору при создании вопроса на сайте. Параметр можно подключить, если в [Параметрах сайта](http://www.diafan.ru/dokument/full-manual/sysmodules/config/) настроены SMS-уведомления.',
			),
			'sms_admin' => array(
				'type' => 'text',
				'name' => 'Номер телефона в федеральном формате',
				'help' => 'Номер телефона для SMS-уведомлений администратора о новом вопросе.',
				'depend' => 'sendsmsadmin',
			),
			'sms_message_admin' => array(
				'type' => 'textarea',
				'name' => 'Сообщение для уведомлений',
				'help' => 'Текст сообщения для SMS-уведомлений администратора о новом вопросе. Не более 800 символов.',
				'depend' => 'sendsmsadmin',
			),
			'hr8' => array(
				'type' => 'title',
				'name' => 'Подключения',
			),
			'counter' => array(
				'type' => 'checkbox',
				'name' => 'Счетчик просмотров',
				'help' => 'Позволяет считать количество просмотров отдельного вопроса.',
			),
			'counter_site' => array(
				'type' => 'checkbox',
				'name' => 'Выводить счетчик на сайте',
				'help' => 'Позволяет вывести на сайте количество просмотров отдельного вопроса. Параметр выводится, если отмечена опция «Счетчик просмотров».',
				'depend' => 'counter',
			),
			'comments' => array(
				'type' => 'module',
				'name' => 'Подключить комментарии к вопросам',
				'help' => 'Подключение модуля «Комментарии». Параметр не будет включен, если модуль «Комментарии» не установлен. Подробности см. в разделе [модуль «Комментарии»](http://www.diafan.ru/dokument/full-manual/upmodules/comments/).',
			),
			'comments_cat' => array(
				'type' => 'none',
				'name' => 'Подключить комментарии к категориям',
				'help' => 'Подключение модуля «Комментарии» к категориям вопросов. Параметр не будет включен, если модуль «Комментарии» не установлен. Подробности см. в разделе [модуль «Комментарии»](http://www.diafan.ru/dokument/full-manual/upmodules/comments/).',
				'no_save' => true,
			),
			'tags' => array(
				'type' => 'module',
				'name' => 'Подключить теги',
				'help' => 'Подключение модуля «Теги». Параметр не будет включен, если модуль «Теги» не установлен. Подробности см. в разделе [модуль «Теги»](http://www.diafan.ru/dokument/full-manual/modules/tags/).',
			),
			'rating' => array(
				'type' => 'module',
				'name' => 'Показывать рейтинг вопросов',
				'help' => 'Подключение модуля «Рейтинг». Параметр не будет включен, если модуль «Рейтинг» не установлен. Подробности см. в разделе [модуль «Рейтинг»](http://www.diafan.ru/dokument/full-manual/upmodules/rating/).',
			),
			'rating_cat' => array(
				'type' => 'none',
				'name' => 'Подключить рейтинг к категориям',
				'help' => 'Подключение модуля «Рейтинг» к категориям. Параметр не будет включен, если модуль «Рейтинг» не установлен. Подробности см. в разделе [модуль «Рейтинг»](http://www.diafan.ru/dokument/full-manual/upmodules/rating/).',
				'no_save' => true,
			),
			'keywords' => array(
				'type' => 'module',
				'name' => 'Подключить перелинковку',
				'help' => 'Отображение перелинковки в модуле. Подробности см. в разделе [модуль «Перелинковка»](http://www.diafan.ru/dokument/full-manual/upmodules/keywords/).',
			),
			'rel_two_sided' => array(
				'type' => 'checkbox',
				'name' => 'В блоке похожих вопросов связь двусторонняя',
				'help' => 'Позволяет установленную в вопросе связь с другим вопросом использовать в двух направлениях.',
			),
			'hr9' => array(
				'type' => 'title',
				'name' => 'Автоформирование для SEO',
			),
			'title_tpl' => array(
				'type' => 'text',
				'name' => 'Шаблон для автоматического генерирования Title',
				'help' => "Если шаблон задан и для вопроса не прописан заголовок *Title*, то заголовок автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название,\n* %category – название категории,\n* %parent_category – название категории верхнего уровня (SEO-специалисту).",
				'multilang' => true
			),
			'title_tpl_cat' => array(
				'type' => 'text',
				'name' => 'Шаблон для автоматического генерирования Title для категории',
				'help' => "Если шаблон задан и для категории не прописан заголовок *Title*, то заголовок автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название категории,\n* %parent – название категории верхнего уровня,\n\n* %page – страница (текст можно поменять в интерфейсе «Языки сайта» – «Перевод интерфейса») (SEO-специалисту).",
				'multilang' => true,
				'depend' => 'cat',
			),
			'keywords_tpl' => array(
				'type' => 'text',
				'name' => 'Шаблон для автоматического генерирования Keywords',
				'help' => "Если шаблон задан и для вопроса не заполнено поле *Keywords*, то поле *Keywords* автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название,\n* %category – название категории,\n* %parent_category – название категории верхнего уровня (SEO-специалисту).",
				'multilang' => true
			),
			'keywords_tpl_cat' => array(
				'type' => 'text',
				'name' => 'Шаблон для автоматического генерирования Keywords для категории',
				'help' => "Если шаблон задан и для категории не заполнено поле *Keywords*, то поле *Keywords* автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название категории,\n* %parent – название категории верхнего уровня (SEO-специалисту).",
				'multilang' => true,
				'depend' => 'cat',
			),
			'descr_tpl' => array(
				'type' => 'text',
				'name' => 'Шаблон для автоматического генерирования Description',
				'help' => "Если шаблон задан и для вопроса не заполнено поле *Description*, то поле *Description* автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название,\n* %category – название категории,\n* %parent_category – название категории верхнего уровня,\n* %anons – краткое описание (SEO-специалисту).",
				'multilang' => true
			),
			'descr_tpl_cat' => array(
				'type' => 'text',
				'name' => 'Шаблон для автоматического генерирования Description для категории',
				'help' => "Если шаблон задан и для категории не заполнено поле *Description*, то поле Description автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название категории,\n* %parent – название категории верхнего уровня,\n* %anons – краткое описание (SEO-специалисту).",
				'multilang' => true,
				'depend' => 'cat',
			),
			'hr10' => array(
				'type' => 'title',
				'name' => 'Оформление',
			),
			'themes' => array(
				'type' => 'function',
				'hide' => true,
			),
			'theme_list' => array(
				'type' => 'none',
				'name' => 'Шаблон для списка элементов',
				'help' => 'По умолчанию modules/faq/views/faq.view.list.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате.',
			),
			'view_list' => array(
				'type' => 'none',
				'hide' => true,
			),
			'theme_list_rows' => array(
				'type' => 'none',
				'name' => 'Шаблон для элементов в списке',
				'help' => 'По умолчанию modules/faq/views/faq.view.rows.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате. Значение параметра важно для AJAX.',
			),
			'view_list_rows' => array(
				'type' => 'none',
				'hide' => true,
			),
			'theme_first_page' => array(
				'type' => 'none',
				'name' => 'Шаблон для первой страницы модуля (если подключены категории)',
				'help' => 'По умолчанию modules/faq/views/faq.view.fitst_page.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате.',
			),
			'view_first_page' => array(
				'type' => 'none',
				'hide' => true,
			),
			'theme_first_page_rows' => array(
				'type' => 'none',
				'name' => 'Шаблон для элементов в списке первой страницы модуля (если подключены категории)',
				'help' => 'По умолчанию modules/faq/views/faq.view.fitst_page.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате. Значение параметра важно для AJAX.',
			),
			'view_first_page_rows' => array(
				'type' => 'none',
				'hide' => true,
			),
			'theme_id' => array(
				'type' => 'none',
				'name' => 'Шаблон для страницы элемента',
				'help' => 'По умолчанию, modules/faq/views/faq.view.id.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате.',
			),
			'view_id' => array(
				'type' => 'none',
				'hide' => true,
			),
			'hr11' => array(
				'type' => 'title',
				'name' => 'Дополнительно',
			),
			'admin_page'     => array(
				'type' => 'checkbox',
				'name' => 'Отдельный пункт в меню администрирования для каждого раздела сайта',
				'help' => 'Если модуль подключен к нескольким страницам сайта, отметка данного параметра выведет несколько пунктов в меню административной части для удобства быстрого доступа (администратору сайта).',
			),
			'map' => array(
				'type' => 'module',
				'name' => 'Индексирование для карты сайта',
				'help' => 'При изменении настроек, влияющих на отображение страницы, модуль автоматически переиндексируется для карты сайта sitemap.xml.',
			),
			'where_access' => array(
				'type' => 'none',
				'hide' => true,
			),
		),
	);

	/**
	 * @var array настройки модуля
	 */
	public $config = array (
		'element_site', // делит элементы по разделам (страницы сайта, к которым прикреплен модуль)
		'config', // файл настроек модуля
	);

	/**
	 * Подготавливает конфигурацию модуля
	 * @return void
	 */
	public function prepare_config()
	{
		if(! $this->diafan->configmodules("sms", 'postman'))
		{
			$this->diafan->variable("sendsmsadmin", "disabled", true);
			$name = $this->diafan->_($this->diafan->variable("sendsmsadmin", "name")).'<br>'.$this->diafan->_('Необходимо %sнастроить%s SMS-уведомления.', '<a href="'.BASE_PATH_HREF.'config/">', '</a>');
			$this->diafan->variable("sendsmsadmin", "name", $name);
			$this->diafan->configmodules("sendsmsadmin", $this->diafan->_admin->module, $this->diafan->_route->site, _LANG, 0);
		}
	}
}
