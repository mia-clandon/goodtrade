//TODO : необходимо реализовать API

define('widgets/ui-notes', ['jquery', 'jquery-ui'], function($){
    /**
     * Виджет заметок
     * @author Kenzhegulov Madiyar
     * @version 1.0.0
     * @example Пример использования виджета
     * // $('.notes').notes();
     * @exports widgets/ui-notes
      */
   var notesWidget =  $.widget('ui.notes',[$.ui.formResetMixin, {
      options : {
          isFocused : false
      },
      _create : function() {
          this.textarea = this.element.find('textarea');
          this.placeholder = this.element.find('.notes-placeholder');
      },
      _init : function() {
          if(this.textarea.val()) {
              this._addClass('has-text')
          }
          this._on(this.placeholder, {
              'click' : '_handleClick'
          });
          this._on(this.textarea, {
              'blur' : '_handleBlur',
              'change' : '_handleChange'
          });
          this._bindFormResetHandler();
      },
        /**
         * Обновить значения
         */
      refresh : function () {
          this.textarea.val('');
          this.send();
      },
        /**
         * Послать изменения на сервер
         * @event send
         */
      send : function () {
          var value = this.textarea.val();
          console.log('send', value);
          this._trigger('send', null, value);
      },
      _destroy : function() {
          this.option('isFocused', false);
          this._off(this.placeholder, 'click');
          this._off(this.textarea, 'blur');
          this.textarea.remove();
          this._unbindFormResetHandler();
      },
      _handleChange : function(event) {
          var element = $(event.target),
              value = element.val();
          if(value) {
              this._addClass('has-text');
          } else {
              this._removeClass('has-text');
          }
          this.send();
          event.preventDefault();
      },
      _handleClick : function(event) {
           this.option('isFocused', true);
           this.textarea.focus();
           event.preventDefault();
      },
      _handleBlur : function(event) {
          this.option('isFocused', false);
          event.preventDefault();
      },
      _setOption : function(key, val) {
          if(key === 'isFocused') {
              if(val) {
                  this._addClass('notes-focus');
              } else {
                  this._removeClass('notes-focus');
              }
          }
          return this._super(key, val);
      }
   }]);
    return notesWidget;
});