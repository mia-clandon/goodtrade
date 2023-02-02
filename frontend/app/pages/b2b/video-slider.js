// Новостной слайдер на базе плагина slick
$(function () {
    // Значения взяты из frontend/web/scss/b2b/bootstrap/_variables.scss. Проверяйте на актуальность.
    var breakpoints = {
        xs: 0,
        sm: 360,
        md: 768,
        lg: 1360,
        xl: 1920
    };

    $(".video-slider").slick({
        arrows: false,
        accessibility: false,
        dots: true,
        dotsClass: "slick-dots video-slider__dots",
        infinite: false,
        useTransform: false,
        mobileFirst: true,
        responsive: [
            {
                breakpoint: breakpoints.lg,
                settings: {
                    arrows: true,
                    dots: false,
                    prevArrow: $(".video-block__arrow_left"),
                    nextArrow: $(".video-block__arrow_right"),
                    variableWidth: true,
                }
            },
            {
                breakpoint: breakpoints.sm,
                settings: {
                    arrows: false,
                    dots: true,
                    variableWidth: false,
                }
            },
        ]
    });
});