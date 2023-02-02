define(['jquery','jquery-ui', 'jquery.cookie'], function($) {
   return $.widget('ui.actionCompare', {
      options : {
        id : 0,
        active : false,
        counter : null,
        key : ''
      },
       _create : function() {
          $.cookie.json = true;
          var $root = this.element;
         this.options.counter = $('#compare-count');
           this.options.name = $root.data('name');
          this.options.id = $root.data('id') || 0;
          this._on($root, {'click' : this._handleClick});
      },

    _handleClick : function(e) {
          var active = this.option('active'),
              $instances = $(':ui-actionCompare');
          $instances.actionCompare('option','active', !active);
          e.preventDefault();
    },
     _setOption : function(key, val) {
        var $root = this.element,
            id = this.option('id'),
            name = this.option('name'),
            array = $.cookie(name) || [],
            $counter = this.option('counter'),
            count = 0;
        if(key == 'active') {
            if(val) {
                if(!(id in array)){
                    array.push(id);
                }
                $root.addClass('is-active');
            } else {
                array.splice(array.indexOf(id), 1);
                $root.removeClass('is-active');
            }
            count = array.length;
            if(count) {
                $counter.show();
            } else {
                $counter.hide();
            }
            $counter.text(count);
            $.cookie('compare', array);
        }
        this._super(key, val);
     }
   });
});