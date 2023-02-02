define('widgets/ui-switcher', ['jquery', 'jquery-ui'], function($){
    var switcherWidget = $.widget('ui.formSwitcher', {
        options : {
            target : '',
            position : 0
        },
        _create : function() {
            this.forms = this.element.children(this.options.target);
        },
        next : function() {
          this.options('position', this.options.position + 1);
        },
        prev : function() {
          this.option('position', this.options.position - 1);
        },
        goto : function(index) {
          this.option('position', index);
        },
        show : function(id) {
            this.forms
                .hide()
                .filter('[id="' + id + '"]')
                .show();
        },
        _setOption : function(key, val) {
            if(key === 'position') {
                this.forms
                    .hide()
                    .eq(val)
                    .show();
            }
        }
    });
    return formSwitcherWidget;
});