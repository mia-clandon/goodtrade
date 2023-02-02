export default $.widget("ui.balance", {
    options: {
        values: {
            left: 0,
            right: 100
        },
        active: false,
        leftInput: null,
        rightInput: null,
        trackBtn: null,
        track: null
    },
    _create: function () {
        let $root = this.element,
            $track = this.options.track = $root.find('.balance-control-track'),
            $trackBtn = this.options.trackBtn = $track.find('.balance-control-track-btn');
        this.options.leftInput = $root.find('.balance-unit-left input');
        this.options.rightInput = $root.find('.balance-unit-right input');
        this.options.hidden = $root.children('input[type="hidden"]');

        this._on($trackBtn, {'mousedown': this._handleBtnMouseDown});
        this.options.leftInput.on('input keyup', {self : this}, this._handleInputChange);
        this.options.rightInput.on('input keyup', {self : this}, this._handleInputChange);
    },
    _init: function () {
        let $hidden = this.option('hidden'),
            value = $hidden.val();
        if (value && $.isNumeric(value)) {
            value = (value < 101 && value > -1) ? value : 0;
            this.option('values', {left: value, right: 100 - value})
        }
    },
    _destroy: function () {
        let $trackBtn = this.option('trackBtn');
        this._off($trackBtn, 'mousedown');
    },
    refresh: function () {
        this.option('values', {left: 0, right: 100});
    },
    _handleBtnMouseDown: function (e) {
        let $body = $('body');
        this._on($body, {'mousemove': this._handleBtnMouseMove});
        $body.one('mouseup', this._handleBtnMouseUp);
    },
    _handleBtnMouseUp: function (e) {
        $('body').off('mousemove');
    },
    _handleBtnMouseMove: function (e) {
        let $track = this.option('track'),
            trackWidth = $track.width(),
            offsetX = $track.offset().left,
            mousePosX = e.pageX - offsetX,
            right = Math.ceil((trackWidth - mousePosX) / trackWidth * 100),
            left = 100 - right;
        if (mousePosX < 0) {
            this.option('values', {left: 0, right: 100});
        }
        else if (mousePosX > 0 && mousePosX < trackWidth) {
            this.option('values', {left: left, right: right});
        }
        else if (mousePosX > trackWidth) {
            this.option('values', {left: 100, right: 0});
        }
    },
    _handleInputChange: function (event) {
        let self = event.data.self,
            $target = $(event.target),
            value = $target.val(),
            $leftInput = self.options.leftInput,
            $rightInput = self.options.rightInput,
            $trackBtn = self.options.trackBtn;

        if (event.type === "keyup") {
            if (event.which === 8) {
                value = value.slice(0, -2);
            } else {
                /* Сделано, чтобы можно было выделить содержимое поля,
                 иначе ниже идёт присвоение значения и сброс выделения */
                return false;
            }
        }

        value = value.replace(/%/g, "");
        value = parseInt(value);

        if (isNaN(value) || value < 0) {
            value = 0;
        }
        if (value > 100) {
            value = 100;
        }

        if (event.target === $leftInput[0]) {
            self.option('values', {left : value, right : 100 - value});
        } else {
            self.option('values', {left : 100 - value, right : value});
        }
    },
    _setOption: function (key, val) {
        let $trackBtn = this.option('trackBtn'),
            $leftInput = this.option('leftInput'),
            $rightInput = this.option('rightInput');

        if (key === 'values') {
            $leftInput.val(val.left + '%');
            $rightInput.val(val.right + '%');
            $trackBtn.css({left: val.left + '%'});

            // Генерируем событие change вручную. Необходимо для взаимодействия с другими фреймворками
            // (например, с Knockout.js)
            let evt = document.createEvent("Event");
            evt.initEvent("change", false, true);
            $leftInput[0].dispatchEvent(evt);
            $rightInput[0].dispatchEvent(evt);
        }
        return this._super(key, val);
    }
});