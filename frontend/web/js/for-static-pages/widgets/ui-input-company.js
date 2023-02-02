define(['jquery','jquery-ui','widgets/ui-input-tips'],function($){
    return $.widget('ui.inputCompany', $.ui.inputTips,{
        options : {
            url : '/api/profile/find',
            tipsClass : 'tips-links'
        },
        _renderTips : function(data) {
            var className = this.option('tipsClass'),
               $list = $('<ul/>');
               this._addClass($list, className);
             $.each(data, function (index, obj) {
                 var $li = $('<li/>').appendTo($list),
                     $a = $('<a/>',  {
                         'data-action' : 'select',
                         'data-value' : JSON.stringify(obj),
                         'text' : obj.title
                     }).appendTo($li);
                 $('<small/>', {'text' : 'БИН: ' + obj.bin}).appendTo($a);
             });
            return $list;
        },
        _setOption : function(key, val) {
            var $root = this.element;
            if(key == 'value') {
                $root.find('a[data-action="skip"]').data('value', {text : val});
            }
            return this._super(key, val);
        }

    });
});