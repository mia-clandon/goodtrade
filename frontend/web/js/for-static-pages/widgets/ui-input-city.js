define(['jquery','jquery-ui','widgets/ui-input-tips'],function($) {
   return $.widget('ui.inputCity', $.ui.inputTips, {
       options: {
           url : '/api/location/find',
           hidden : null,
           data : {
               cityID : 0,
               regionID : 0
           }
       },
      _create : function() {
          var $root = this.element;
          this.options.hidden = $('input[type="hidden"]', $root);
          this._on($root, {'inputcityselect' : this._handleSelect});
          return this._super();
      },
       _init : function() {
          var $hidden = this.option('hidden'),
              value = $hidden.val();
           if(value) {
               this.option('data', JSON.parse(value));
           }
           return this._super();
       },
       _destroy : function() {
           var $root = this.element;
           this._off($root, 'inputcityselect');
           return this._super();
       },
       refresh : function() {
          this.option('data', {cityID : 0, regionID : 0});
           return this._super();
       },
       _renderTips : function(data) {
           var $list = $('<ul/>'),
               className = this.option('tipsClass'),
               self = this;
           this._addClass($list, className);
           $.each(data, function(i, obj) {
               var $li = $('<li/>').appendTo($list),
                   $a = $('<a/>', {
                      'data-action' : 'select',
                       'data-value' : JSON.stringify(obj)
                   }).html(self._highlightText(obj.title))
                       .appendTo($li);
               $('<small/>', {'text' : obj.region}).appendTo($a);
           });
           return $list;
       },
       _handleSelect : function(e,data) {
           this._setValue([data.title, data.region].join(', '));
           this.option('data', {cityID : data['id'], regionID : data['region_id']});
       },
       _setOption : function(key, val) {
           var $hidden = this.option('hidden');
            if(key == 'data') {
                $hidden.val(JSON.stringify(val));
            } else if (key == 'value') {
                $hidden.val(JSON.stringify({cityID : 0, regionID : 0}));
            }
            return this._super(key, val);
       }
   });

});