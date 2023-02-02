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

    $(".news-slider").slick({
        arrows: false,
        accessibility: false,
        dots: true,
        dotsClass: "slick-dots col",
        cssEase: "cubic-bezier(.3,1,.7,1)",
        infinite: false,
        mobileFirst: true,
        responsive: [
            {
                breakpoint: breakpoints.lg,
                settings: {
                    arrows: true,
                    prevArrow: $(".news-slider-block__arrow_left"),
                    nextArrow: $(".news-slider-block__arrow_right"),
                    slidesToShow: 4
                }
            },
            {
                breakpoint: breakpoints.md,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: breakpoints.sm,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            },
        ]
    });
});