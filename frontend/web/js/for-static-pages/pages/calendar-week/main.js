require([
      'jquery',
      'jquery-ui',
      'widgets/ui-layout',
      //'widgets/ui-calendar-month',
      'widgets/ui-modal',
      'widgets/ui-commerce-offer',
      'widgets/ui-dropdown-menu',
      'widgets/ui-tumbler'
], function ($) {
    $(document.body).layout();
    //$('.calendar').monthCalendar();
    $('.dropdown').dropdownMenu();
    $('.tumbler').tumbler({"value" : 1});
});