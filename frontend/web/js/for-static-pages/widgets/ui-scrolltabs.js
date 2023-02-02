define( 'widgets/ui-scrolltabs', [ 'jquery', 'jquery-ui', 'jquery.scrollmagic', 'widgets/jq-smooth-scroll' ], function ($) {

    var scrollTabWidget = $.widget( 'ui.scrollTabs', {
        options : {

        },
        _create : function () {
            var self = this;
            this.controller = new $.ScrollMagic.Controller();
            this.tabs = this.element
                .find('.tabs-btn')
                .children('a');
            this.header = this.element.children('.tabs-header');
            this.content = this.element.children('.ui-scrolltabs-content').css({position : 'absolute', top: 0});
            this.element.css({position: 'relative'});
            this.header.clone().prependTo(this.content);
            var offset = this.header.offset().top;
            console.log(offset);
            this.scenes = new $.ScrollMagic.Scene({triggerElement: self.header.get(0), duration: '100%', offset: 259})
                .setPin(this.header.get(0))
                .addTo(this.controller);
            this.newScene = new $.ScrollMagic.Scene({ triggerElement : '#js-tab1', duration : '100%', offset: 259})
                .setClassToggle($('.tabs-header > .tabs-btn:eq(0)').get(0), "is-active") // add class toggle
                .addTo(this.controller);
            this.newNewScene = new $.ScrollMagic.Scene({ triggerElement : '#js-tab2', duration : '100%', offset: 259})
                .setClassToggle($('.tabs-header > .tabs-btn:eq(1)').get(0), "is-active") // add class toggle
                .addTo(this.controller);

        }
    });
    return scrollTabWidget;
});