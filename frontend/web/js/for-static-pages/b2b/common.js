$(function () {
    // Значения взяты из frontend/web/scss/b2b/bootstrap/_variables.scss. Проверяйте на актуальность.
    var breakpoints = {
        xs: 0,
        sm: 360,
        md: 768,
        lg: 1360,
        xl: 1920
    };

    // Эффект фокуса на родительском блоке input'a
    $(".input:not(.input_no-focus) > input")
        .focus(function () {
            $(this).parent(".input").addClass("input_focus");
        })
        .blur(function () {
            $(this).parent(".input").removeClass("input_focus");
        });

    // Скрытие хлебных крошек и разворот формы поиска при фокусе строки поиска
    $(".bar-top .bar-top__search .input_inline.input_sm.input_no-borders > input")
        .focus(function () {
            $(this).closest(".bar-top").addClass("bar-top_no-breadcrumbs");
            $(this).closest(".bar-top__search-form").addClass("bar-top__search-form_active");
            $(this).parent(".input")
                .removeClass("input_inline input_sm input_no-borders")
                .addClass("input_no-focus");
        })
        .blur(function () {
            if ($(this).val() === "") {
                $(this).closest(".bar-top").removeClass("bar-top_no-breadcrumbs");
                $(this).closest(".bar-top__search-form").removeClass("bar-top__search-form_active");
                $(this).parent(".input")
                    .addClass("input_inline input_sm input_no-borders")
                    .removeClass("input_no-focus");
            }
        });

    // Показ формы поиска при клике на переключатель
    var $barTop = $(".bar-top"),
        $searchForm = $barTop.find(".bar-top__search-form"),
        $searchInput = $searchForm.find("input"),
        $searchToggle = $barTop.find(".bar-top__search-toggle"),
        $logoContainer = $barTop.find(".bar-top__logo-container"),
        $leftPart = $barTop.find(".bar-top__left-part"),
        $breadcrumbs = $barTop.find(".bar-top__breadcrumbs"),
        $searchBlock = $barTop.find(".bar-top__search");

    $searchToggle.on("click", function (e) {
        $searchForm.show();
        $searchInput.focus();
        $searchToggle.hide();
        $logoContainer.hide();
        $leftPart.css("flex-grow", 0);
        $breadcrumbs.hide();
        $searchBlock.css("flex-grow", 1);

        e.stopPropagation();

        $('body').on('click', function (e) {
            var $el = $(e.target);
            if ($searchForm.has($el).length || $el.is($searchForm) || $searchInput.val() !== "") {
                e.stopPropagation();
            }
            else {
                $searchForm.hide();
                $searchToggle.show();
                $logoContainer.show();
                $leftPart.css("flex-grow", 1);
                $breadcrumbs.show();
                $searchBlock.css("flex-grow", 0);
                $('body').off(e);
            }
        });
    });

    // Если поле не пустое на момент загрузки страницы, то нужно показать форму, симитировав нажатие на переключатель
    if ($searchToggle.is(":visible") && $searchInput.val() !== "") { $searchToggle.trigger("click"); }

    // Выезд левой навигационной панели
    $(".bar-top__menu-button").on("click", function () {
        $(".nav-left").addClass("nav-left_active");
        if ($(window).width() >= breakpoints.lg) {
            $(".bar-top").addClass("bar-top_shifted-right");
            $(".wrapper").addClass("wrapper_shifted-right");
        }
    });
    $(".nav-left__close-button").on("click", function () {
        $(".nav-left").removeClass("nav-left_active");
        if ($(window).width() >= breakpoints.lg) {
            $(".bar-top").removeClass("bar-top_shifted-right");
            $(".wrapper").removeClass("wrapper_shifted-right");
        }
    });

    // Абсолютное позиционирование ячеек товаров. Пришлось использовать из-за недостатков display: flex.
    // В будущем будет не нужен, когда можно будет использовать display: grid.
    $(".elements-grid:not(.elements-grid_no-masonry) > div:first-child").masonry({
        // options
        itemSelector: '.elements-grid__cell',
        columnWidth: '.elements-grid__cell'
    });

/*
    $(".checkbox").checkbox({
        checkedClass : "checkbox_checked"
    });

    $('*[data-type="modal"]').modal();

    $(".dropdown").dropdownMenu({
        classes: {
            toggleClass: "dropdown__toggle",
            menuClass: "dropdown__item",
        }
    });
*/

    // ленивая загрузка фотографий.
    try {
        $("img.lazy").lazyload({effect : "fadeIn"});
    }
    catch (Exception) {
        //do nothing.
    }

    function compare_widget() {
        var b2b = 0;
        if($('.js-compare-b2b').length > 0) {
            b2b = 1;
        }
        // update compare widget
        $.ajax({url: '/site/update-compare-widget', type: 'POST', data: {'b2b': b2b}, dataType: 'JSON'})
            .done(function(data) {
                if($('.js-compare-b2b').length > 0) {
                    var style = $('.js-compare-b2b .dropdown__item_bar-top-icon-center .dropdown__item-indicator').attr("style");

                    $('.js-compare-b2b .dropdown__item_bar-top-icon-center')
                        .html(
                            $(data).find('.dropdown__item_bar-top-icon-center').html()
                        )
                        .find('.dropdown__item-indicator').attr('style', style);

                    $('.js-compare-b2b .icon-counter').html($(data).find('.icon-counter').html());
                }
                else {
                    $('.dropdown-menu-compare .dropdown-menu-body').html(
                        $(data).find('.dropdown-menu-body').html()
                    );

                    if($('.dropdown-menu-compare .dropdown-menu-body ul').length > 0) {
                        $('.dropdown-menu-compare').removeClass('dropdown-menu-compare_empty');
                    }
                    else {
                        $('.dropdown-menu-compare').addClass('dropdown-menu-compare_empty');
                    }

                    $('li.snippets-item .action-icon-compare .action-count').html($(data).find('.action-count').html());

                }

                compare_counter();

                $('.js-compare-b2b .js-keeper')
                    .keeper()
                    .on('updateCompare', function (event, object) {
                        compare_widget();
                    });
            });
    }

    function compare_counter() {
        var counter_elem = null;
        if ($('.js-compare-b2b').length > 0) {
            counter_elem = $('.js-compare-b2b .icon-counter');
        }
        else {
            counter_elem = $('li.snippets-item .action-icon-compare .action-count');
        }
        if (parseInt(counter_elem.text()) === 0) {
            counter_elem.hide();
        }
        else {
            counter_elem.show();
        }
    }

    function favorite_widget() {
        var b2b = 0;
        if($('.js-favorite-b2b').length > 0) {
            b2b = 1;
        }
        // update compare widget
        $.ajax({url: '/site/update-favorite-widget', type: 'POST', data: {'b2b': b2b}, dataType: 'JSON'})
            .done(function(data) {
                if($('.js-favorite-b2b').length > 0) {
                    var style = $('.js-favorite-b2b .dropdown__item_bar-top-icon-center .dropdown__item-indicator').attr("style");

                    $('.js-favorite-b2b .dropdown__item_bar-top-icon-center')
                        .html(
                            $(data).find('.dropdown__item_bar-top-icon-center').html()
                        )
                        .find('.dropdown__item-indicator').attr('style', style);

                    $('.js-favorite-b2b .icon-counter').html($(data).find('.icon-counter').html());
                }
                else {
                    $('.dropdown-menu-favorite .dropdown-menu-body').html(
                        $(data).find('.dropdown-menu-body').html()
                    );

                    $('li.snippets-item .action-icon-favorite .action-count').html($(data).find('.action-count').html());

                }

                favorite_counter();

                $('.js-favorite-b2b .js-keeper')
                    .keeper()
                    .on('updateCompare', function (event, object) {
                        favorite_widget();
                    });
            });
    }

    function favorite_counter() {
        var counter_elem = null;
        if ($('.js-favorite-b2b').length > 0) {
            counter_elem = $('.js-favorite-b2b .icon-counter');
        }
        else {
            counter_elem = $('li.snippets-item .action-icon-favorite .action-count');
        }
        if (parseInt(counter_elem.text()) === 0) {
            counter_elem.hide();
        }
        else {
            counter_elem.show();
        }
    }

    function notify_counter() {
        if($('.b2b .notify-counter').length > 0) {
            var counter_elem = $('.b2b .notify-counter');
            if (parseInt(counter_elem.text()) === 0) {
                counter_elem.parent().hide();
            }
            else {
                counter_elem.parent().show();
            }
        }
    }

    compare_counter();
    favorite_counter();
    notify_counter();

    // добавление в cookies (товары/избранное).
/*
    $('.js-keeper')
        .keeper()
        .on('updateCompare', function (event, object) {
            var key = object.key;
            if (key.match("^compare")) {
                compare_widget();
            }
            else {
                favorite_widget();
            }
        });

    // js валидатор.
    $.validate({
        lang: 'ru',
        inlineErrorMessageCallback:  function($input, errorMessage, config) {
            if (errorMessage) {
                var $formControl = $input.closest('.form-control');

                $input.parent().addClass('input_danger');
                $formControl.addClass('form-control_required');

                if ($formControl.has('.form-control__message_danger').length) {
                    $formControl.find('.form-control__message_danger').html(errorMessage);
                }
                else {
                    $formControl
                        .find('.form-control__bottom-text')
                        .append($('<span/>', {
                            'class' : 'form-control__message form-control__message_danger',
                            html : errorMessage
                        }));
                }
            }
            else {
                var $formControl = $input.closest('.form-control');

                $input.parent().removeClass('input_danger');
                $formControl.removeClass('form-control_required');
                $formControl.find('.form-control__message_danger').remove();
            }
            return false; // prevent default behaviour
        },
    });
*/

    // Плавная прокрутка
    $('a[href*="#"]:not([href="#"])').click(function() {
        // Условие для виджета ui-affix-b2b, чтобы не перекрывать обработчик нажатия.
        // Необходимо сверять селектор здесь и в том виджете.
        if ($(this).is('[data-type="affix-button"]')) {
            return false;
        }

        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            let target = $(this.hash),
                $header = $("header");

            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                let headerH = 0;

                if ($header.length) {
                    headerH = $header.outerHeight();
                }

                $('html, body').animate({
                    // Координата заголовка минус шапка
                    scrollTop: target.offset().top - headerH
                }, 1000);
                return false;
            }
        }
    });

    // popup'ы с коммерческим запросом.
