define('widgets/ui-calendar', ['jquery', 'jquery-ui'], function($){
    /**
     * Виджет календаря
     * @example Пример использования виджета
     * //$('.calendar').calendar();
     * @version 1.0.0
     * @exports widgets/ui-calendar
     * @author Kenzhegulov Madiyar
     */
    var calendarWidget = $.widget('ui.calendar', {
        options : {

        },
       _create : function () {
           this.body = this.element.find('tbody');
           this.head = this.element.find('thead');
           console.log('created');
       },
       _init : function () {
           this._on(this.body, {'mouseenter td' : '_handleMouseEnterCell'});
       },
       _handleMouseEnterCell : function (event) {
           var $el = $(event.currentTarget),
               index = $el.index();
           console.log($el.index(), $(event.target).index());
           this._removeClass('calendar-hover-' + this.lastIndex);
           this._addClass('calendar-hover-' + index);
           this.lastIndex = index;
           console.log(index);
       }

    });
    return calendarWidget;
});