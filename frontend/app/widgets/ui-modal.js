import './ui-switcher';

export default $.widget('ui.modal', {
    options: {
        visible: false
    },
    _create: function () {
        this.logo = this.element.find('.modal-logo');
        this.modal = this.element.find('.modal');
        this._on($('.modal-btn'), {'click': '_handleClick'});
        this._on($('.modal-close'), {'click': '_handleClickClose'});
    },
    _handleClick: function () {
        this.option('visible', true);
        return false;
    },
    _handleClickClose: function (event) {
        this.option('visible', false);
        event.preventDefault();
    },
    _setOption: function (key, val) {
        var self = this;
        if (key == 'visible') {
            if (val) {
                //noinspection JSUnresolvedVariable
                if (Modernizr.csstransitions) {
                    this._addClass('modal-visible');
                }
                else {
                    this.element.fadeIn({
                        duration: 300,
                        easing: 'easeOutQuad',
                        queue: false,
                        complete: function () {
                            self.logo.fadeIn({
                                duration: 700,
                                easing: 'easeOutCubic',
                                queue: false
                            }).animate({
                                top: '0px'
                            }, {
                                duration: 700,
                                easing: 'easeOutCubic',
                                queue: false
                            });
                            setTimeout(function () {
                                self.modal.fadeIn({
                                    duration: 700,
                                    easing: 'easeOutCubic',
                                    queue: false
                                }).animate({
                                    top: '0px'
                                }, {
                                    duration: 700,
                                    easing: 'easeOutCubic',
                                    queue: false
                                });
                            }, 200);
                        }
                    });
                }

                $('body').on('click', function (event) {
                    var $target = $(event.target);
                    if ($target.is(self.modal) === true || self.modal.has($target).length > 0) {
                        return false;
                    }
                    self.option('visible', false);
                    $('body').off(event);
                });

            }
            else {
                this._removeClass('modal-visible');
                this.element
                    .find('.ui-switcher')
                    .switcher('refresh');
                //noinspection JSUnresolvedVariable
                if (!Modernizr.csstransitions) {
                    this.logo.finish().hide().css({top: 60 + 'px'});
                    this.modal.finish().hide().css({top: 60 + 'px'});
                }
            }
        }
        return this._super(key, val);
    }
});