<?php
/**
 * Редактирование модуля
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
 * Account_admin
 */
class Account_admin extends Frame_admin
{
  /**
	 * @var boolean маркер отложенной загрузки контента
	 */
	public $defer = true;

  /**
	 * @var string заголовок отложенной загрузки контента
	 */
	public $defer_title = 'Подождите, идет соединение с сервером ...';

  /**
	 * Подготавливает конфигурацию модуля
   *
	 * @return void
	 */
	public function prepare_config()
	{

	}

  /**
	 * Выводит содержание "Персональная страница"
   *
	 * @return void
	 */
	public function show()
	{
        $this->user();

        echo '
        <div class="box box_height">';
        $this->auth();
        echo '
        </div>';
	}

  /**
	 * Выводит форму авторизации
   *
	 * @return void
	 */
  private function auth()
  {
    $errors = array();
    $auth_type = $this->diafan->filter($_POST, 'int', "auth_type");
    $auth_type = in_array($auth_type, array(1, 2)) ? $auth_type : $this->diafan->_account->auth_type;
    $auth_in = (! empty($_POST["form_action"]) && $_POST["form_action"] == 'auth_in');
    if($auth_in && ! $this->diafan->_account->is_auth())
    {
      switch($auth_type)
      {
        case 2:
          $this->diafan->_account->token = $this->diafan->filter($_POST, 'string', "token");
          break;

        case 1:
        default:
          $name = $this->diafan->filter($_POST, 'string', "name");
          $pass = $this->diafan->filter($_POST, 'string', "pass");
          $this->diafan->_account->token = $this->diafan->_account->auth($name, $pass);
          break;
      }
      if($this->diafan->_client->errors) $errors = array_merge($errors, $this->diafan->_client->errors);
      $this->diafan->_addons->update(true);
    }
    $auth_out = (! empty($_POST["form_action"]) && $_POST["form_action"] == 'auth_out');
    if($auth_out && $this->diafan->_account->is_auth())
    {
      $this->diafan->_account->revoke(CLIENT_LOCAL_REVOKE);
      $this->diafan->_account->token = '';
      if($this->diafan->_client->errors) $errors = array_merge($errors, $this->diafan->_client->errors);
      $this->diafan->_addons->update(true);
    }
    echo '<h2>'.$this->diafan->_('Авторизация').'</h2>';
    if(! $this->diafan->_account->is_auth())
    {
      if($errors && isset($errors["v"]))
      {
        echo '<div class="attention">'.$errors["v"].'</div>';
        return;
      }
      elseif($errors)
      {
        if(! is_array($errors)) $errors = array($errors);
        foreach($errors as $key => $error)
        {
          if(in_array($key, array('no_verify'))) continue;
          echo '<div class="attention">'.$error.'</div>';
        }
      }

      echo '<p>'.$this->diafan->_('Для использования возможностей персонального кабинета DIAFAN.CMS необходимо авторизоваться, используя логин и пароль учетной записи %sdiafan.ru%s. Либо используя электронный ключ, указанный на персональной странице сайта %suser.diafan.ru%s.', '<a href="https://user.diafan.ru/" target="_blank">', '</a>', '<a href="https://user.diafan.ru/" target="_blank">', '</a>').'</p>';

      echo '
      <form method="POST" action="'.URL.$this->diafan->get_nav.'" enctype="multipart/form-data" id="auth_in">';
      echo '
        <input type="hidden" name="check_hash_user" value="'.$this->diafan->_users->get_hash().'">
        <input type="hidden" name="form_action" value="auth_in">';
      echo '
        <div class="unit">
      		<input class="radio_tab" type="radio" name="auth_type" value="1"'.($auth_type == 1 ? ' checked' : '').' id="auth1_radio"> <label for="auth1_radio">'.$this->diafan->_('Логин и пароль').'</label>
      		<input class="radio_tab" type="radio" name="auth_type" value="2"'.($auth_type == 2 ? ' checked' : '').' id="auth2_radio"> <label for="auth2_radio">'.$this->diafan->_('Электронный ключ').'</label>

    			<div class="auth_type'.($auth_type != 1 ? ' hide' : '').'" for="auth1_radio">
            <div class="infofield">'.$this->diafan->_('Логин').': <i class="tooltip fa fa-question-circle" title="'.$this->diafan->_('Имя учетной записи').'"></i></div>
            <input type="text" name="name" placeholder="'.$this->diafan->_('Введите имя учетной записи diafan.ru').'"'.($auth_in && $auth_type == 1 && ! empty($_POST["name"]) ? ' value="'.$_POST["name"].'"' : '').'>
            '.($auth_in && $auth_type == 1 && empty($_POST["name"]) ? '<div class="error">'.$this->diafan->_('Необходимо ввести имя учетной записи.').'</div>' : '').'
            <div class="infofield">'.$this->diafan->_('Пароль').': <i class="tooltip fa fa-question-circle" title="'.$this->diafan->_('Пароль учетной записи').'"></i></div>
            <input type="password" name="pass" placeholder="'.$this->diafan->_('Введите пароль учетной записи diafan.ru').'">
            '.($auth_in && $auth_type == 1 && empty($_POST["pass"]) ? '<div class="error">'.$this->diafan->_('Необходимо ввести пароль учетной записи.').'</div>' : '').'
            '.($auth_in && $auth_type == 1 && ! empty($_POST["name"]) && ! empty($_POST["pass"]) ? '<div class="error">'.$this->diafan->_('Введена неверная пара логин/пароль.').'</div>' : '').'
          </div>
    			<div class="auth_type'.($auth_type != 2 ? ' hide' : '').'" for="auth2_radio">
            <div class="infofield">'.$this->diafan->_('Электронный ключ').': <i class="tooltip fa fa-question-circle" title="'.$this->diafan->_('Электронный ключ, указанный на персональной странице user.diafan.ru').'"></i></div>
            <input type="text" name="token" placeholder="'.$this->diafan->_('Введите электронный ключ').'"'.($auth_in && $auth_type == 2 && ! empty($_POST["token"]) ? ' value="'.$_POST["token"].'"' : '').'>
            '.($auth_in && $auth_type == 2 && empty($_POST["token"]) ? '<div class="error">'.$this->diafan->_('Необходимо ввести электронный ключ.').'</div>' : '').'
            '.($auth_in && $auth_type == 2 && ! empty($_POST["token"]) ? '<div class="error">'.$this->diafan->_('Введен неверный электронный ключ.').'</div>' : '').'
          </div>
        </div>';
      echo '
        <button class="btn btn_blue btn_small btn_sign-in">'.$this->diafan->_('Войти').'</button>
      </form>';
      if($auth_out)
      {
        if(! $this->diafan->defer) $this->diafan->redirect($this->diafan->_route->current_admin_link());
        else $this->diafan->defer_redirect = $this->diafan->_route->current_admin_link();
      }
      return;
    }
    $token = $this->diafan->configmodules("token");
    if($this->diafan->_account->token != $token)
    {
      $this->diafan->configmodules("token", "account", 0, 0, $this->diafan->_account->token);
    }
    echo '
    <form method="POST" action="'.URL.$this->diafan->get_nav.'" enctype="multipart/form-data" id="auth_out">';

    echo '
      <input type="hidden" name="check_hash_user" value="'.$this->diafan->_users->get_hash().'">
      <input type="hidden" name="form_action" value="auth_out">';
    echo '
      <div class="unit">';
    echo '<p>'.$this->diafan->_('Ваш сайт авторизован на %suser.diafan.ru%s', '<a href="https://user.diafan.ru/" target="_blank">', '</a>').'</p>';
    echo '
      </div>';
    echo '
      <button class="btn btn_blue btn_small btn_sign-out">'.$this->diafan->_('Выйти').'</button>
    </form>';
    if($auth_in)
    {
      if(! $this->diafan->defer) $this->diafan->redirect($this->diafan->_route->current_admin_link());
      else $this->diafan->defer_redirect = $this->diafan->_route->current_admin_link();
    }
  }

