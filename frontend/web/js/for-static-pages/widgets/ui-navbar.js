define(['jquery','jquery-ui'],function($){
    return $.widget('ui.navbar',{
        options : {
            height : 0,
            lastScrollTop : 0,
            delta : 5,
            didScroll : false
        },
        _create: function(){
            this._on(window, {'scroll' : this._handleScroll});
        },
        _init : function() {
            var $root = this.element,
                self = this;
            setInterval(function() {
                if (self.option('didScroll')) {
                    self._hasScrolled();
                    self.option('didScroll', false);
                }
            }, 250);
            this.option('height', $root.outerHeight());
        },
        _destroy : function() {
           this._off(window, 'scroll');
        },
        _handleScroll : function(e) {
            this.option('didScroll', true);
        },
        _hasScrolled : function(){
            var $root = this.element,
                lastScrollTop = this.option('lastScrollTop'),
                height = this.option('height'),
                delta = this.option('delta'),
                scrollTop = $(window).scrollTop(),
                $self = this;
            if(Math.abs(lastScrollTop - scrollTop) <= delta) {
                return;
            }
            if(scrollTop > lastScrollTop && scrollTop > height) {
                if(!$root.hasClass('is-collapsed') && !$root.is(':animated')) {
                    $root.animate({top : '-60px'},{
                        duration : 200,
                        easing : 'linear',
                        start : function() {
                            $(this).trigger('hide.navbar')
                            $self._trigger('hide');
                        },
                        step : function(now,fx) {
                            $(this).trigger('step.navbar', now, fx);
                            $self._trigger('hidestep', null, {now: now, fx: fx});
                        },
                        complete : function() {
                            $self._addClass('is-collapsed');
                            $self._trigger('hidedone');
                        }
                    });
                }
            } else  {
                if($root.hasClass('is-collapsed') && !$root.is(':animated')) {
                    $root.animate({top : '0px'},{
                        duration : 200,
                        easing : 'linear',
                        start : function() {
                            $(this).trigger('show.navbar');
                            $self._trigger('show');
                        },
                        step : function(now,fx) {
                            $(this).trigger('step.navbar', now, fx);
                            $self._trigger('showstep', null, {now: now, fx: fx});
                        },
                        complete : function() {
                            $self._removeClass('is-collapsed');
                            $self._trigger('showdone');
                        }
                    });
                }
            }
            this.option('lastScrollTop',scrollTop);
        }
    });
});