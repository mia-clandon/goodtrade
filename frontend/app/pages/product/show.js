import "../b2b/common";
import "../../widgets/ui-affix-b2b";
import "../../widgets/ui-collapse";
import "../../widgets/ui-float-element-b2b";

// Раскрываемые блоки описания и технических характеристик
$(".js-collapse").each(function (idx, el) {
    let startContentHeight = $(el).data("startContentHeight");

    if ($(el).find(".js-collapse__content").outerHeight(true) > startContentHeight) {
        $(el).collapse({
            startContentHeight : startContentHeight,
            disposable: true,
            selectors : {
                toggle : ".js-collapse__toggle",
                content : ".js-collapse__content",
            }
        });
    } else {
        $(el).addClass("is-open");
        $(el).find(".js-collapse__content_faded").removeClass("js-collapse__content_faded");
        $(el).find(".js-collapse__toggle").hide();
    }
});

// Плавающие вкладки "Описание", "Характеристики" и "Похожие товары"
$('[data-type="affix-container"]').affix();

// Плавающий блок с логотипом производителя
let floatToElement = $("body"),
    $footer = $("footer");

if ($footer.length) { floatToElement = $footer; }

$('[data-type="float-element"]').floatElement({
    floatToElement: floatToElement
});

let dots, draggable;

// Слайдер фотографий товара
if ($(".product-photo-slider .product-photo-slider__item").length > 1) {
    dots = true;
    draggable = true;
}
else {
    dots = false;
    draggable = false;
}

$(".product-photo-slider").slick({
    accessibility: false,
    arrows: false,
    dots: dots,
    dotsClass: "slick-dots product-photo-slider__dots",
    draggable: draggable,
});

$(".firms-slider").slick({
    accessibility: false,
    arrows: false,
    dots: true,
    infinite: false,
});