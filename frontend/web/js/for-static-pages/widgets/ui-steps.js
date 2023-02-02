define(['jquery','jquery-ui'],function($) {
   return $.widget('ui.steps',{
       options : {
           steps : null,
           nextStep : 0,
           count : 0
       },
       _init : function() {
         var $steps = this.option('steps');
           $steps.eq(0).show();
           this.option('nextStep', 1);
       },
        _create : function() {
            this._on(this.element,{'nextStep': this._next});
            this.options.steps = this.element.find('.step');
            this.options.count = this.options.steps.length;
        },
        _next: function(e,data) {
            var $el = $(e.target),
                $step = $el.hasClass('.step') ?  $el: $el.closest('.step'),
                steps = this.option('steps'),
                nextStep = this.option('nextStep'),
                $nextStep = steps.eq(nextStep);
            if($step.data('step-hide') == true) {
                $step.hide();
            }
            $nextStep.show();
            this._trigger('next');
            this.option('nextStep', ++nextStep);
            if($nextStep.data('step-skip')) {
                $nextStep.trigger('nextStep');
            }

        }
   });

});