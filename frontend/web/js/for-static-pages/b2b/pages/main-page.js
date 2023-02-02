require( [
    "jquery",
    "jquery-ui",
    'jquery.mask',
    'widgets/ui-modal-b2b',
    'widgets/ui-dropdown-menu-b2b',
    'widgets/ui-checkbox',
    'widgets/ui-popup',
], function($) {
    $(".checkbox").checkbox({
        checkedClass : "checkbox_checked"
    });

    $('*[data-type="modal"]').modal();

    $(".dropdown").dropdownMenu();

    // Это скрипт только для показа анимации иконки галочки у кнопок
    $(".button_action").on("click", function (e) {
        $(e.currentTarget).toggleClass("is-active");
    });

    // Изменение вида шапки на главной при загрузке или прокрутке
    function makeWhiteHeader() {
        $(".header-absolute .bar-top").removeClass("bar-top_inverted");
        $(".header-absolute .bar-top .bar-top__menu-button").removeClass("bar-top__menu-button_white");
        $(".header-absolute .bar-top .logo").removeClass("logo_sm-white").addClass("logo_sm");
        $(".header-absolute .bar-top .icon_search-white").removeClass("icon_search-white").addClass("icon_search");
        $(".header-absolute .bar-top .bar-top__search .input").removeClass("input_white");
        $(".header-absolute .bar-top .bar-top__search .input .icon").removeClass("icon_search-white").addClass("icon_search");
        $(".header-absolute .bar-top .icon_favorite-white").removeClass("icon_favorite-white").addClass("icon_favorite");
        $(".header-absolute .bar-top .icon_comparison-white").removeClass("icon_comparison-white").addClass("icon_comparison");
        $(".header-absolute .bar-top .icon_deal-white").removeClass("icon_deal-white").addClass("icon_deal");
        $(".header-absolute .bar-top .icon_notice-white").removeClass("icon_notice-white").addClass("icon_notice");
        $(".header-absolute .bar-top .bar-top__user-avatar_white").removeClass("bar-top__user-avatar_white");
        $(".header-absolute .bar-top .bar-top__buttons-block .button_link-white").removeClass("button_link-white").addClass("button_link");
        $(".header-absolute .bar-top .bar-top__buttons-block .button").not(".button_link").removeClass("button_white");
    }
    function makeTransparentHeader() {
        $(".header-absolute .bar-top").addClass("bar-top_inverted");
        $(".header-absolute .bar-top .bar-top__menu-button").addClass("bar-top__menu-button_white");
        $(".header-absolute .bar-top .logo").addClass("logo_sm-white").removeClass("logo_sm");
        $(".header-absolute .bar-top .icon_search").addClass("icon_search-white").removeClass("icon_search");
        $(".header-absolute .bar-top .bar-top__search .input").addClass("input_white");
        $(".header-absolute .bar-top .bar-top__search .input .icon").addClass("icon_search-white").removeClass("icon_search");
        $(".header-absolute .bar-top .icon_favorite").addClass("icon_favorite-white").removeClass("icon_favorite");
        $(".header-absolute .bar-top .icon_comparison").addClass("icon_comparison-white").removeClass("icon_comparison");
        $(".header-absolute .bar-top .icon_deal").addClass("icon_deal-white").removeClass("icon_deal");
        $(".header-absolute .bar-top .icon_notice").addClass("icon_notice-white").removeClass("icon_notice");
        $(".header-absolute .bar-top .bar-top__user-avatar").addClass("bar-top__user-avatar_white");
        $(".header-absolute .bar-top .bar-top__buttons-block .button_link").addClass("button_link-white").removeClass("button_link");
        $(".header-absolute .bar-top .bar-top__buttons-block .button").not(".button_link-white").addClass("button_white");
    }
    if ($(window).scrollTop() !== 0) {
        makeWhiteHeader();
    }
    else {
        makeTransparentHeader();
    }
    $(window).on("scroll", function () {
        if ($(this).scrollTop() !== 0) {
            makeWhiteHeader();
        }
        else {
            makeTransparentHeader();
        }
    });

});