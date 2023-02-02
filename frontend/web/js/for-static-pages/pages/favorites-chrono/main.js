require( [
    "jquery",
    "jquery-ui",
    'jquery.mask',
    'widgets/ui-layout',
    'widgets/ui-collapse',
    'widgets/ui-keeper',
    'widgets/ui-dropdown-menu',
    'widgets/ui-choice',
    'widgets/ui-tumbler',
    'widgets/ui-callback',
    'widgets/ui-commerce-offer'
], function($) {
    $(document.body).layout();
    $('.js-collapse').collapse();
    $('.js-keeper').keeper();
    $('.dropdown').dropdownMenu();
    $('.choice').choice();
    $('.tumbler').tumbler();
    // popup с обратным звонком.
    $('div[data-type="callback"]').callback();
    $('input[type="tel"]').mask('+0 (000) 000 00 00');
});