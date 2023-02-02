require( [
    "jquery",
    "jquery-ui",
    'jquery.mask',
    'widgets/ui-input-mail',
    'widgets/ui-multiply-b2b',
    'widgets/ui-collapse',
    'widgets/ui-input-select',
    'widgets/ui-input-photo',
    'widgets/ui-input-number',
    'widgets/ui-input-city',
    'widgets/ui-input-product',
    'widgets/ui-select',
    'widgets/ui-category-new',
    'widgets/ui-autocomplete-company',
], function($) {
    $('.ui-category.activity-control').category();

    $('.input-photo').each(function () {
        $(this).inputPhoto({
            classes: {
                thumbnailsWrap: 'input-photo-thumbnails-wrap',
                thumbnails: 'input-photo-thumbnails',
                placeholder: 'input-photo-placeholder',
                inputHiddenWrap: 'input-hidden-wrap',
                addButton: 'input-photo-btn',
                delButton: 'input-photo-del'
            }
        });
    });

    $('#multiply-phone')
        .multiply({
            max : 3,
            init: function (event, node) {
                var $inputs = $(node).find('input[type="tel"]');
                $inputs.mask('+7 (000) 000 00 00');
            }
        })
        .on("add", function (event) {
            var $input = $(event.target).find('input[type="tel"]');
            $input.mask('+7 (000) 000 00 00');

            $input
                .focus(function () {
                    $(this).parent(".input").addClass("input_focus");
                })
                .blur(function () {
                    $(this).parent(".input").removeClass("input_focus");
                });
        });

    $('#multiply-email')
        .multiply({
            max : 3,
            init: function (event, node) {
                var $inputs = $(node).find('input[type="email"]');
                $inputs.mask("A", {
                    translation: {
                        "A": { pattern: /[\w@\-.+]/, recursive: true }
                    }
                });
            }
        })
        .on("add", function (event) {
            var $input = $(event.target).find('input[type="email"]');
            $input.mask("A", {
                translation: {
                    "A": { pattern: /[\w@\-.+]/, recursive: true }
                }
            });

            $input
                .focus(function () {
                    $(this).parent(".input").addClass("input_focus");
                })
                .blur(function () {
                    $(this).parent(".input").removeClass("input_focus");
                });
        });
});