require( [

    'jquery',
    'jquery-ui',
    'slick',
    'widgets/ui-dropdown-menu',

],function($) {
    $('.slider').slick({
        accessibility: false,
        adaptiveHeight : true,
        autoplay : true,
        autoplaySpeed: 2000,
        arrows : false,
        dots : true,
        dotsClass : 'dots',
        draggable : true,
        infinite: true
    });
    // выпадающий список.
    $('.dropdown').dropdownMenu();
});