define(['jquery','jquery-ui'],function($){
   return $.widget('ui.tabs', {
       options : {
            activeTab : 0,
            tabsHeader : null,
            tabsContent : null,
            tabsButtons : null
       },
       _create : function () {
            var $tabsHeader = this.options.tabsHeader = this.element.find('.tabs-header');
            this.options.tabsButtons = this.element.find('.tabs-btn');
            this.options.tabsContent = this.element.find('.tabs-content');
            this._on($tabsHeader ,{'click .tabs-btn': this._handleClick});
       },
       _init : function() {
            var defaultActiveTab = this.option('activeTab'),
                index = this.element.find('.is-active').index();
            if(index != -1) {
                this.option('activeTab',index);
            } else {
                this.option('activeTab',defaultActiveTab);
            }
       },
       _handleClick : function (e) {
            var index = $(e.target).parent().index(),
                activeTab = this.option('activeTab');
            if(activeTab != index) {
                this.option('activeTab',index);
            }
            return false;
       },
       _setOption : function (key, val) {
           this._super(key,val);
           this._refresh();
       },
       _refresh : function() {
           var $tabsContent = this.option('tabsContent'),
               $tabsButtons = this.option('tabsButtons'),
               activeTab = this.option('activeTab');
           $tabsButtons.removeClass('is-active')
                       .eq(activeTab)
                       .addClass('is-active');
           $tabsContent.find('.tabs-content-item')
               .removeClass('is-visible')
               .eq(activeTab)
               .addClass('is-visible');
       }
   });
});



