// Информационный слайдер на базе плагина slick
$(function () {
    var $slider = $(".info-slider__slides-container"),
        $sliderPanelItems = $(".info-slider__panel-item"),
        slideTime = 5000;

    // Из-за багов в работе autoplay в связке с pauseOnHover приходится самому создавать таймер
    var tTimer,
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
        fade: true,
        pauseOnHover: false,
        waitForAnimate: false
    });

    $sliderPanelItems.find(".info-slider__panel-item-circle").css("animation-duration", slideTime / 2 + "ms");
    $sliderPanelItems.find(".info-slider__panel-item-circle_left").css("animation-delay", slideTime / 2 + "ms");

    // Изначальное задание активного элемента панели слайдера
    $sliderPanelItems
        .eq($slider.slick("slickCurrentSlide"))
        .addClass("info-slider__panel-item_active");

    // Удаление активности элементов слайдера перед каждой сменой слайда
    $slider.on("beforeChange", function () {
        $sliderPanelItems.removeClass("info-slider__panel-item_active");
    });

    // Задание активного элемента панели слайдера после каждой смены слайда
    $slider.on("afterChange", function () {
        $sliderPanelItems
            .eq($slider.slick("slickCurrentSlide"))
            .addClass("info-slider__panel-item_active");
    });

    // Пауза при наведении на слайдер и панель
    $slider
        .on("mouseenter", function () {
            tPause();
            $sliderPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "paused");
        })
        .on("mouseleave", function () {
            tUnPause();
            $sliderPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "");
        });

    // Сбрасываем анимации при переключении слайда с помощью "свайпа" (зажим кнопки мыши и проведение в строну, либо проведение пальцем по экрану смартфона)
    $slider.on("swipe", function () {
        $sliderPanelItems
            .find(".info-slider__panel-item-circle")
            .css("animation-play-state", "");
    });

    // Отслеживаем выбор слайда в панели слайдера
    $sliderPanelItems.on("click", function () {
        $slider.slick("slickGoTo", $(this).index());
    });

    // Отслеживаем переключение вкладки браузера или сворачивание окна
    $(window)
        .on("blur", function () {
            tPause();
            $sliderPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "paused");
        })
        .on("focus", function () {
            tUnPause();
            $sliderPanelItems
                .eq($slider.slick("slickCurrentSlide"))
                .find(".info-slider__panel-item-circle")
                .css("animation-play-state", "");
        });
});