import './ui-autocomplete-patch';

/**
 * Автозаполнение поля "название компании".
 */
export default $.widget('ui.autocompleteCompany', $.ui.autocomplete, {
    options: {
        source: '/api/profile/find'
    },
    _create: function () {
        this._super();
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
                        response($.map(data.data, function (obj) {
                            return {label: obj.title, value: obj.id, bin: obj.bin};
                        }));
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
    _renderItem: function (ul, item) {
        let regexp = new RegExp('(' + this.term + ')', 'gi'),
            label = item.label.replace(regexp, '<strong>$1</strong>'),
            $wrap = $('<div/>').html(label),
            $strong = $wrap.children('strong'),
            $small = $('<small/>', {'text': 'БИН: ' + item.bin}).appendTo($wrap);
        this._addClass($strong, 'ui-menu-item-wrapper-highlight');
        this._addClass($small, 'ui-menu-item-wrapper-small');
        return $("<li>")
            .append($wrap)
            .appendTo(ul);
    },
    _renderMenu: function (ul, items) {
        this._super(ul, items);
        this._renderSkipData(ul, {label: null, value: null});
    },
    _renderSkip: function (ul, item) {
        let $li = $('<li/>'),
            $wrap = $("<div>").text('Я не нашел свою компанию');
        this._addClass($wrap, 'ui-menu-item-wrapper-muted not-find');
        return $("<li>")
            .append($wrap)
            .appendTo(ul);
    },
    _renderSkipData: function (ul, item) {
        return this._renderSkip(ul, item).data("ui-autocomplete-item", item).addClass('ui-menu-item-muted');
    }
});