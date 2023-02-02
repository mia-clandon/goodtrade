/**
 * @constructor
 */
function FullPageLoader() {
    if ($('div.full-page-loader').length == 0) {
        $('<div />', {class: 'full-page-loader', style: 'display: none;'}).appendTo($('body'));
    }
}

/**
 * Отображение прелодера.
 * @returns {FullPageLoader}
 */
FullPageLoader.prototype.show = function() {
    $('div.full-page-loader').show();
    return this;
};

/**
 * Скрытие прелодера.
 * @returns {FullPageLoader}
 */
FullPageLoader.prototype.hide = function() {
    $('div.full-page-loader').hide();
    return this;
};