  /**
	 * Выводит информацию о пользователе
   *
	 * @return void
	 */
  private function user()
  {
    if(! $this->diafan->_account->is_auth())
    {
      return;
    }
    $url = $this->diafan->_account->uri('users', 'info');
    if(! $result = $this->diafan->_client->request($url, $this->diafan->_account->token))
    {
      return;
    }
    $this->diafan->attributes($result, 'name', 'fio', 'mail', 'avatar', 'created', 'cash', 'site_info', 'add_money', 'files_buy');
    $this->diafan->attributes($result["files_buy"], 'buy', 'subscription');


    echo '
    <div class="box box_height">';
    echo $result["add_money"];
    echo '
    </div>';


    echo '
    <div class="box box_height">';
    echo '
      <h2>'.$this->diafan->_('Сайт').'</h2>';
    echo $result["site_info"];
    echo '
    </div>';


    $rows = $ids = array();
    if(! empty($result["files_buy"]["buy"])) $ids = array_merge($ids, $this->diafan->array_column($result["files_buy"]["buy"], 'id'));
    if(! empty($result["files_buy"]["subscription"])) $ids = array_merge($ids, $this->diafan->array_column($result["files_buy"]["subscription"], 'id'));
    if(! empty($ids))
    {
      $names = Custom::names(); $this->diafan->_addons->recovery_custom_id();
  		foreach($names as $key => $name) $names[$key] = "'".$name."'";
  		$rows = DB::query_fetch_key("SELECT e.*, e.id as id, e.addon_id, IFNULL(c.id, 0) as `custom.id`, IFNULL(c.name, '') as `custom.name`, IF (c.id > 0 AND c.name IN (".implode(", ", $names)."), '1', '0') AS act FROM {%s} AS e LEFT JOIN {custom} AS c ON c.id=e.custom_id WHERE e.addon_id IN (%s)", 'addons', implode(',', $ids), "addon_id");
    }
    if(! empty($result["files_buy"]["buy"]))
    {
      echo '
      <div class="box box_height">';
      echo '
        <h2>'.$this->diafan->_('Покупки').'</h2>';
      echo '
        <div class="list list_addonss_buy">';
      foreach($result["files_buy"]["buy"] as $key => $value)
      {
        $this->diafan->attributes($value, 'id', 'name', 'link', 'img', 'file_rewrite');
        $row = ! empty($rows[$value["id"]]) ? $rows[$value["id"]] : array();
        echo '
          <div class="item">';
    		if (! empty($value["img"]))
    		{
    			if(empty($row["custom.id"]) || ! $row["file_rewrite"])
    			{
    				echo '<a href="'.$value["link"].'" title="'.$value["name"].'"><img src="'.$value["img"].'" border="0" alt=""></a>';
    			}
    			else
    			{
    				echo '<a href="'.BASE_PATH.ADMIN_FOLDER.'/'.$value["file_rewrite"].ROUTE_END.'" title="'.$value["name"].'"><img src="'.$value["img"].'" border="0" alt=""></a>';
    			}
    		}
    		echo '
          </div>';
      }
      echo '
        </div>';
      echo '
      </div>';
    }
    if(! empty($result["files_buy"]["subscription"]))
    {
      echo '
      <div class="box box_height">';
      echo '
        <h2>'.$this->diafan->_('Подписка').'</h2>';
      echo '
        <div class="list list_addonss_subscription">';
      foreach($result["files_buy"]["subscription"] as $key => $value)
      {
        $this->diafan->attributes($value, 'id', 'name', 'link', 'img', 'file_rewrite', 'subscription', 'auto_subscription', 'price_month');
        $row = ! empty($rows[$value["id"]]) ? $rows[$value["id"]] : array();
        echo '
          <div class="item">';
    		if (! empty($value["img"]))
    		{
    			if(empty($row["custom.id"]) || ! $row["file_rewrite"])
    			{
    				echo '<a href="'.$value["link"].'" title="'.$value["name"].'"><img src="'.$value["img"].'" border="0" alt=""></a>';
    			}
    			else
    			{
    				echo '<a href="'.BASE_PATH.ADMIN_FOLDER.'/'.$value["file_rewrite"].ROUTE_END.'" title="'.$value["name"].'"><img src="'.$value["img"].'" border="0" alt=""></a>';
    			}
    		}
        if($value["price_month"])
        {
          echo '<span class="subscription">'
            .$this->diafan->_(
              'Подписка %s / мес. оформлена до %s',
              '<b>'.number_format($value["price_month"], 0, ',', ' ').'</b>'.' <rub>&#8381;</rub>',
              date("d.m.Y", $value["subscription"])
            ).'</span>';
        }
        else
        {
          echo '<span class="subscription">'
            .$this->diafan->_('Подписка до %s',
              date("d.m.Y", $value["subscription"])
            ).'</span>';
        }

				if($value["auto_subscription"])
				{
					echo ' <span class="subscription">('.$this->diafan->_('автопродление').').</span>';
				}
    		echo '
          </div>';
      }
      echo '
        </div>';
      echo '
      </div>';
    }


    echo '
    <div class="box box_height">';
    echo '
      <h2>'.$this->diafan->_('Пользователь').'</h2>';

    echo '
      <div class="user_page"><table><tr>';
    if(! empty($result['avatar']))
    {
    	echo '<td><img src="'.$result['avatar'].'" alt="'.$result["fio"].' ('.$result["name"].')" class="avatar"></td>';
    }
    else
    {
      echo '<td><img src="'.BASE_PATH.Custom::path('img/avatar.jpg').'" alt="'.$result["fio"].' ('.$result["name"].')" class="avatar"></td>';
    }
    echo '
        <td>'
        .'<p>'.$this->diafan->_('Имя').': <b>'.$result['fio'].' ('.$result['name'].')'.'</b></p>'
        .'<p>'.$this->diafan->_('E-mail').': <b>'.$result["mail"].'</b></p>'
        .'<p>'.$this->diafan->_('Дата регистрации').': <b>'.($result['created'] ? $this->diafan->format_date($result['created'], false, false, 5) : '&nbsp;').'</b></p>'
        .'<p>'.$this->diafan->_('Код подтверждения').': <b title="'.$this->diafan->_('Ваш код подтверждения. Потребуется для некоторых автоматических операций, например, для переноса лицензий на Ваш аккаунт.').'">'.$result["code"].'</b></p>';
    echo '<p>'.$this->diafan->_('Сумма на балансе').': <b>'.number_format((float)$result["cash"], 0, ',', ' ').'</b> <i class="fa fa-rub"></i></p>';
    echo '
        </td>';
    echo '
      </tr></table></div>';
    echo '
    </div>';
  }
}
