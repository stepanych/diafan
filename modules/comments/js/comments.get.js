/**
 * JS-сценарий модуля
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$(document).on('click', '.js_comments_show_form, .comments_show_form', function(){
	$(this).next('.js_comments_block_form, .comments_block_form').toggle();
});

$(document).on('click', '.js_comments_show_form', function (e) {
	e.preventDefault();

	var $button = $(this);

	var target = $button.data('target') || $button.attr('href');
	if (target)
	{
		$target = $(target);
		if ($target.length)
		{
			$target.toggle();
			//$target.toggleClass('_hidden');
			$button.toggleClass('_active');

			var isVisible = $target.is(':visible');

			var focusNode = $button.data('focusNode') || false;
			if (focusNode)
			{
				var $focusNode = $target.find(focusNode);
				if ($focusNode.length)
				{
					$focusNode.focus();
					//помещаем курсор в конец строки
					var val = $focusNode.val();
					$focusNode.val(val + ' ');
					$focusNode.val(val);
				}
			}
			var textNode = $button.data('textNode') || false;
			if (textNode)
			{
				var $textNode = $button.find(textNode).first();
				if ($textNode.length)
				{
					var textShow = $button.data('textShow');
					var textHide = $button.data('textHide');

					if (isVisible)
					{
						if (textHide) $textNode.text(textHide);
					}
					else
					{
						if (textShow) $textNode.text(textShow);
					}
				}
			}
			// if (!isVisible)
			// {
			//     if ($button.data('empty')) $target.empty();
			// }
			if ($button.data('remove')) $button.remove();
		}
	}
});
diafan_ajax.success['comments_add'] = function (form, result) {
	
	if (result.result == 'success')
	{
		$('.' + $(form).attr("id")).closest('._hidden').removeClass('_hidden').show();
	}
}