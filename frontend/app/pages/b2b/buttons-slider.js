// Слайдер подкатегорий на базе плагина slick
$(function () {
    let $slider = $('.buttons-slider');

    function setSlideMaxWidth(slider) {
        let listWidth = $(slider).outerWidth();

        $(slider).find(".slick-slide").each(function () {
            $(this).css("max-width", listWidth);
        });
    }

    $slider.on("init beforeChange", function () {
        setSlideMaxWidth($slider);
    });

    $(window).on("resize", function () {
        setSlideMaxWidth($slider);
    });

    $slider.slick({
        arrows: false,
        accessibility: false,
        infinite: false,
        adaptiveHeight: true,
        //useTransform: false,
        variableWidth: true,
    });
});