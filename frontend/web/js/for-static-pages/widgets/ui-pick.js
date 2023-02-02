define(['jquery','jquery-ui','widgets/ui-tips'],function($){
   return $.widget('ui.pick', $.ui.tips, {
       options : {
           items: [],
           max : 3,
           count : 0,
           open : false
       },
       _create : function () {
           this.options.pickBtn = this.element.find('.pick-add');
           this.options.pickItems = this.element.find('.pick-items');
           this.options.pickInput = this.element.find('.pick-input');
           this._on(this.element,{'click a': this._handleClick});
           return this._super();
       },
       _handleClick : function (e) {
           var $el = $(e.target),
               action = $el.data('action');
           if(action == 'open') {
                this._showTips();
                return false;
           }
       },
       _refresh : function () {


       },
       _addItem : function (id,value,icon) {
           var items = this.option('items');
           items.push({ id : id,
               value : value,
               icon : icon });
           this.option('items',items);
           this._trigger('add');
       },
       _removeItem : function (index) {
           var items = this.option('items');
           items.splice(index,1);
           this.option('items',items);
           this._trigger('del');
       },
       _setOption : function (key,val) {
           this._super(key,val);
           this._refresh();
       }
   });
});