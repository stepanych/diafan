<?php
/**
 * Шаблонный тег: выводит ссылки на социальные сети.
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

/*
echo '
<br>
<script src="https://yastatic.net/share2/share.js"></script>
<div class="ya-share2" data-curtain data-limit="3" data-services="vkontakte,facebook,odnoklassniki,telegram,viber,whatsapp,moimir"></div>';
*/