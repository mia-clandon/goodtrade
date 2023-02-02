require([
    "jquery",
    "jquery-ui",
    "widgets/ui-layout",
    "widgets/ui-input-photo",
    "widgets/ui-dropdown-menu",
], function($) {
    $('.dropdown').dropdownMenu();
    $('body').layout();
});