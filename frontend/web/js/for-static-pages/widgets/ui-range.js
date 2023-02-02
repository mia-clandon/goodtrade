define(["jquery","jquery-ui"], function($) {
    return $.widget("ui.range", {
        options: {
            type: "range", // range, balance, value
            valueClass: "range_value",
            balanceClass: "range_balance",
            leftInput: null,
            rightInput: null,
            hidden: null,
            leftTrackBtn: null,
            rightTrackBtn: null,
            track: null,
            checkbox: null,
            data: {},

            selectors: {
                trackContainer : ".range__track-container",
                track : ".range__track",
                leftTrackBtn : ".range__button:first-child",
                rightTrackBtn : ".range__button:last-child",
                trackLine : ".range__line",
                leftInput : ".input-group .input:first-child input",
                rightInput : ".input-group .input:last-child:not(:first-child) input",
                checkbox : ".range__checkbox :checkbox",
            }
        },
        _create: function () {
            var $root = this.element,
                $track = this.options.track = $root.find(this.options.selectors.track),
                $leftTrackBtn = this.options.leftTrackBtn = $track.find(this.options.selectors.leftTrackBtn),
                $rightTrackBtn = this.options.rightTrackBtn = $track.find(this.options.selectors.rightTrackBtn),
                $trackLine = this.options.trackLine = $track.find(this.options.selectors.trackLine),
                $leftInput = this.options.leftInput = $root.find(this.options.selectors.leftInput),
                $rightInput = this.options.rightInput = $root.find(this.options.selectors.rightInput),
                $hidden = this.options.hidden = $root.children("input[type=hidden]"),
                $checkbox = this.options.checkbox = $root.find(this.options.selectors.checkbox),
                data = this.options.data = {
                    min : parseFloat($hidden[0].dataset.min),
                    max : parseFloat($hidden[0].dataset.max),
                    unit : $hidden[0].dataset.unit,
                    decimals : parseInt($hidden[0].dataset.decimals),
                };

            // Задаём значения по умолчанию, если они не были указаны в data-атрибутах тега input[type="hidden"]
            if (isNaN(data.min)) { data.min = this.options.data.min = 0; }
            if (isNaN(data.max)) { data.max = this.options.data.max = 100; }
            if (data.unit === undefined) { data.unit = this.options.data.unit = ""; }
            if (isNaN(data.decimals)) { data.decimals = this.options.data.decimals = 0; }
            if ($leftInput.val() === "") { $leftInput.val(data.min); }
            if ($rightInput.val() === "") { $rightInput.val(data.max); }

            if (data.min < 0 && data.max > 0) {
                this.options.data.total = data.max + Math.abs(data.min);
            } else if (data.min < 0 && data.max < 0) {
                this.options.data.total = data.max + data.min;
            } else {
                this.options.data.total = data.max - data.min;
            }

            if (this.options.type !== "range" && this.options.type !== "balance" && this.options.type !== "value") {
                this.options.type = "range";
            }

            if (this.options.type === "value" || ($checkbox.length > 0 && !this.options.checkbox[0].checked)) {
                this.options.removedInput = $rightInput.closest(".input").detach();
                $rightTrackBtn.hide();
                $trackLine.hide();
            }

            if (this.options.type === "balance") {
                $rightTrackBtn.hide();
                $trackLine.hide();
            }

            this._on($leftTrackBtn, {"mousedown": this._handleBtnMouseDown});
            this._on($rightTrackBtn, {"mousedown": this._handleBtnMouseDown});

            this._on($leftInput, {
                "input": this._handleInputChange,
                "keyup": this._handleInputChange,
            });
            this._on($rightInput, {
                "input": this._handleInputChange,
                "keyup": this._handleInputChange,
            });
            this._on($checkbox[0], {"click": this._handleCheckboxClick});
        },
        _init: function () {
            var leftVal = parseFloat(this.option("leftInput").val()),
                rightVal = parseFloat(this.option("rightInput").val()),
                data = this.option("data");

            if ( (leftVal && $.isNumeric(leftVal)) || (rightVal && $.isNumeric(rightVal)) ) {
                leftVal = (leftVal >= data.min && leftVal <= data.max) ? leftVal : data.min;
                rightVal = (rightVal >= data.min && rightVal <= data.max) ? rightVal : data.max;

                if (leftVal > rightVal) { leftVal = rightVal; }

                this.option("values", {left: leftVal, right: rightVal});
            }
        },
        _destroy: function () {
            var $leftTrackBtn = this.option("leftTrackBtn"),
                $rightTrackBtn = this.option("rightTrackBtn"),
                $leftInput = this.option("leftInput"),
                $rightInput = this.option("rightInput");

            this._off($leftTrackBtn, "mousedown");
            this._off($rightTrackBtn, "mousedown");
            this._off($leftInput, "input");
            this._off($leftInput, "keyup");
            this._off($rightInput, "input");
            this._off($rightInput, "keyup");
        },
        _handleBtnMouseDown: function (e) {
            var $body = $("body");

            this._on($body, {"mousemove": this._handleBtnMouseMove});
            this._on($body, {"mouseup": this._handleBtnMouseUp});
            this.options.currentBtn = $(e.target);
        },
        _handleBtnMouseUp: function () {
            $("body").off("mousemove");
            $("body").off("mouseup");
        },
        _handleBtnMouseMove: function (e) {
            var $track = this.option("track"),
                $leftTrackBtn = this.option("leftTrackBtn"),
                $rightTrackBtn = this.option("rightTrackBtn"),
                $currentBtn = this.option("currentBtn"),
                trackWidth = $track.width(),
                trackPosX = $track.offset().left,
                mousePosX = e.pageX - trackPosX,
                btnPos = 0;

            if (this.options.type === "range") {
                // Смещение курсора на половину ширины кнопки бегунка, чтобы курсор был по центру
                if ($currentBtn.is($leftTrackBtn)) {
                    mousePosX = mousePosX + $leftTrackBtn.width() / 2;
                } else {
                    mousePosX = mousePosX - $rightTrackBtn.width() / 2;
                }
            }

            // Позиция кнопки в процентах, округлённая до 5 знака после запятой
            btnPos = Math.round(mousePosX / trackWidth * 100 * 100000) / 100000;

            if (mousePosX < 0) {
                this.option("currentBtnPos", 0);
            }
            else if (mousePosX > 0 && mousePosX < trackWidth) {
                this.option("currentBtnPos", btnPos);
            }
            else if (mousePosX > trackWidth) {
                this.option("currentBtnPos", 100);
            }
        },
        _handleInputChange: function (e) {
            var value = $(e.target).val(),
                $leftInput = this.options.leftInput,
                $rightInput = this.options.rightInput,
                data = this.options.data;

            if (e.type === "keyup") {
                if (e.which === 8 && data.unit.length !== 0) {
                    value = value.slice(0, -(data.unit.length + 1));
                } else {
                    /* Сделано, чтобы можно было выделить содержимое поля,
                     иначе ниже идёт присвоение значения и сброс выделения */
                    return false;
                }
            }

            // Вырезаем единицу измерения из значения поля
            var regexp = new RegExp(data.unit, "g");
            value = value.replace(regexp, "");

            if (value !== "" && value !== "-" && value[value.length - 1] !== ".") {
                var valueArr = value.split("."),
                    valueDec = valueArr[1] || 0;

                if (valueDec.length > data.decimals) {
                    value = valueArr[0] + "." + valueArr[1].slice(0, data.decimals);
                }

                value = parseFloat(value);

                if (e.target === $leftInput[0]) {
                    if (isNaN(value) || value < data.min) { value = data.min; }
                    if (value > data.max) { value = data.max }

                    if (this.options.type === "balance") {
                        var value2 = this.options.data.max - (Math.abs(this.options.data.min) + value);
                        this.option("values", {left : value, right : value2});
                    } else {
                        if (value > parseFloat($rightInput.val())) {
                            this.option("values", {left : value, right : value});
                        } else {
                            this.option("values", {left : value, right : parseFloat($rightInput.val())});
                        }
                    }
                } else {
                    if (isNaN(value) || value > data.max) { value = data.max; }
                    if (value < data.min) { value = data.min }

                    if (this.options.type === "balance") {
                        var value2 = this.options.data.max - (Math.abs(this.options.data.min) + value);
                        this.option("values", {left : value2, right : value});
                    } else {
                        if (value < parseFloat($leftInput.val())) {
                            this.option("values", {left : value, right : value});
                        } else {
                            this.option("values", {left : parseFloat($leftInput.val()), right : value});
                        }
                    }
                }
            } else {
                if (value[value.length - 1] === "." && data.decimals === 0) {
                    value = value.slice(0, -1);
                }
                if (e.target === $leftInput[0]) {
                    this.option("values", {left : value, right : parseFloat($rightInput.val())});
                } else {
                    this.option("values", {left : parseFloat($leftInput.val()), right : value});
                }
            }
        },
        _handleCheckboxClick: function (e) {
            if (e.currentTarget.checked) {
                this.option("checked", true);
            } else {
                this.option("checked", false);
            }
        },
        _setOption: function (key, val) {
            var $leftTrackBtn = this.option("leftTrackBtn"),
                $rightTrackBtn = this.option("rightTrackBtn"),
                $currentBtn = this.option("currentBtn"),
                $trackLine = this.option("trackLine"),
                $leftInput = this.option("leftInput"),
                $rightInput = this.option("rightInput"),
                data = this.option("data");

            if (key === "values") {
                $leftInput.val(val.left + data.unit);
                $rightInput.val(val.right + data.unit);
                $leftTrackBtn.css("left", (val.left - data.min) / data.total *  100 + "%");
                $rightTrackBtn.css("right", 100 - (val.right - data.min) / data.total *  100 + "%");
                $trackLine.css({
                    "left" : (val.left - data.min) / data.total *  100 + "%",
                    "right" : 100 - (val.right - data.min) / data.total *  100 + "%"
                });

                // Генерируем событие change вручную. Необходимо для взаимодействия с другими фреймворками
                // (например, с Knockout.js)
                var evt = document.createEvent("Event");
                evt.initEvent("change", false, true);
                $leftInput[0].dispatchEvent(evt);
                if (this.options.type !== "value") {
                    $rightInput[0].dispatchEvent(evt);
                }
            }

            if (key === "currentBtnPos") {
                // Вычисляем из процента число от общего числа и округляем до нужного количества знаков после запятой
                var inputVal = Math.ceil((data.total * val / 100 + data.min) * Math.pow(10, data.decimals)) / Math.pow(10, data.decimals) + data.unit;

                if ($currentBtn.is($leftTrackBtn)) {
                    $leftInput.val(inputVal);
                    $leftTrackBtn.css("left", val + "%");
                    $trackLine.css("left", val + "%");

                    // Если левая кнопка "упёрлась" в правую
                    if (val > 100 - parseFloat($rightTrackBtn[0].style.right)) {
                        $rightInput.val(inputVal);
                        $rightTrackBtn.css("right", 100 - val + "%");
                        $trackLine.css("right", 100 - val + "%");
                    }

                    if (this.options.type === "balance") {
                        $rightInput.val(this.options.data.max - (Math.abs(this.options.data.min) + parseFloat(inputVal)) + data.unit);
                    }
                } else {
                    $rightInput.val(inputVal);
                    $rightTrackBtn.css("right", 100 - val + "%");
                    $trackLine.css("right", 100 - val + "%");

                    // Если правая кнопка "упёрлась" в левую
                    if (val < parseFloat($leftTrackBtn[0].style.left)) {
                        $leftInput.val(inputVal);
                        $leftTrackBtn.css("left", val + "%");
                        $trackLine.css("left", val + "%");
                    }
                }

                // Генерируем событие change вручную. Необходимо для взаимодействия с другими фреймворками
                // (например, с Knockout.js)
                var evt = document.createEvent("Event");
                evt.initEvent("change", false, true);
                $leftInput[0].dispatchEvent(evt);
                if (this.options.type !== "value") {
                    $rightInput[0].dispatchEvent(evt);
                }
            }

            if (key === "checked") {
                if (val) {
                    this.options.type = "range";
                    this.element.removeClass(this.options.valueClass);
                    $rightTrackBtn.show();
                    $leftInput.closest(".input").after(this.options.removedInput);
                    $trackLine.show();
                } else {
                    this.options.type = "value";
                    this.element.addClass(this.options.valueClass);
                    $rightTrackBtn.hide();
                    this.options.removedInput = $rightInput.closest(".input").detach();
                    $trackLine.hide();
                }
            }

            return this._super(key, val);
        }
    });
});