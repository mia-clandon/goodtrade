import './ui-animation-mixin';

/**
 * Быстрый поиск.
 * @deprecated (не использовать.)
 */
export default $.widget('ui.autocomplete', $.ui.autocomplete, [$.ui.animationMixin, {
    options: {
        minLength: 3,
        popupOnFocus: true,
        position: {
            my: 'center top+15',
            at: 'center bottom'
        },
        classes: {
            'ui-autocomplete': 'input-menu',
            'ui-autocomplete-item': 'input-menu-item',
            'ui-autocomplete-item-highlight': 'input-menu-item-highlight',
            'ui-autocomplete-item-small': 'input-menu-item-small'
        }
    },
    _create: function () {
        this._super();
        this._off(this.menu.element, 'menufocus');
        this._off(this.menu.element, 'menuselect');
        this.element.parent().addClass('ui-front');
        this._on(this.menu.element, {
            'menufocus': '_handleMenuFocus',
            'menuselect': '_handleMenuSelect'
        });
        let param_name = this.element.attr('name');
        //this.element.removeAttr('name');
        //this.hidden = $('<input/>', {'type': 'hidden', 'name': param_name}).appendTo(this.element.parent());
        this.option('appendTo', this.element.parent());
        this._on({
            'focus': '_handleFocus'
        });
    },
    _handleMenuFocus: function (event, ui) {
        let label, item;

        // support: Firefox
        // Prevent accidental activation of menu items in Firefox (#7024 #9118)
        if (this.isNewMenu) {
            this.isNewMenu = false;
            if (event.originalEvent && /^mouse/.test(event.originalEvent.type)) {
                this.menu.blur();

                this.document.one("mousemove", function () {
                    $(event.target).trigger(event.originalEvent);
                });

                return;
            }
        }

        item = ui.item.data("ui-autocomplete-item");
        this._trigger("focus", event, {item: item});
        // if ( false !== this._trigger( "focus", event, { item: item } ) ) {
//
        //     // use value to match what will end up in the input, if it was a key event
        //     if ( event.originalEvent && /^key/.test( event.originalEvent.type ) ) {
        //         this._value( item.value );
        //     }
        // }

        // Announce the value in the liveRegion
        label = ui.item.attr("aria-label") || item.value;
        if (label && $.trim(label).length) {
            this.liveRegion.children().hide();
            $("<div>").text(label).appendTo(this.liveRegion);
        }
    },
    _handleMenuSelect: function (event, ui) {
        let item = ui.item.data("ui-autocomplete-item"),
            previous = this.previous,
            value = this._value();

        // Only trigger when focus was lost (click on menu)
        if (this.element[0] !== $.ui.safeActiveElement(this.document[0])) {
            this.element.trigger("focus");
            this.previous = previous;

            // #6109 - IE triggers two focus events and the second
            // is asynchronous, so we need to reset the previous
            // term synchronously and asynchronously :-(
            this._delay(function () {
                this.previous = previous;
                this.selectedItem = item;
            });
        }

        if (ui.label === null && ui.value === null) {
            this._trigger('skip', event, {item: {label: value, value: value}});
        }
        else {
            // пометка о том что элемент выбран с подсказки.
            item.from_autocomplete = true;
            this._trigger("select", event, {item: item});
        }

        // if ( false !== this._trigger( "select", event, { item: item } ) ) {
        //     this._value( item.label );
        // }

        // reset the term after the select event
        // this allows custom select handling to work properly
        this.term = value;

        this.close(event);
        this.selectedItem = item;
    },
    _handleFocus: function (event) {
        // в случае если включено отображение подсказок при нажатии на input.
        let has_items = this.menu.element.find('li').length > 0;
        if (this.options.popupOnFocus === true && has_items) {
            this._showElement(this.menu.element);
        }
    },
    _initSource: function () {
        let array, url,
            that = this;
        if ($.isArray(this.options.source)) {
            array = this.options.source;
            this.source = function (request, response) {
                response($.ui.autocomplete.filter(array, request.term));
            };
        }
        else if (typeof this.options.source === "string") {
            url = this.options.source;
            this.source = function (request, response) {
                if (that.xhr) {
                    that.xhr.abort();
                }
                that.xhr = $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        query: request.term
                    },
                    success: function (data) {
                        response(data.data);
                    },
                    error: function () {
                        response([]);
                    }
                });
            };
        }
        else {
            this.source = this.options.source;
        }
    },
    _renderItemData: function (ul, item) {
        let $item = this._renderItem(ul, item).data("ui-autocomplete-item", item);
        this._addClass($item, 'ui-autocomplete-item');
        return $item;
    },
    _renderItem: function (ul, item) {
        let regexp = new RegExp('(' + this.term + ')', 'gi'),
            label = item.label.replace(regexp, '<strong>$1</strong>'),
            $wrap = $('<div/>').html(label),
            $strong = $wrap.children('strong');
        this._addClass($wrap, 'ui-autocomplete-wrapper');
        this._addClass($strong, 'ui-autocomplete-highlight');
        return $("<li>")
            .append($wrap)
            .appendTo(ul);
    },
    _highlightText: function (text) {
        let regexp = new RegExp('(' + this.term + ')', 'gi');
        return text.replace(regexp, '<strong>$1</strong>');
    },
    _close: function (event) {
        this._off(this.document, "mousedown");

        if (this.menu.element.is(":visible")) {
            this._hideElement(this.menu.element);
            this.menu.blur();
            this.isNewMenu = true;
            this._trigger("close", event);
        }
    },
    _suggest: function (items) {
        let ul = this.menu.element.empty();
        this._renderMenu(ul, items);
        this.isNewMenu = true;
        this.menu.refresh();

        // Size and position menu
        this._showElement(ul);
        this._resizeMenu();
        ul.position($.extend({
            of: this.element
        }, this.options.position));

        if (this.options.autoFocus) {
            this.menu.next();
        }

        // Listen for interactions outside of the widget (#6642)
        this._on(this.document, {
            mousedown: "_closeOnClickOutside"
        });
    }
}]);