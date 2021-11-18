/**
 * Редактирование типов пользователей, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$(document).on('change', "input[name='check_all_role']", function() {
  if($(this).is(':checked')) {
    $('.checkbox_'+$(this).attr('value')).each(function () {
        $(this).prop('checked', true);
    });
  } else {
    $('.checkbox_'+$(this).attr('value')).each(function () {
        $(this).prop('checked', false);
    });
  }
});
