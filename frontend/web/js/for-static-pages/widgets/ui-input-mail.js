define(['jquery','jquery-ui','widgets/ui-input'],function($){
    return $.widget('ui.inputMail', $.ui.input,{
        _getCreateOptions: function() {
            return { pattern : /[0-9a-zA-Z@_]+/ }
        },
        _init :function() {
            console.log(this.option());
        }
    });
});