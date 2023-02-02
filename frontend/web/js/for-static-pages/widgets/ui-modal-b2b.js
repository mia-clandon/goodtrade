define( 'widgets/ui-modal-b2b', [ 'jquery', 'jquery-ui' ], function($) {

return $.widget('ui.modal', {
    options: {
        visible: false,
        modalLogoClass: "modal-logo",
        modalClass: "modal",
        modalVisibleClass: "overlay_visible"
    },
    _create: function () {
        var self = this;

        this.modalId = this.element[0].dataset.modal;
        this.logo = this.element.find("." + this.options.modalLogoClass);
        this.modal = this.element.find("." + this.options.modalClass);
        this._on($('*[data-type="modal-open"][data-modal="' + this.modalId + '"]'), {'click': '_handleClick'});
        this._on($('*[data-type="modal-close"][data-modal="' + this.modalId + '"]'), {'click': '_handleClick'});

        $('body').on('click', function (event) {
            var $target = $(event.target);

            if ($target.is(self.modal) === true || self.modal.has($target).length > 0) {
                return false;
            }

            self.element.removeClass(self.options.modalVisibleClass);
        });
    },
    _handleClick: function (event) {
        var type = event.currentTarget.dataset.type;

        event.stopPropagation();

        if (type === "modal-open") {
            this.element.addClass(this.options.modalVisibleClass);
        }

        if (type === "modal-close") {
            this.element.removeClass(this.options.modalVisibleClass);
        }
    }
});

});