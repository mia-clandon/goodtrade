require( [
    "jquery",
    "jquery-ui",
    'jquery.mask',
    'widgets/ui-modal-b2b',
    'widgets/ui-dropdown-menu',
    'widgets/ui-checkbox',
    'widgets/ui-popup',
], function($) {
    $(".checkbox").checkbox({
        checkedClass : "checkbox_checked"
    });

    $('*[data-type="modal"]').modal();

    $(".dropdown").dropdownMenu({
        toogleClass: "dropdown__toggle",
        menuClass: "dropdown__item",
        /*menuBodyClass: "dropdown-menu-body",
        menuBodyFadedClass: "menu-body-faded",
        menuContentClass: "dropdown-menu-content",
        hasScrollBarClass: "has-scrollbar"*/
    });
});