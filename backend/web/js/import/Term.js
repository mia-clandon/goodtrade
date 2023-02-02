/**
 * @constructor
 */
function Term() {
    this.title = ko.observable('');
}

/**
 * Устанавливает значение характеристики.
 * @param {string} value
 */
Term.prototype.setTitle = function (value) {
    this.title(value.toString());
    return this;
};

/**
 * Возвращает значение характеристики.
 * @returns {string}
 */
Term.prototype.getTitle = function() {
    return this.title();
};