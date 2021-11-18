$(function () {

    var $win = $(window),
        $bod = $('body'),
        $doc = $(document),
        $page = $('.page-d'),
        $upper = $('.upper-d'),
        $hpanel = $('.page-d__hpanel'),
        $hboard = $('.page-d__hboard'),
        $nav_main = $('.page-d__nav_main');

    function tune()
    {
        var scrollY = $win.scrollTop();
        var h = $hpanel.outerHeight() + $hboard.outerHeight() + $nav_main.outerHeight();
        if (scrollY > h)
        {
            if (!$bod.hasClass('_toolbar-show'))
            {
                $bod.addClass('_toolbar-show');
            }
        }
        else
        {
            $bod.removeClass('_toolbar-show');
        }
    }
    setTimeout(tune, 300);
    $win.on('scroll resize', tune);

    $win.on('scroll', function () {
        ($win.scrollTop() <= 200) ? $bod.removeClass('_upper-show') : $bod.addClass('_upper-show');
    });

    $upper.on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 0);
    });

    // Меню

    $('.item-d__link .link-d__sign').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $menu_item = $(this).closest('.item-d');

        if ( !$menu_item.length ) return;

        if ($menu_item.hasClass('item-d_active'))
        {
            $menu_item
                .removeClass('item-d_active _active')
                .find('.item-d_active, ._active').removeClass('item-d_active _active');
        }
        else
        {
            $menu_item.toggleClass('_active');

            if ( ! $menu_item.hasClass('_active')) {
                $menu_item.find('.item-d_active, ._active').removeClass('item-d_active _active');
            }
        }
    });

    // Прикрепить

    $doc.on('change', '.attach-d input:file, .inpimage input:file, .inpattachment input:file', function (e) {
        var $input = $(this);
        $input.next().text($input.val());
    });

    // Количество

    $doc.on('click', '.js_count_plus', function () {
        var $input = $(this).parent().find('.js_count_input');
        if ($input.length)
		{
			var step = $input.data('step');
				step = step && step > 0 ? step : 1;

            var count = $input.val();
			if (count && count > 0)
			{
				count = +count + step;
			}
			else
			{
				count = step;
			}
			var max = $input.data('max');
			if (max && count > max)
			{
				count = max;
			}
			$input.val(count);
        }
    });
    $doc.on('click', '.js_count_minus', function () {
        var $input = $(this).parent().find('.js_count_input');
        if ($input.length)
		{
			var step = $input.data('step');
				step = step && step > 0 ? step : 1;

            var count = $input.val();
            if (count && count > step)
			{
				count = +count - step;
            }
			var min = $input.data('min');
			if (min && count < min)
			{
				count = min;
			}
			$input.val(count);
        }
    });
    $doc.on('keyup', '.js_count_input', function(e) {
        e.target.value = e.target.value.replace(/,/g,'.');
    });

    // Виджеты

    // Галерея
    (function gall_app() {

        function init(options)
        {
            return this.each(function () {
    
                if (this.swiper && this.swiper.initialized) {
                    return this;
                }
    
                var $gall = $(this);
    
                var options_init = {};
    
                var options_default = {
                    navigation: {
                        nextEl: $gall.find('.swiper-button-next').get(0),
                        prevEl: $gall.find('.swiper-button-prev').get(0),
                    },
                    on: {
    
                    },
                    pagination: {
                        el: $gall.find('.swiper-pagination').get(0),
                        type: 'bullets',
                    },
                    grabCursor: true,
                    watchOverflow: true,
                    setWrapperSize: true,
                };
    
                $.extend(options_init, options_default, options || {});
    
                var autoplay = $gall.data('gallAutoplay');
                if (autoplay)
                {
                    options_init.autoplay = {
                        delay: autoplay
                    };
                    options_init.on.init = function () {
                        $(this.el).on('mouseenter.gall_d', function () {
                            this.swiper.autoplay.stop();
                        });
                        $(this.el).on('mouseleave.gall_d', function () {
                            this.swiper.autoplay.start();
                        });
                    };
                }
    
                var show = $gall.data('gallShow');
                if (show)
                {
                    options_init.slidesPerView = show;
                }
    
                var gap = $gall.data('gallGap');
                if (gap)
                {
                    options_init.spaceBetween = gap;
                }
    
                var thumbs = $gall.data('gallThumbs');
                if (thumbs)
                {
                    var $thumbs = $(thumbs);
                    if ($thumbs.length)
                    {
                        if (!$thumbs.get(0).swiper || !$thumbs.get(0).swiper.initialized)
                        {
                            $thumbs.eq(0).gall_d();
                        }
                        options_init.thumbs = {
                            swiper: $thumbs.get(0).swiper
                        };
                    }
                }
    
                var pagintype = $gall.data('gallPagintype');
                if (pagintype)
                {
                    options_init.pagination.type = pagintype;
                }
    
                var breakpoints = $gall.data('gallBreakpoints');
                if (breakpoints)
                {
                    options_init.breakpoints = breakpoints;
                }
    
                options_init.simulateTouch = $gall.data('gallSimulateTouch') ? true : false;
    
                new Swiper(this, options_init);
    
            });
        }

        function slideTo(index)
        {
            if (index > -1)
            {
                var gall = this.get(0);
                if (gall.swiper && gall.swiper.initialized) {
                    gall.swiper.slideTo(index);
                }
            }
            return this;
        }

        var methods = {
            init: init,
            slideTo: slideTo,
        };

        $.fn.gall_d = function (method) {

            if (!window.Swiper) return false;

            if (methods[method])
            {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            }
            else if (typeof method === 'object' || !method)
            {
                return methods.init.apply(this, arguments);
            }
            else
            {
                $.error('Метод ' + method + ' не определён для jQuery.gall_d');
            }
        };

    })();
    $('.gall-d').gall_d();

    $doc.on('order_ajax_submit.after_last', function (e) {
        $('.gall-d.swiper-container:not(.swiper-container-initialized)').gall_d();
    });

    // Бегунок
    (function runner_app() {

        function normalize(vFrom, vTo, vmin, vmax)
        {
            if (isNaN(vFrom) || vFrom < vmin || vFrom > vmax - 1) {
                vFrom = vmin;
            }
            if (isNaN(vTo) || vTo < vmin + 1 || vTo > vmax) {
                vTo = vmax;
            }
            return [+vFrom, +vTo];
        };

        function text_input($inputFrom, $inputTo, $track)
        {
            var norm = normalize($inputFrom.val(), $inputTo.val());

            $inputFrom.val(norm[0]);
            $inputTo.val(norm[1]);

            $track.slider('option', 'values', norm);
        };
    
        function init(options)
        {    
            return this.each(function () {
    
                var $runner = $(this);

                var $track = $runner.find('.runner-d__track');
                if ($track.length)
                {
                    var options_init = {};
    
                    var options_default = {
                        range: true,
                        min: 0,
                        max: 500000,
                        step: 1,
                        disabled: false,
                    };
        
                    $.extend(options_init, options_default, options || {});

                    var $inputFrom = $runner.find('.runner-d__from input'),
                        $inputTo = $runner.find('.runner-d__to input');
                        
                    var vmin = $runner.data('min');
                    if (vmin > 0)
                    {
                        options_init.min = +vmin;
                    }
                    var vmax = $runner.data('max');
                    if (vmax > 0)
                    {
                        options_init.max = +vmax;
                    }
                    if (options_init.min > options_init.max) {
                        options_init.min = options_init.max;
                    }

                    options_init.values = normalize($inputFrom.val(), $inputTo.val(), vmin, vmax);
    
                    //возможен целый и дробный шаг: 1, 5, 100, 0.1, 0.5, ...
                    var step = $runner.data('step');
                    if (step > 0)
                    {
                        options_init.step = +step;
                    }

                    if ($runner.data('disabled') == 'true')
                    {
                        options_init.disabled = true;
                    }

                    options_init.create = function () {
                        $inputFrom.val($track.slider('values', 0));
                        $inputTo.val($track.slider('values', 1));

                        $runner.addClass('_runner-initialized');
                    };
                    options_init.slide = function (event, ui) {
                        $inputFrom.val($track.slider('values', 0));
                        $inputTo.val($track.slider('values', 1));
                    };
                    options_init.stop = function (event, ui) {
                        $inputFrom.val($track.slider('values', 0));
                        $inputTo.val($track.slider('values', 1));
                    }

                    $track.slider(options_init);
    
                    $inputFrom.on('input.runner_d', text_input($inputFrom, $inputTo, $track));
                    $inputTo.on('input.runner_d', text_input($inputFrom, $inputTo, $track));
                }
            });
        }

        var methods = {
            init: init,
        };

        $.fn.runner_d = function (method) {

            if (typeof $.fn.slider !== 'function') return false;
    
            if (methods[method])
            {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            }
            else if (typeof method === 'object' || !method)
            {
                return methods.init.apply(this, arguments);
            }
            else
            {
                $.error('Метод ' + method + ' не определён для jQuery.runner_d');
            }
        };

    })();
    $('.runner-d').runner_d();

    $doc.on('order_ajax_submit.after_last', function (e) {
        $('.runner-d:not(._runner-initialized)').runner_d();
    });

    // Контакт-подсказка
    (function contacts_tooltip_app() {

        function init()
        {
            return this.each(function () {
    
                var $tooltip = $(this);
    
                $tooltip.on('click.contacts_tooltip_d', function (e) {

                    var $contact = $(this);
                    if ($contact.hasClass('contact-d_active'))
                    {
						var $target = $(e.target);
						if ($target.hasClass('contact-d__icon') || $target.closest('.contact-d__icon').length)
						{
							$contact.removeClass('contact-d_active');
						}
                    }
                    else
                    {
						$contact.addClass('contact-d_active');
                    }
                });
    
            });
        }

        var methods = {
            init: init,
        };

        $.fn.contacts_tooltip_d = function (method) {

            if (methods[method])
            {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            }
            else if (typeof method === 'object' || !method)
            {
                return methods.init.apply(this, arguments);
            }
            else
            {
                $.error('Метод ' + method + ' не определён для jQuery.contacts_tooltip_d');
            }
        };

        $(document).on('click.contacts_tooltip_d', function (e) {
            
			var $contact = $(e.target).closest('.contact-d_active');

			var $contacts = $('.contact-d_active');
			$contacts.each(function(i, contact) {
				if($contact.get(0) !== contact) $contacts.eq(i).removeClass('contact-d_active');
			});
        });

    })();
    $('.contact-d_tooltip').contacts_tooltip_d();

    // Вкладки
    (function tabs_app() {

        function init()
        {
            return this.each(function () {

                var $self = $(this);
    
                var $tabs = $self.find('.tab-d'),
                    $tabnames = $self.find('.tabname-d');
    
                $self.on('click.tabs_d', '.tabname-d', function (e) {
                    e.preventDefault();
    
                    var $tabname = $(this);
    
                    if ($tabname.hasClass('tabname-d_active')) {
                        return true;
                    }
    
                    var index = $tabnames.index($tabname);
    
                    $tabnames.removeClass('tabname-d_active').eq(index).addClass('tabname-d_active');
                    $tabs.removeClass('tab-d_active').eq(index).addClass('tab-d_active');
                });
                $self.addClass('_tabs-initialized');
            });
        }

        var methods = {
            init: init,
        };

        $.fn.tabs_d = function (method) {

            if (methods[method])
            {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            }
            else if (typeof method === 'object' || !method)
            {
                return methods.init.apply(this, arguments);
            }
            else
            {
                $.error('Метод ' + method + ' не определён для jQuery.tabs_d');
            }
        };

    })();
    $('.tabs-d').tabs_d();

    $doc.on('order_ajax_submit.after_last', function (e) {
        $('.tabs-d:not(._tabs-initialized)').tabs_d();
    });

    // Прокрутка
    (function scroll_app() {

        $.fn.hasScrollBar = function() {
            var node = this.get(0);
            return {
                vertical: node.scrollHeight > node.clientHeight,
                horizontal: node.scrollWidth > node.clientWidth
            };
        }

        function set_scroll($scroll)
        {
            var scroll = $scroll.hasScrollBar();
            if (scroll.vertical || scroll.horizontal)
            {
                if (scroll.vertical) {
                    $scroll.addClass('_scroll_vertical');
                }
                if (scroll.horizontal) {
                    $scroll.addClass('_scroll_horizontal');
                }
            }
            else
            {
                $scroll.removeClass('_scroll_vertical _scroll_horizontal');
            }
        }

        function init()
        {
            return this.each(function () {

                var $scroll = $(this);
    
                set_scroll($scroll);
                $scroll.on('scroll.scroll_d', function () {
                    set_scroll($(this));
                });
                $scroll.addClass('_scroll-initialized');
            });
        }

        var methods = {
            init: init,
        };

        $.fn.scroll_d = function (method) {

            if (methods[method])
            {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            }
            else if (typeof method === 'object' || !method)
            {
                return methods.init.apply(this, arguments);
            }
            else
            {
                $.error('Метод ' + method + ' не определён для jQuery.scroll_d');
            }
        };

    })();
    $('._scroll').scroll_d();

    $doc.on('order_ajax_submit.after_last', function (e) {
        $('._scroll:not(._scroll-initialized)').scroll_d();
    });

});


/*
 *
 * 		Инструмент отладки. Создает окошко, в которое выводит данные.
 */

function echo(text) {
    var debugWindow = $("#debug-window");
    if (!debugWindow.length) {
        debugWindow = $('<div id="debug-window"></div>');
        $("body").append($('<div id="debug-window-container"></div>').append(debugWindow)).append("<style>\
		 #debug-window-container{ \
			position: fixed; \
			right: 10px; \
			bottom:0px; \
			width: 390px; \
			height: 200px; \
			z-index: 100000;\
			background-color: #000; \
			display: table-cell; \
			padding: 20px; \
			overflow:scroll; \
		} \
		\
		\
		#debug-window {\
			position:absolute;\
			bottom:0;\
			padding:20px;\
		}\
		 \
	</style>");
    }
    $("#debug-window").append(text + "<br>");
}

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
