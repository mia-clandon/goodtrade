require([
        "jquery",
        "slick",
    ],
    function($) {

        $('#feedback').slick({
            accessibility: false,
            adaptiveHeight : true,
            autoplay : true,
            autoplaySpeed: 2000,
            arrows : false,
            dots : true,
            dotsClass : 'feedback-dots',
            draggable : true,
            infinite: true
        });
    }
);