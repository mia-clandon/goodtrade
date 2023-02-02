define(['jquery','jquery-ui','widgets/ui-steps'],function ($) { // TODO: прикрутить slick
    return $.widget('ui.register', {
        options : {
          step : 1,
          sections: null,
          max : 0,
          progress : null,
          nextBtn : null,
          prevBtn : null,
          doneBtn : null
        },
        _create : function () {
            var $root = this.element,
            $sections = this.options.sections = $('section', $root),
            $next = this.options.nextBtn = $('#register-next'),
            $prev = this.options.prevBtn = $('#register-prev'),
            $done = this.options.doneBtn = $('#register-done');
            this.option.wrap = $root.find('.register-wrap');
            this.options.max = $sections.length;
            this.options.progress = $('#register-progress');
            this._on($done, {click : this.done});
            this._on($next, {click : this.nextStep});
            this._on($prev, {click : this.prevStep});
        },
        _init : function () {
            var $sections = this.option('sections');
            $sections.first().show();
        },
        nextStep : function () {

            var $progress = this.option('progress'),
                step = this.option('step'),
                max = this.option('max');
            $progress
                .find('.is-active')
                .removeClass('is-active')
                .addClass('is-success')
                .next()
                .addClass('is-active');
            this.option('step',++step);
        },
        prevStep : function () {
            var $progress = this.option('progress'),
                step = this.option('step');
            if(step == 1) return;
            $progress
                .find('.is-active')
                .removeClass('is-active')
                .prev()
                .removeClass('is-success')
                .addClass('is-active');
            this.option('step', --step);
        },
        done : function() {
          var $form = this.element;
            $form.submit();
        },
        _setOption : function (key,val) {
            var $sections = this.option('sections'),
                max = this.option('max'),
                $next = this.option('nextBtn'),
                $done = this.option('doneBtn');
            if(key == 'step') {
                if(val == max) {
                    $next.hide();
                    $done.attr('style','display:inline-bloc !important');
                } else {
                    $next.show();
                    $done.removeAttr('style');
                }
                $sections.hide()
                    .eq(val - 1)
                    .show();
            }
            return this._super(key,val);
        }

    });
});