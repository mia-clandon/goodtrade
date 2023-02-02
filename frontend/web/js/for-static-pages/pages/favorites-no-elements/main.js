require( [
    "jquery",
    "jquery-ui",
    'widgets/ui-layout',
    'widgets/ui-collapse',
    'widgets/ui-keeper',
    'widgets/ui-dropdown-menu',
    'widgets/ui-choice'
], function($) {
    $(document.body).layout();
    $('.js-collapse').collapse();
    $('.js-keeper').keeper();
    $('.dropdown').dropdownMenu();
    $('.choice').choice();
});