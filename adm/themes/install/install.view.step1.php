<?php
/**
 * Шаблон контентной области первого шага установки DIAFAN.CMS
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

$disabled = false;
if(! defined('PHP_VERSION_ID'))
{
	$version = phpversion();
	$version = explode('.', $version);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
$php_version = 50600; // PHP 5.6
?><div class="box box_install">
<div class="list-item"><?php
	if (PHP_VERSION_ID < $php_version)
	{
		echo '<i class="fa fa-times-circle" style="color: #ed1c24"></i>';
		$disabled = true;
	}
	else
	{
		echo '<i class="fa fa-check-circle" style="color: #acd373"></i>';
	}
	?>
	PHP 5.6X и более новые версии
	<?php
	if (PHP_VERSION_ID < $php_version)
	{
		echo '— необходима версия PHP выше, обратитесь к администратору сервера';
	}
	?>
</div>
<div class="list-item">
	<?php
	if (! function_exists('mysql_connect') && ! function_exists('mysqli_connect'))
	{
		echo '<i class="fa fa-times-circle" style="color: #ed1c24"></i>';
		$disabled = true;
	}
	else
	{
		echo '<i class="fa fa-check-circle" style="color: #acd373"></i>';
	}
	?>
	Поддержка MySQL
	<?php
	if (! function_exists('mysql_connect') && ! function_exists('mysqli_connect'))
	{
		echo '— необходима';
	}
	?>
</div>
<div class="list-item">
	<?php
	$fp = @fsockopen('diafan.ru', 80);
	if (! $fp)
	{
		echo '<i class="fa fa-times-circle" style="color: #ed1c24"></i>';
		$disabled = true;
	}
	else
	{
		echo '<i class="fa fa-check-circle" style="color: #acd373"></i>';
	}
	?>
	Поддержка сокетов
	<?php
	$fp = @fsockopen('diafan.ru', 80);
	if (! $fp)
	{
		echo '— необходима';
	}
	?>
</div>
<?php $folders = array('userfls', 'cache', 'tmp', 'custom', 'return');
foreach($folders as $f)
{
	echo '<div class="list-item">';
	if (File::is_writable($f))
	{
		echo '<i class="fa fa-check-circle" style="color: #acd373"></i>';
	}
	else
	{
		echo '<i class="fa fa-times-circle" style="color: #ed1c24"></i>';
		$disabled = true;
	}
	echo ' Папка /'.$f.'/';
	if (! File::is_writable($f))
	{
		echo ' — создайте папку и установите права 777';
	}
	echo '</div>';
}
?>
<?php $files = array('install.php', 'config.php', 'index.html');
foreach($files as $f)
{
	echo '<div class="list-item">';
	if (File::is_writable($f))
	{
		echo '<i class="fa fa-check-circle" style="color: #acd373"></i>';
	}
	else
	{
		echo '<i class="fa fa-times-circle" style="color: #ed1c24"></i>';
		$disabled = true;
	}
	echo ' Файл '.$f;
	if (! File::is_writable($f))
	{
		echo ' — создайте файл и установите права 777';
	}
	echo '</div>';
}
?>
<?php if ($disabled)
{ ?>
	<div class="list-item">
		<b>Установка DIAFAN.CMS может быть продолжена после устранения вышеуказанных несоответствий.</b>
	</div>
<?php
} ?>

<span class="btn btn_blue" onclick="window.location=window.location">
	<i class="fa fa-refresh"></i>
	Проверить снова
</span>
&nbsp;
<span class="btn <?php if ($disabled){ echo " btn_disable"; } else { echo '" onclick="window.location=\''.BASE_PATH.'installation/step2/\'';} ?>">
	Далее
</span>
</div>
