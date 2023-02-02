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

    $(".categories-slider").slick({
        arrows: false,
        accessibility: false,
        dots: true,
        dotsClass: "slick-dots col",
        infinite: false,
        useTransform: false,
        mobileFirst: true,
        responsive: [
            {
                breakpoint: breakpoints.lg,
                settings: {
                    dots: false,
                    slidesToShow: 4,
                    draggable: false
                }
            },
            {
                breakpoint: breakpoints.md,
                settings: {
                    dots: true,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: breakpoints.sm,
                settings: {
                    dots: true,
                    slidesToShow: 1
                }
            },
        ]
    });
});