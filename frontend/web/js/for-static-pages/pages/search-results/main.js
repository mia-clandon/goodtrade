/*
require.config({
    baseUrl: "../js",
    paths: {
        "jquery": "libs/jquery-1.11.3",
        "jquery-ui": "libs/jquery-ui",
        "jquery.nanoscroller" : "libs/jquery.nanoscroller.min",
        'jquery.fileupload' : 'libs/jquery.fileupload',
        'jquery.mask':'libs/jquery.mask',
        'jquery.cookie' : 'libs/jquery.cookie'

    },
    waitSeconds: 15
});
 */

require( [
        'jquery',
        'jquery-ui',
        'jquery.mask',
        'widgets/ui-layout',
        'widgets/ui-modal',
        'widgets/ui-commerce-offer',
        'widgets/ui-keeper',
        'widgets/ui-dropdown-menu',
        'widgets/ui-tumbler'

    ],function($) {
    $(document.body).layout();
    $('#input-tel').mask('+0 (000) 000 00 00');
    $('#input-mail').mask("A", {
        translation: {
            "A": { pattern: /[\w@\-.+]/, recursive: true }
        }
    });
    $('#input-bin').mask('000000000000');
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 1000);
                return false;
            }
        }
    });
    $('#popup-commerce').commerce();
    $('#modal').modal();
    $('.dropdown').dropdownMenu();
    $('.js-keeper').keeper();
    $('.tumbler').tumbler({"value" : 1});
});

