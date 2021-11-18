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
 * News_admin_config
 */
class News_admin_config extends Frame_admin
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
			'nastr' => array(
				'type' => 'numtext',
				'name' => 'Количество новостей на странице',
				'help' => 'Количество одновременно выводимых новостей в списке.',
			),
			'show_more' => array(
				'type' => 'checkbox',
				'name' => 'Включить «Показать ещё»',
				'help' => 'На странице новостей появится кнопка «Показать ещё». Увеличивает количество одновременно выводимых новостей в списке.',
			),
			'hr2' => array(
				'type' => 'title',
				'name' => 'Категории',
			),
			'cat' => array(
				'type' => 'checkbox',
				'name' => 'Использовать категории',
				'help' => 'Разделение новостей на категории, рубрики.',
			),
			'nastr_cat' => array(
				'type' => 'numtext',
				'name' => 'Количество категорий на странице',
				'help' => 'Количество одновременно выводимых категорий в списке на первой страницы модуля.',
				'depend' => 'cat',
			),			
			'count_list' => array(
				'type' => 'numtext',
				'name' => 'Количество новостей в списке категорий',
				'help' => 'Количество новостей, выводимых в списке категорий на главной странице модуля.',
				'depend' => 'cat',
			),
			'children_elements' => array(
				'type' => 'checkbox',
				'name' => 'Показывать новости подкатегорий',
				'help' => 'Если отмечена, в списке новостей категории будут отображатся последние новости из всех вложенных категорий.',
				'depend' => 'cat',
			),
			'count_child_list' => array(
				'type' => 'numtext',
				'name' => 'Количество новостей в списке вложенной категории',
				'help' => 'Для первой страницы модуля и для страницы категории.',
				'depend' => 'cat',
			),			
			'hr3' => array(
				'type' => 'title',
				'name' => 'Изображения',
			),
			'images' => array(
				'type' => 'module',
				'element_type' => array('element', 'cat'),
				'hide' => true,
			),
			'images_element' => array(
				'type' => 'none',
				'name' => 'Использовать изображения',
				'help' => 'Если отмечена, к новостям можно будет будет добавлять изображения.',
				'no_save' => true,
			),
			'images_variations_element' => array(
				'type' => 'none',
				'name' => 'Генерировать размеры изображений',
				'help' => 'Размеры изображений, заданные в модуле «Изображения» и тег латинскими буквами для подключения изображения на сайте. Обязательно должны быть заданы два размера: превью изображения в списке новостей (тег medium) и полное изображение (тег large).',
				'no_save' => true,
			),
			'resize' => array(
				'type' => 'none',
				'name' => 'Применить настройки ко всем ранее загруженным изображениям',
				'help' => 'Позволяет переконвертировать размер уже загруженных изображений. Кнопка необходима, если изменены настройки размеров изображений. Параметр выводится, если отмечена опция «Использовать изображения».',
				'no_save' => true,
			),			
			'list_img_element' => array(
				'type' => 'none',
				'name' => 'Отображение изображений в списке',
				'help' => "Параметр принимает значения:\n\n* нет (отключает отображение изображений в списке);\n* показывать одно изображение;\n* показывать все изображения. Параметр выводится, если отмечена опция «Использовать изображения».",
				'no_save' => true,
			),
			'images_cat' => array(
				'type' => 'none',
				'name' => 'Использовать изображения для категорий',
				'help' => 'Позволяет включить/отключить загрузку изображений к категориям.',
				'no_save' => true,
			),
			'images_variations_cat' => array(
				'type' => 'none',
				'name' => 'Генерировать размеры изображений для категорий',
				'help' => 'Размеры изображений, заданные в модуле «Изображения» и тег латинскими буквами для подключения изображения на сайте. Обязательно должны быть заданы два размера: превью изображения в списке категорий (тег medium) и полное изображение (тег large). Параметр выводится, если отмечена опция «Использовать изображения для категорий».',
				'no_save' => true,
			),
			'list_img_cat' => array(
				'type' => 'none',
				'name' => 'Отображение изображений в списке категорий',
				'help' => "Параметр принимает значения:\n\n* нет (отключает отображение изображений в списке);\n* показывать одно изображение;\n* показывать все изображения. Параметр выводится, если отмечена опция «Использовать изображения для категорий».",
				'no_save' => true,
			),
			'use_animation' => array(
				'type' => 'none',
				'name' => 'Использовать анимацию при увеличении изображений',
				'help' => 'Параметр добавляет JavaScript код, позволяющий включить анимацию при увеличении изображений. Параметр выводится, если отмечена опция «Использовать изображения».',
				'no_save' => true,
			),
			'upload_max_filesize' => array(
				'type' => 'none',
				'name' => 'Максимальный размер загружаемых файлов',
				'help' => 'Параметр показывает максимально допустимый размер загружаемых файлов, установленный в настройках хостинга. Параметр выводится, если отмечена опция «Использовать изображения».',
				'no_save' => true,
			),
			'hr6' => array(
				'type' => 'title',
				'name' => 'Подключения',
			),
			'counter' => array(
				'type' => 'checkbox',
				'name' => 'Подключить счетчик просмотров',
				'help' => 'Позволяет считать количество просмотров отдельной новости.',
			),
			'counter_site' => array(
				'type' => 'checkbox',
				'name' => 'Выводить счетчик на сайте',
				'help' => 'Позволяет вывести на сайте количество просмотров отдельной новости. Параметр выводится, если отмечена опция «Счетчик просмотров».',
				'depend' => 'counter',
			),
			'comments' => array(
				'type' => 'module',
				'name' => 'Подключить комментарии к новостям',
				'help' => 'Если отмечена, пользователи сайта смогут комментировать новости. Параметр не будет включен, если модуль «Комментарии» не установлен. Подробности см. в разделе [модуль «Комментарии»](http://www.diafan.ru/dokument/full-manual/upmodules/comments/).',
			),
			'comments_cat' => array(
				'type' => 'none',
				'name' => 'Показывать комментарии к категориям',
				'help' => 'Подключение модуля «Комментарии» к категориям новостей. Параметр не будет включен, если модуль «Комментарии» не установлен. Подробности см. в разделе [модуль «Комментарии»](http://www.diafan.ru/dokument/full-manual/upmodules/comments/).',
				'no_save' => true,
			),
			'tags' => array(
				'type' => 'module',
				'name' => 'Подключить теги',
				'help' => 'Если отмечена, к каждой новости можно будет добавлять теги. Параметр не будет включен, если модуль «Теги» не установлен. Подробности см. в разделе [модуль «Теги»](http://www.diafan.ru/dokument/full-manual/modules/tags/).',
			),
			'rating' => array(
				'type' => 'module',
				'name' => 'Подключить рейтинг новостей',
				'help' => 'Если отмечена, каждой новости пользователи сайта смогут выставлять рейтинг. Параметр не будет включен, если модуль «Рейтинг» не установлен. Подробности см. в разделе [модуль «Рейтинг»](http://www.diafan.ru/dokument/full-manual/upmodules/rating/).',
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
				'name' => 'В блоке похожих новостей связь двусторонняя',
				'help' => 'Если отметить, то при назначении новости А похожей новости Б, у новости Б автоматически станет похожая новость А.',
			),
			'hr7' => array(
				'type' => 'title',
				'name' => 'Автогенерация для SEO',
			),
			'title_tpl' => array(
				'type' => 'text',
				'name' => 'Шаблон для автоматического генерирования Title',
				'help' => "Если шаблон задан и для новости не прописан заголовок *Title*, то заголовок автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название,\n* %category – название категории,\n* %parent_category – название категории верхнего уровня (SEO-специалисту).",
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
				'help' => "Если шаблон задан и для новости не заполнено поле *Keywords*, то поле *Keywords* автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название,\n* %category – название категории,\n* %parent_category – название категории верхнего уровня (SEO-специалисту).",
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
				'help' => "Если шаблон задан и для новости не заполнено поле *Description*, то поле *Description* автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название,\n* %category – название категории,\n* %parent_category – название категории верхнего уровня,\n* %anons – краткое описание (SEO-специалисту).",
				'multilang' => true
			),
			'descr_tpl_cat' => array(
				'type' => 'text',
				'name' => 'Шаблон для автоматического генерирования Description для категории',
				'help' => "Если шаблон задан и для категории не заполнено поле *Description*, то поле Description автоматически генерируется по шаблону. В шаблон можно добавить:\n\n* %name – название категории,\n* %parent – название категории верхнего уровня,\n* %anons – краткое описание (SEO-специалисту).",
				'multilang' => true,
				'depend' => 'cat',
			),
			'hr8' => array(
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
				'help' => 'По умолчанию modules/news/views/news.view.list.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате.',
			),
			'view_list' => array(
				'type' => 'none',
				'hide' => true,
			),
			'theme_list_rows' => array(
				'type' => 'none',
				'name' => 'Шаблон для списка элементов',
				'help' => 'По умолчанию modules/news/views/news.view.rows.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате. Значение параметра важно для AJAX.',
			),
			'view_list_rows' => array(
				'type' => 'none',
				'hide' => true,
			),
			'theme_first_page' => array(
				'type' => 'none',
				'name' => 'Шаблон для первой страницы модуля (если подключены категории)',
				'help' => 'По умолчанию modules/news/views/news.view.fitst_page.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате.',
			),
			'view_first_page' => array(
				'type' => 'none',
				'hide' => true,
			),
			'theme_first_page_rows' => array(
				'type' => 'none',
				'name' => 'Шаблон для первой страницы модуля (если подключены категории)',
				'help' => 'По умолчанию modules/news/views/news.view.fitst_page.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате. Значение параметра важно для AJAX.',
			),
			'view_first_page_rows' => array(
				'type' => 'none',
				'hide' => true,
			),
			'theme_id' => array(
				'type' => 'none',
				'name' => 'Шаблон для страницы новости',
				'help' => 'По умолчанию, modules/news/views/news.view.id.php. Параметр для разработчиков! Не устанавливайте, если не уверены в результате.',
			),
			'view_id' => array(
				'type' => 'none',
				'hide' => true,
			),						
			'hr9' => array(
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
}