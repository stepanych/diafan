<?php
/**
 * Шаблон блока авторизации
 *
 * Шаблонный тег <insert name="show_login" module="registration" [template="шаблон"]>:
 * блок авторизации
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



echo '<div class="auth-d">';
if (! empty($result['user']))
{
	echo
	'<nav class="auth-d__nav nav-d">
		<ul class="nav-d__menu menu-d">';
			if($result['userpage'])
			{
				echo 
				'<li class="item-d">
					<a class="item-d__link link-d" href="'.$result['userpage'].'">
						<span class="link-d__icon icon-d fas fa-user"></span>
						<span class="link-d__name">'.$this->diafan->_('Профиль').'</span>
					</a>
				</li>';
			}	
			echo
			'<li class="item-d">
				<a class="item-d__link link-d" href="'.BASE_PATH_HREF.'logout/?'.rand(0, 99999).'">
					<span class="link-d__icon icon-d fas fa-sign-out-alt"></span>
					<span class="link-d__name">'.$this->diafan->_('Выйти').'</span>
				</a>
			</li>
		</ul>
	</nav>';
}
else
{
	echo
	'<nav class="auth-d__nav nav-d">
		<ul class="nav-d__menu menu-d">
			<li class="item-d">
				<a class="item-d__link link-d js_popup" data-popup="" href="#profile">
					<span class="link-d__icon icon-d fas fa-lock"></span>
					<span class="link-d__name">'.$this->diafan->_('Вход на сайт').'</span>
				</a>
			</li>';
			if(! empty($result['registration']))
			{
				echo
				'<li class="item-d">
					<a class="item-d__link link-d" href="'.$result['registration'].'">
						<span class="link-d__icon icon-d fas fa-user"></span>
						<span class="link-d__name">'.$this->diafan->_('Регистрация').'</span>
					</a>
				</li>';
			}
			echo
		'</ul>
	</nav>';
}
echo '</div>';
