define( 'widgets/ui-form-steps', [ 'jquery', 'jquery-ui', 'jquery.steps' ], function( $ ) {
   return $.widget('ui.formSteps', {
      _create : function () {
          var self = this;
          this.element.steps({
              headerTag: "h3",
              bodyTag: "section",
              transitionEffect: 'fade',
              autoFocus: true,
              titleTemplate: "#title#",
              enableCancelButton: true,
              labels: {
                  current: '',
                  next: 'Пропустить',
                  cancel: 'Отмена',
                  finish: 'Готово',
                  previous: 'Назад'
              },
              onCanceled : $.proxy( self._handleCancel, self ),
              onStepChanged : $.proxy( self._handleChange, self ),
              onInit: $.proxy( self._handleInit, self )
          });
      },
       _handleInit : function( event, currentIndex ) {
           this.steps = $('.steps');
           this.actions = $('.actions');
           this.buttons = this.actions.find("a[href^='#']");
           this.previous = this.buttons.filter('[href="#previous"]').hide();
           this.cancel = this.buttons.filter('[href="#cancel"]');
           this.next = this.buttons.filter('[href="#next"]');
           this.preloader = $('#preloader');
           this.buttons.addClass('btn btn-link');
           this.progressItem = $('.progress-item');
           this.preloader.delay(200).fadeOut();
       },
       _handleChange : function ( event, currentIndex, priorIndex ) {
           if( currentIndex === 0) {
               this.cancel.show();
               this.progressItem.eq(1)
                   .removeClass('is-success')
                   .addClass('is-active');
               this.progressItem.eq(2)
                   .removeClass('is-active');
               this.previous.hide();
           } else if ( currentIndex === 1 ) {
               this.progressItem.eq(2).addClass('is-active');
               this.progressItem.eq(1)
                   .removeClass('is-active')
                   .addClass('is-success');
               this.previous.show();
               this.cancel.hide();
           }
       },
       _handleCancel : function ( event ) {
           window.history.back();
       }
   });
});