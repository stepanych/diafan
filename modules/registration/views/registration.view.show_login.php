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



if (! $result["user"])
{
	echo '<section class="block-d block-d_registration block-d_registration_profile profile-d" id="profile">';

	echo '<header class="block-d__name">'.$this->diafan->_('Вход на сайт').'</header>';

	echo
	'<form method="post" action="'.$result["action"].'" class="profile-d__form ajax">
		<input type="hidden" name="action" value="auth">
		<input type="hidden" name="module" value="registration">
		<input type="hidden" name="form_tag" value="registration_auth">';
		echo
		'<div class="field-d">
			<input type="text" name="name" placeholder="'.$this->diafan->_($this->diafan->configmodules("mail_as_login", "users") ? 'E-mail' : 'Имя пользователя').'" autocomplete="off">
		</div>
		<div class="field-d">
			<input type="password" name="pass" placeholder="'.$this->diafan->_('Пароль').'" autocomplete="off">
		</div>
		<div class="field-d">
			<input type="checkbox" id="not_my_computer" name="not_my_computer" value="1">
			<label for="not_my_computer">'.$this->diafan->_('Чужой компьютер').'</label>
		</div>';
		echo 
		'<button class="button-d" type="submit">
			<span class="button-d__name">'.$this->diafan->_('Войти').'</span>
		</button>
		<div class="profile-d__note">';
			if (! empty($result["reminding"]))
			{
				echo '<a href="'.$result["reminding"].'">'.$this->diafan->_('Забыли пароль?').'</a>';
			}
			if(! empty($result["registration"]))
			{		
				echo '<a href="'.$result["registration"].'">'.$this->diafan->_('Регистрация').'</a>';
			}
			echo
		'</div>
		<div class="errors error"'.($result["error"] ? '>'.$result["error"] : ' style="display:none">').'</div>
	</form>';

	if(! empty($result["use_loginza"]))
	{
		$this->diafan->_site->js_view[] = 'http'.(IS_HTTPS ? "s" : '').'://loginza.ru/js/widget.js';
		echo '<br><a href="https://loginza.ru/api/widget?token_url='.urlencode('http'.(IS_HTTPS ? "s" : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'" class="loginza">
		<img src="http'.(IS_HTTPS ? "s" : '').'://loginza.ru/img/providers/yandex.png" alt="Yandex" title="Yandex">
		<img src="http'.(IS_HTTPS ? "s" : '').'://loginza.ru/img/providers/google.png" alt="Google" title="Google Accounts">
		<img src="http'.(IS_HTTPS ? "s" : '').'://loginza.ru/img/providers/vkontakte.png" alt="Вконтакте" title="Вконтакте">
		<img src="http'.(IS_HTTPS ? "s" : '').'://loginza.ru/img/providers/mailru.png" alt="Mail.ru" title="Mail.ru">
		<img src="http'.(IS_HTTPS ? "s" : '').'://loginza.ru/img/providers/twitter.png" alt="Twitter" title="Twitter">
		<img src="http'.(IS_HTTPS ? "s" : '').'://loginza.ru/img/providers/loginza.png" alt="Loginza" title="Loginza">
		<img src="http'.(IS_HTTPS ? "s" : '').'://loginza.ru/img/providers/myopenid.png" alt="MyOpenID" title="MyOpenID">
		<img src="http'.(IS_HTTPS ? "s" : '').'://loginza.ru/img/providers/openid.png" alt="OpenID" title="OpenID">
		<img src="http'.(IS_HTTPS ? "s" : '').'://loginza.ru/img/providers/webmoney.png" alt="WebMoney" title="WebMoney">
		</a><br><br>';
	}
	echo '</section>';
}
else
{
	echo '<section class="block-d block-d_registration block-d_registration_profile profile-d" id="profile">';

	echo '<header class="block-d__name">'.$this->diafan->_('Профиль').'</header>';

	echo
	'<div class="profile-d__account account-d account-d_profile">
		<div class="account-d__images">';
			if (! empty($result['avatar']))
			{
				echo
				'<figure class="account-d__avatar avatar-d avatar-d_fit">
					<img src="'.BASE_PATH.USERFILES.'/avatar/'.$result["name"].'.png" width="'.$result["avatar_width"].'" height="'.$result["avatar_height"].'" alt="'.$result["fio"].' ('.$result["name"].')">
				</figure>';
			}
			else
			{
				echo
				'<figure class="account-d__avatar avatar-d avatar-d_none" title="'.$result["fio"].' ('.$result["name"].')">
					<span class="avatar-d__icon icon-d fas fa-user"></span>
				</figure>';
			}
			echo
			'<div class="account-d__greeting">';
				echo $this->diafan->_('Здравствуйте').',<br>';
				echo $result["fio"].'!';
				echo
			'</div>
		</div>';
		// <div class="account-d__details details-d">
		// 	<div class="detail-d account-d__detail account-d__detail_greeting">';
		// 		echo $this->diafan->_('Здравствуйте').',<br>';
		// 		echo $result["fio"].'!';
		// 		echo
		// 	'</div>
		// </div>
		echo
		'<nav class="account-d__nav nav-d _underline">
			<ul class="nav-d__menu menu-d">';
				if(! empty($result['userpage']))
				{
					echo
					'<li class="item-d">
						<a class="item-d__link link-d" href="'.$result['userpage'].'">
							<span class="link-d__name">'.$this->diafan->_('Личная страница').'</span>
						</a>
					</li>';
				}
				if(! empty($result["usersettings"]))
				{
					echo
					'<li class="item-d">
						<a class="item-d__link link-d" href="'.$result['usersettings'].'">
							<span class="link-d__name">'.$this->diafan->_('Настройки').'</span>
						</a>
					</li>';
				}
				if(! empty($result['messages']))
				{
					echo
					'<li class="item-d">
						<a class="item-d__link link-d" href="'.$result['messages'].'">
							<span class="link-d__name">'.$result['messages_name'].'</span>';
							if($result['messages_unread'])
							{
								echo ' (<b>'.$result['messages_unread'].'</b>)';
							}
							echo
						'</a>
					</li>';
				}
				echo
			'</ul>
		</nav>
	</div>';
	echo
	'<a class="button-d" href="'.BASE_PATH_HREF.'logout/?'.rand(0, 99999).'">
		<span class="button-d__icon icon-d fas fa-sign-out-alt"></span>
		<span class="button-d__name">'.$this->diafan->_('Выйти', false).'</span>
	</a>';
	echo '</section>';
}