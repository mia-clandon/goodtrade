// Информационный слайдер на базе плагина slick
$(function () {
    let $slider = $(".info-slider__slides-container"),
        $sliderSmPanelItems = $(".info-slider__slide .info-slider__panel .info-slider__panel-item"),
        $sliderLgPanelItems = $(".info-slider > .info-slider__panel .info-slider__panel-item"),
        slideTime = 5000;

    // Значения взяты из frontend/web/scss/b2b/bootstrap/_variables.scss. Проверяйте на актуальность.
    let breakpoints = {
        xs: 0,
        sm: 360,
        md: 768,
        lg: 1360,
        xl: 1920
    };

    // Из-за багов в работе autoplay в связке с pauseOnHover приходится самому создавать таймер
    let tTimer,
        tStamp,
        tFunc,
        tDelay,
        tRemain;

    function tCreate(func, delay) {
        tTimer = setTimeout(func, delay);
        tStamp = new Date();
        tFunc = func;
        tDelay = delay;
    }

    function tPause() {
        clearTimeout(tTimer);
        tRemain = tDelay - (new Date() - tStamp);
    }

    function tUnPause() {
        if (typeof tRemain === "number") {
            tTimer = setTimeout(tFunc, tRemain);
            tStamp = new Date();
        }
    }

    function tClear() {
        tStamp = null;
        tRemain = null;
        clearTimeout(tTimer);
    }

    $slider.on("init afterChange swipe", function () {
        if (typeof tTimer === "number") { tClear(); }

        tCreate(function () {
            $slider.slick('slickNext');
        }, slideTime);
    });

    $slider.slick({
        accessibility: false,
        arrows: false,
        pauseOnHover: false,
        waitForAnimate: false,
        mobileFirst: true,
        responsive: [
            {
                breakpoint: breakpoints.lg,
                settings: {
                    fade: true,
                    draggable: false
                }
            },
            {
                breakpoint: breakpoints.md,
                settings: {
                    fade: false,
                    draggable: true
                }
            },
            {
                breakpoint: breakpoints.sm,
                settings: {
                    fade: false,
                    draggable: true
                }
            },
        ]
    });

    $sliderSmPanelItems.add($sliderLgPanelItems)
        .find(".info-slider__panel-item-circle").css("animation-duration", slideTime / 2 + "ms");
    $sliderSmPanelItems.add($sliderLgPanelItems)
        .find(".info-slider__panel-item-circle_left").css("animation-delay", slideTime / 2 + "ms");

    // Изначальное задание активного элемента панели слайдера
    $sliderSmPanelItems
        .eq($slider.slick("slickCurrentSlide"))
        .addClass("info-slider__panel-item_active");

    $sliderLgPanelItems
        .eq($slider.slick("slickCurrentSlide"))
        .addClass("info-slider__panel-item_active");

    // Удаление активности элементов слайдера перед каждой сменой слайда
    $slider.on("beforeChange", function () {
        $sliderSmPanelItems.add($sliderLgPanelItems).removeClass("info-slider__panel-item_active");
    });

    // Задание активного элемента панели слайдера после каждой смены слайда
    $slider.on("afterChange", function () {
        $sliderSmPanelItems
            .eq($slider.slick("slickCurrentSlide"))
            .addClass("info-slider__panel-item_active");

        $sliderLgPanelItems
            .eq($slider.slick("slickCurrentSlide"))
            .addClass("info-slider__panel-item_active");
    });

    // Пауза при наведении на слайдер и панель
    $slider
        .on("mouseenter", function () {
            tPause();
            $sliderSmPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "paused");

            $sliderLgPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "paused");
        })
        .on("mouseleave", function () {
            tUnPause();
            $sliderSmPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "");

            $sliderLgPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "");
        });

    // Сбрасываем анимации при переключении слайда с помощью "свайпа"
    // (зажим кнопки мыши и проведение в строну, либо проведение пальцем по экрану смартфона)
    $slider.on("swipe", function () {
        $sliderSmPanelItems.add($sliderLgPanelItems)
            .find(".info-slider__panel-item-circle")
            .css("animation-play-state", "");
    });

    // Отслеживаем выбор слайда в панели слайдера для больших экранов
    $sliderLgPanelItems.on("click", function () {
        $slider.slick("slickGoTo", $(this).index());
    });

    // Отслеживаем переключение вкладки браузера или сворачивание окна
    $(window)
        .on("blur", function () {
            tPause();
            $sliderSmPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "paused");

            $sliderLgPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "paused");
        })
        .on("focus", function () {
            tUnPause();
            $sliderSmPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "");

            $sliderLgPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "");
        });

    $slider
        .on("beforeChange", function (event, slick, currentSlide, nextSlide) {
            let $allSlides = $slider.find(".slick-slide"),
                $slides = $slider.find(".slick-slide:not(.slick-cloned)");

            if (currentSlide === 0 && nextSlide === $slides.length - 1) {
                $allSlides.eq(0).addClass("slick-shrink");
            }

            if (currentSlide === $slides.length - 1 && nextSlide === 0) {
                $allSlides.eq($slides.length + 1).addClass("slick-shrink");
            }
        })
        .on("afterChange", function () {
            $slider.find(".slick-shrink").removeClass("slick-shrink");
        });
});