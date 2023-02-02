define(['jquery','jquery-ui','jquery.nanoscroller'],function($){
    return $.widget('ui.notify',{
        options : {
            open: false
        },
        controls : {
            notifyToggle : null,
            notifyBody : null,
            notifyModal : null,
            notifyContent : null
        },
        _create : function () {
            this.controls.notifyToggle = this.element.find('.notify-toggle');
            this.controls.notifyModal = this.element.find('.notify-modal');
            this.controls.notifyBody = this.controls.notifyModal.find('.notify-body');
            this.controls.notifyContent = this.controls.notifyBody.find('.notify-content');
            this._on(this.controls.notifyToggle,{click : this._toggle});
            this._on(this.controls.notifyModal,{click : function() { return false;}});
        },
        _setOption : function (key,val) {
            var self = this;
            if(key == 'open' && val == true) {
                $(document.body).one('click',function(){
                    self._toggle();
                });
            }
            this._super(key,val);
            this.refresh();
        },
        _init : function () {
            if(this.controls)
            this.controls
                .notifyBody
                .nanoScroller({
                    contentClass:'notify-content',
                    paneClass: 'notify-scrollbar',
                    sliderClass: 'notify-scrollbar-slider'
                });
        },
        _toggle : function () {
           var open =  this.option('open');
           this.option('open',!open);
           return false;
        },
        refresh : function () {
            if(this.element.hasClass('is-open')) {
                this.element.removeClass('is-open');
            } else {
                this.element.addClass('is-open');
            }
        }

    });
});