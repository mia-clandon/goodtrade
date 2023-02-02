/**
 * Переключатель вкладок.
 * @author Артём Широких kowapssupport@gmail.com
 * $(selector).tabSwitcher();
 */
define(['jquery','jquery-ui'],function($) {
    return $.widget('ui.tabSwitcher', {
        options: {
            activeTab: 0,
            tabCount: 0,
            tabsName: '',
        },
        _create : function () {},
        _init: function () {

            var th = this;

            this.options.tabCount = parseInt(this.element.find('li').length);
            this.options.tabsName = this.element.data('tabs-name');

            this.tabSwitch(0);

            // события.
            $('body').on('click', this.element.find('li').selector, function () {
                th.options.activeTab = $(this).index();
                th.tabSwitch($(this).index());
            });
        },
        /**
         * Переключатель вкладок.
         * @param index
         */
        tabSwitch: function (index) {

            var $tab_content_id = this.options.tabsName + '-' + (index + 1).toString(),
                $tab_content = $('div[id='+$tab_content_id+']'),
                $tab = this.element.find('li').eq(index);

            if ($tab.length > 0 && !$tab.hasClass('active')) {

                // прячу все вкладки.
                $('div[id^='+this.options.tabsName + '-'+']').removeClass('active').hide();
                this.element.find('li.active').removeClass('active');

                // отображаю контент вкладки.
                $tab.addClass('active');
                $tab_content
                    .addClass('active')
                    .show();

                this._trigger('onTabSwitch', null, {tab: $tab, tab_id: $tab_content_id, tab_content: $tab_content});
            }
        },

    })
});