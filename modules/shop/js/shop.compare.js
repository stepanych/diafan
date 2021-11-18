/**
 * JS-сценарий сравнения товаров
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */



$(function () {

	var $doc = $(document);

	var $shop_compare = $('.section-d_shop_compare');

	var $toggle = $('.compare-d__button_toggle');
	var $compare_params = $shop_compare.find('.compare-d__params');
	
	function tune()
	{
		var top = $('.element-d_shop_item_compare .element-d__images').first().outerHeight(true);
		$compare_params.css('margin-top', top);

		$('.compare-d__param').each(function () {

			var hmax = 0;
			var $params = $('.param-d_compare[data-param_id=' + $(this).data('param_id') + ']');
			$params.each(function () {

				var h = $(this).removeAttr('style').height();
				if (h > hmax)
				{
					hmax = h;
				}
			});
			$params.height(hmax);
		});
	}
	setTimeout(tune, 300);

	var is_diff = true;
	$toggle.on('click', function (e) {
		$('.param-d_compare:not(._diff)').toggle();

		var name = '';
		if (is_diff)
		{
			name = $toggle.data('name1');
		}
		else
		{
			name = $toggle.data('name2');
			tune();
		}
		if (name)
		{
			$toggle.find('.button-d__name').text(name);
		}
		is_diff = !is_diff;
	});

	$shop_compare.on('mouseenter', '.param-d_compare', function () {

		var $param = $(this);
		if (!$param.hasClass('_colored'))
		{
			$('.param-d_compare._colored').removeClass('_colored');
			$('.param-d_compare[data-param_id="' + $param.data('param_id') + '"]').addClass('_colored');
		}
	});
	$shop_compare.on('mouseleave', '.param-d_compare', function (e) {

		if (!$(e.relatedTarget).hasClass('.param-d_compare')) {
			$('.param-d_compare._colored').removeClass('_colored');
		}
	});

	$doc.on('click', '.js_shop_compare_delete', function () {

		var $delete = $(this);

		var id = $delete.data('id');
		var site_id = $delete.data('site_id');

		if (!id || !site_id) return;

		$.ajax({
			method: 'POST',
			data: {
				module: 'shop',
				action: 'compare_goods',
				id: id,
				site_id: site_id,
				add: 0,
				ajax: 1
			},
			beforeSend: function () {
				$delete.attr('disabled', 'disabled');
			}
		})
		.done(function (result, statusText, xhr) {

			try
			{
				var response = $.parseJSON(result);
			}
			catch (err)
			{
				return false;
			}

			if (response.result == 'ok')
			{
				$delete.closest('.slide-d').remove();

				var gall = $('.gall-d').get(0);
				if (gall.swiper && gall.swiper.initialized)
				{
					gall.swiper.update();
					switch (gall.swiper.slides.length)
					{
						case 0:
							$('.section-d_shop_compare').empty();
							break;
						case 1:
							$('.param-d_compare').removeClass('_diff _colored');
							$toggle.remove();
							break;
					}
				}
			}
			return diafan_ajax.result(null, result);
		});
	});
});
