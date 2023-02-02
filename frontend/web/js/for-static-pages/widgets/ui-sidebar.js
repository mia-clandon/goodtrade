define(['jquery','jquery-ui'],function ($){
   return $.widget('ui.sidebar',{
      options : {
          open : false,
          show : {
              queue : false,
              duration : 800,
              easing : 'easeOutCubic',
              start : function() {
                  $(this).trigger('show.sidebar');
              },
              step : function(now, fx){
                  $(this).trigger('showstep.sidebar', now, fx);
              },
              complete : function() {
                  $(this).trigger('showdone.sidebar');
              }
          },
          hide : {
              queue : false,
              duration : 800,
              easing : 'easeOutCubic',
              start : function() {
                  $(this).trigger('hide.sidebar');
              },
              step : function(now, fx){
                  $(this).trigger('hidestep.sidebar', now, fx);
              },
              complete : function() {
                  $(this).trigger('hidedone.sidebar');
              }
          },
          controls : {
              button : null
          }
      },
      _create : function () {
          var $button = this.options.controls.button = $('.menu');
          this._on($button, {'click' : this._handleClick});
      },
      _destroy : function() {
          var $button = this.option('controls.button');
          this._off($button, 'click');
      },
      _handleClick : function(e) {
          var currentState = this.option('open');
          this.option('open', !currentState);
          e.preventDefault();
      },
      _setOption : function(key, val) {
          var $root = this.element,
              $button = this.option('controls.button'),
              hide = this.option('hide'),
              show = this.option('show');
          if(key == 'open') {
              if(val) {
                  if(!Modernizr.csstransitions) {
                      $root.animate({width: '250px'}, show);
                  } else {
                      $root.trigger('show.sidebar');
                      this._trigger('show');
                      this._addClass('is-open');
                  }
              } else {
                  if(!Modernizr.csstransitions) {
                      $root.animate({width: '0px'}, hide);
                  } else {
                      $root.trigger('hide.sidebar');
                      this._trigger('hide');
                      this._removeClass('is-open');
                  }
              }
              this._toggleClass($button, 'menu-close');
          }
          return this._super(key, val);
      }
   });
});