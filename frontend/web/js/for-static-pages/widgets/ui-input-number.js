define(['jquery','jquery-ui','widgets/ui-input'],function($){
   return $.widget('ui.inputNumber', $.ui.input,{
       _create : function () {
            return this._super();
       },
       _handleKeyUp : function (e) {
           console.log('asd');
           var $el = $(e.target);
            if(this.isValid($el)) {
                this._super(e);
            } else {
                this.refresh();
            }
       },
       isValid : function (value) {
           return Number.isInteger(+value) ? true : false;
       }
   });
});