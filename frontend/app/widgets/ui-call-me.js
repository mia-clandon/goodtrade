import './ui-input-select';

export default $.widget('ui.callMe', $.ui.inputSelect, {
    _create: function () {
        this.element.find('.input-field').mask('+0 (000) 000-00-00');
        return this._super();
    }
});