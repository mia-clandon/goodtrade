/**
 * @constructor
 */
function Image() {
    this.image = ko.observable('');
}

/**
 * @param {string} value
 */
Image.prototype.setImage = function (value) {
    this.image(value.toString());
    return this;
};

/**
 * @returns {string}
 */
Image.prototype.getImage = function() {
    return this.image();
};