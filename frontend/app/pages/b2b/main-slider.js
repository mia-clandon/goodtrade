// Главный слайдер на базе плагина slick
$(function () {
    // Значения взяты из frontend/web/scss/b2b/bootstrap/_variables.scss. Проверяйте на актуальность.
    var breakpoints = {
            xs: 0,
            sm: 360,
            md: 768,
            lg: 1360,
            xl: 1920
        },
        sliderSettings = {
            arrows: false,
            accessibility: false,
            dots: true,
            dotsClass: "slick-dots main-slider__dots",
            cssEase: "cubic-bezier(.3,1,.7,1)",
            speed: 1000,
            mobileFirst: true,
            responsive: [
                {
                    breakpoint: breakpoints.lg,
                    settings: {
                        arrows: true,
                        prevArrow: $(".main-slider-block__arrow_left"),
                        nextArrow: $(".main-slider-block__arrow_right"),
                    }
                },
                {
                    breakpoint: breakpoints.sm,
                    settings: {
                        arrows: false,
                        prevArrow: "",
                        nextArrow: "",
                    }
                },
            ]
        },
        $mainSlider = $(".main-slider"),
        splitted = false;

    function splitSlide(i, el) {
        if ($(el).find(".cards-list__card").length) {
            var $clone = $(el).clone();

            $(el).children(".main-slider__slide-content").children().eq(1).remove();
            $clone.children(".main-slider__slide-content").children().eq(0).remove();
            $clone.addClass("main-slider__slide_secondary");
            $mainSlider.slick("slickAdd", $clone[0].outerHTML, $(el).index() - 1);

            splitted = true;
        }
    }

    function joinSlides(i, el) {
        if ($(el).find(".cards-list__card").length) {
            var $prev = $(el).prev().children(".main-slider__slide-content");

            $(el).children(".main-slider__slide-content").children().appendTo($prev);
            $mainSlider.slick("slickRemove", $(el).index() - 1);

            splitted = false;
        }
    }

    $mainSlider.slick(sliderSettings);

    if ($(window).outerWidth() < breakpoints.lg) {
        $mainSlider.find(".main-slider__slide:not(.slick-cloned)").each(function(i, el) {
            splitSlide(i, el);
        });
        $mainSlider.slick("slickFilter", ".main-slider__slide:not(.main-slider__slide_secondary)");
    }

    $(window).on("resize", function () {
        if ($(window).outerWidth() < breakpoints.lg && !splitted) {
            $mainSlider.find(".main-slider__slide:not(.slick-cloned)").each(function(i, el) {
                splitSlide(i, el);
            });
            $mainSlider.slick("slickFilter", ".main-slider__slide:not(.main-slider__slide_secondary)");
        }
        if ($(window).outerWidth() >= breakpoints.lg && splitted) {
            $mainSlider.slick("slickUnfilter");
            $mainSlider.find(".main-slider__slide:not(.slick-cloned)").each(function(i, el) {
                joinSlides(i, el);
            });
        }
    });

    // Исправляем ограничения CSS3
    $mainSlider
        .on("beforeChange", function (event, slick, currentSlide, nextSlide) {
            let $allSlides = $mainSlider.find(".slick-slide"),
                $slides = $mainSlider.find(".slick-slide:not(.slick-cloned)");

            if (currentSlide === 0 && nextSlide === $slides.length - 1) {
                $allSlides.eq(0).addClass("slick-shrink");
            }

            if (currentSlide === $slides.length - 1 && nextSlide === 0) {
                $allSlides.eq($slides.length + 1).addClass("slick-shrink");
            }
        })
        .on("afterChange", function () {
            $mainSlider.find(".slick-shrink").removeClass("slick-shrink");
        });
});