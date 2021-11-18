/**
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2019 OOO «Диафан» (http://www.diafan.ru/)
 */

$(".js_btn_test").click(function (e) {
    e.preventDefault();

    diafan_ajax.init({
        data: {
            action: 'test',
            module: 'cashregister'
        },
        success: function (response) {
            if (response.data)
            {
                $("#test_check").text(prepare(response.data)).addClass('ok');
            }
            if (response.error)
            {
                $("#test_check").text(prepare(response.error)).addClass('error');
            }
        }
    });
});

$('.payments_backend input[type=checkbox]').change(function(){
   if($(this).is(':checked'))
   {
        var name = $(this).attr('name');
        $(this).parents('.payments_backend').find('input[type=checkbox]').each(function(){
            if($(this).attr('name') != name)
            {
                $(this).prop('checked', false);
            }
        });
   }
});