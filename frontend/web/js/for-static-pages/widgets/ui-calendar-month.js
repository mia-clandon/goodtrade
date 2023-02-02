define('widgets/ui-calendar-month',['jquery', 'jquery-ui', 'widgets/ui-calendar'], function($){
    /**
     * Виджет помесячного календаря
     * @version 1.0.0
     * @exports widgets/ui-calendar-month
     */
   var monthCalendarWidget = $.widget('ui.monthCalendar', $.ui.calendar, {
       options : {

       },
       _create : function() {
           this.element.find('.calendar-event').resizable({
               grid: [176, 176],
               handles: 'e'
           });
           return this._super();
       }
   });
   return monthCalendarWidget;
});