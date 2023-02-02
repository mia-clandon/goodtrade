/**
 * @constructor
 */
function Vocabulary() {
    this.title = ko.observable('');
    this.terms = [];
}

/**
 * Устанавливает название характеристики.
 * @param {string} title
 * @returns {Vocabulary}
 */
Vocabulary.prototype.setTitle = function (title) {
    this.title(title.toString());
    return this;
};

/**
 * Возвращает название характеристики.
 * @returns {string}
 */
Vocabulary.prototype.getTitle = function() {
    return this.title();
};

/**
 * Добавляет в список характеристику.
 * @param {Term} term
 * @returns {Vocabulary}
 */
Vocabulary.prototype.addTerm = function(term) {
    this.terms.push(term);
    return this;
};

/**
 * @returns {Array}
 */
Vocabulary.prototype.getTerms = function() {
    return this.terms;
};