/*
    $('[data-type="request"]').commerce({
        // элемент при нажатии на который - будет открываться popup.
        toggleSelector: '[data-action="popup-toggle"]',
        // элемент, внутри которого находится переключатель и будет появляеться модальное окно
        wrapperSelector: '[data-type="popup-wrapper"]',
        // элемент, при нажатии на который будет будет происходить событие отмены
        cancelSelector: '[data-action="popup-close"]'
    });
*/

    // Функция очистки поля. Для функционала ниже.
    function clearField(icon) {
        $(icon).siblings("input").val("");
        $(icon).remove();
    }

    // Добавление за заполненными полями иконки удаления
    var $inputs = $(".input > input");
    $inputs.each(function (index, element) {
        if ($(element).val() !== "") {
            var $icon = $("<div>", {"class" : "icon icon_delete"});
            $(element).after($icon);

            $icon.on("click", function (e) {
                e.stopPropagation();
                clearField(e.target);
            });
        }
    });

    $inputs.on("keyup", function () {
        if ($(this).val() !== "" && !$(this).siblings(".icon_delete").length) {
            var $icon = $("<div>", {"class" : "icon icon_delete"});
            $(this).after($icon);

            $icon.on("click", function (e) {
                e.stopPropagation();
                clearField(e.target);
            });
        }
        else if ($(this).val() === "") {
            $(this).siblings(".icon_delete").remove();
        }
    });

    // Очистка поля после нажатия на иконку удаления
    $(".input .icon_delete").on("click", function (e) {
        e.stopPropagation();
        clearField(e.target);
    });
});