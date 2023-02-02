import './ui-input';

export default $.widget('ui.inputMail', $.ui.input, {
    _getCreateOptions: function () {
        return {pattern: /[0-9a-zA-Z@_]+/}
    },
    _init: function () {
    }
});