import '../common';
import '../../widgets/ui-float-element';

// Раскрываемые блоки описания и технических характеристик
$(".js-collapse").each(function (index, element) {
    if ($(element).height() > 220) {
        $(element).collapse({
            startContentHeight : 220
        });
    } else {
        $(element).addClass("is-open");
        $(element).find(".js-collapse-toggle").hide();
    }
});

// Плавающие вкладки "Описание" и "Характеристики"
$('.js-affix').affix();

// Плавающий блок с логотипом производителя
var floatToElement = $('body');

if ( $('.preview-items-block').length > 0 ) { floatToElement = $('.preview-items-block') }
else if ( $('#register-form').length > 0 ) {floatToElement = $('#register-form') }
else if ( $('footer').length > 0 ) {floatToElement = $('footer') }

$('.profile').floatElement({
    floatToElement: floatToElement
});

// Слайдер фотографий товара
$('.slider').slick({
    accessibility: false,
    adaptiveHeight: false,
    autoplay: false,
    //autoplaySpeed: 2000,
    arrows: false,
    dots: true,
    dotsClass: 'feedback-dots',
    draggable: true,
    infinite: true
});
