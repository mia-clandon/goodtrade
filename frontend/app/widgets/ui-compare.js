/**
 * Виджет для таблицы сравнения. Может делать фиксированными шапку, подвал и первый столбец
 * @author Селюцкий Викентий razrabotchik.www@gmail.com
 */
export default $.widget('ui.compare', {
    options : {
        url : {
            product : 'api/',
            search : 'api/'
        },
        table : '.comparison-table',
        thead : 'thead',
        tbody : 'tbody',
        tfoot : 'tfoot',

        addBtn : '.comparison-add-btn',
        searchBlock : '.comparison-search',

        isFixedThead : true,
        isFixedTfoot : true,
        isFixedColumn : true,

        haveCollapsibleRows : true,
        collapsibleRow : '.comparison-container__tech-specs',
        collapseToggle : '.comparison-table__tech-specs-title',

        contactsBlock : ".comparison-table__item-manufacturer-contacts",
        contactsToggle : ".comparison-table__item-manufacturer-contacts-link",
        contactsToggleTextOn : "Показать контакты",
        contactsToggleTextOff : "Скрыть контакты",
    },

    _create : function() {
        // Находим нужные элементы в общем блоке сравнения и присваиваем переменным
        var $root = this.element,
            $table = this.options.table = $root.find(this.options.table),
            $thead = $table.find(this.options.thead),
            $tbody = $table.find(this.options.tbody),
            $tfoot = $table.find(this.options.tfoot),
            $addBtn = $root.find(this.options.addBtn),
            $searchBlock = $root.find(this.options.searchBlock);

        // Создаём отдельные блоки для таблицы и для кнопки добавления с блоком поиска
        var $tableContainer = $("<div>").addClass("comparison-container__table-container"),
            $addContainer = $("<div>").addClass("comparison-container__add-container");

        $root.prepend($tableContainer);
        $root.children().first().after($addContainer);
        $addContainer.append($addBtn, $searchBlock);

        // Фиксируем таблицу в целом
        var $fixedTbodyBlockDiv = $("<div>").addClass("comparison-container__fixed-tbody"),
            $fixedTbodyBlockTable = $table;

        $fixedTbodyBlockDiv.append($fixedTbodyBlockTable);
        $tableContainer.append($fixedTbodyBlockDiv);

        // Если в настройках задано, чтобы заголовок таблицы был фиксирован, то отделяем его
        if (this.options.isFixedThead) {
            var $fixedTheadBlockDiv = $("<div>").addClass("comparison-container__fixed-thead"),
                $fixedTheadBlockTable = $("<table>").addClass("comparison-table");

            $fixedTheadBlockDiv.append($fixedTheadBlockTable);
            $fixedTheadBlockTable.append($thead);
            $fixedTbodyBlockDiv.before($fixedTheadBlockDiv);
        }

        // Если в настройках задано, чтобы подвал таблицы был фиксирован, то отделяем его
        if (this.options.isFixedTfoot) {
            var $fixedTfootBlockDiv = $("<div>").addClass("comparison-container__fixed-tfoot"),
                $fixedTfootBlockTable = $("<table>").addClass("comparison-table");

            $fixedTfootBlockTable.append($tfoot);
            $fixedTfootBlockDiv.append($fixedTfootBlockTable);
            $fixedTbodyBlockDiv.after($fixedTfootBlockDiv);
        }

        // Если в настройках задано, чтобы первый столбец таблицы был фиксирован, то отделяем его
        if (this.options.isFixedColumn) {
            var $fixedColumn = $("<div>").addClass("comparison-container__fixed-column"),
                $scrollableColumn = $("<div>").addClass("comparison-container__scrollable-column"),
                fixedColumnThead = '<div class="comparison-container__fixed-column-thead"><table class="comparison-table"><thead></thead></table></div>',
                fixedColumnTbody = '<div class="comparison-container__fixed-column-tbody"><table class="comparison-table"><tbody></tbody></table></div>',
                fixedColumnTfoot = '<div class="comparison-container__fixed-column-tfoot"><table class="comparison-table"><tfoot></tfoot></table></div>';

            $fixedColumn.html(fixedColumnThead + fixedColumnTbody + fixedColumnTfoot);

            var $fixedColumnTheadDiv = $fixedColumn.children("div").eq(0),
                $fixedColumnTbodyDiv = $fixedColumn.children("div").eq(1),
                $fixedColumnTfootDiv = $fixedColumn.children("div").eq(2);

            /**
             * Извлекает первые ячейки в рядах одного элемента и помещает их в другой пустой элемент
             * @param $elFrom - jQuery object
             * @param $elTo - jQuery object
             */
            function extractFirstCells($elFrom, $elTo) {
                $elFrom.find("tr").each(function (index, element) {
                    var $newTr = $(element).clone().html(""),
                        $firstTd = $(element).children().eq(0);

                    $(element).height($(element).height());
                    $newTr.height($(element).height());

                    $newTr.append($firstTd);
                    $elTo.append($newTr);
                });
            }

            //Перемещаем ячейки в пустые блоки
            extractFirstCells($fixedTheadBlockTable, $fixedColumn.find("thead"));
            extractFirstCells($fixedTbodyBlockTable, $fixedColumn.find("tbody"));
            extractFirstCells($fixedTfootBlockTable, $fixedColumn.find("tfoot"));

            $root.prepend($fixedColumn);

            $scrollableColumn.append($tableContainer, $addContainer);

            $root.append($scrollableColumn);
        }

        // Если в настройках задано, что в таблице есть сворачиваемые ряды
        if (this.options.haveCollapsibleRows) {
            this.handleCollapse();
        }

        /**
         * Стилизация
         */

        // Задаём высоту таблиц.
        this.setTbodyFixedHeight($fixedTheadBlockDiv, $fixedTbodyBlockDiv, $fixedTfootBlockDiv);
        this.setTbodyFixedHeight($fixedColumnTheadDiv, $fixedColumnTbodyDiv, $fixedColumnTfootDiv);

        $fixedTbodyBlockDiv.css("overflow", "overlay");
        $fixedColumnTbodyDiv.css({"overflow" : "hidden"});

        // Приводим в порядок отображение блоков относительно друг друга
        $root.css({
            "overflow" : "overlay",
            "white-space" : "nowrap",
            "font-size" : "0"
        });

        $tableContainer.add($addContainer).css({
            "display" : "inline-block",
            "vertical-align" : "top",
            "white-space" : "normal"
        });

        if(this.options.isFixedColumn) {
            $root.css({
                "position" : "relative",
                "overflow" : "hidden"
            });
            $fixedColumn.css({
                "position" : "absolute",
                //"background-color" : "white",
                "z-index" : "3",
                "white-space" : "normal"
            });
            $scrollableColumn.css({
                "overflow-x" : "overlay",
                "margin-left" : $fixedColumn.width(),
                "white-space" : "nowrap"
            });
        }

        // Явно прописываем ширину каждой таблице, чтобы она не уменьшалась в размерах при уменьшении ширины
        // экрана либо при открытии боковой колонки с меню.
        $root.find("table").each(function (index, element) {
            $(element).width($(element).width());
        });

        $("body").css("overflow", "auto");

        /**
         * Обработчики событий
         */

        // Красивая прокрутка с помощью PerfectScrollbar
        $fixedTbodyBlockDiv.css({
            "overflow" : "",
            "position" : "relative"
        }).perfectScrollbar();

        if(this.options.isFixedColumn) {
            // Красивая прокрутка с помощью PerfectScrollbar
            $scrollableColumn.css({
                "overflow" : "",
                "position" : "relative"
            }).perfectScrollbar();

            // Корявое решение появления горизонтальной прокрутки у прокручиваемого блока при открытии бокового меню
            $("body").find("button.menu").on("click", function () {
                $scrollableColumn.animate({scrollLeft: 0}, 700, function () {
                    $scrollableColumn.perfectScrollbar("update");
                });
            });

            // Синхронизация прокрутки между основным содержанием таблицы и прикреплённым столбцом
            $(".comparison-container__fixed-tbody").scroll(function () {
                var scrollPos = $(this).scrollTop();
                $(".comparison-container__fixed-column-tbody").scrollTop(scrollPos);
            });
        }
    },

    _init : function() {
        var $root = this.element;

        this._on($root, {'click' : this._handleClick});
        this._on(this.options.addBtn, {'click' : this._handleClick});
    },

    _handleClick : function(e) {
        var $el = $(e.target),
            action = $el.data('action');

        // Показ блока поиска для добавления к сравнению
        if (action === 'show') {
            $(this.options.addBtn).addClass("is-hidden");
            $(this.options.searchBlock).removeClass("is-hidden");

            // Если первый столбец таблицы фиксирован, то осуществляем прокрутку вправо
            if (this.options.isFixedColumn) {
                var $scrollableColumn = $el.closest(".comparison-container__scrollable-column");

                $scrollableColumn.animate({scrollLeft : 1000000}, 0);
                $scrollableColumn.perfectScrollbar("update");
            }

            e.preventDefault();
        }

        if (action === 'add-col') {
            this.add($el.data('id'));
            e.preventDefault();
        }

        if (action === "del-col") {
            this.del($el);
            e.preventDefault();
        }

        // Показ и скрытие блока контактов
        if ($el.is(this.options.contactsToggle)) {
            var $root = $(this.element),
                $tableThead = $root.find(".comparison-container__fixed-thead"),
                $tableTbody = $root.find(".comparison-container__fixed-tbody"),
                $tableTfoot = $root.find(".comparison-container__fixed-tfoot");

            if ($el.text() === this.options.contactsToggleTextOn) {
                $el.parent().children(this.options.contactsBlock).addClass("is-visible");
                $el.text(this.options.contactsToggleTextOff);
            } else if ($el.text() === this.options.contactsToggleTextOff) {
                $el.parent().children(this.options.contactsBlock).removeClass("is-visible");
                $el.text(this.options.contactsToggleTextOn);
            }

            // Если закреплён первый столбец, то синхронизировать высоту рядов
            if (this.options.isFixedColumn) {
                var $fixedColumn = $root.find(".comparison-container__fixed-column"),
                    $scrollableColumn = $root.find(".comparison-container__scrollable-column"),
                    $fixedColumnThead = $fixedColumn.find(".comparison-container__fixed-column-thead"),
                    $fixedColumnTbody = $fixedColumn.find(".comparison-container__fixed-column-tbody"),
                    $fixedColumnTfoot = $fixedColumn.find(".comparison-container__fixed-column-tfoot"),
                    idx = $scrollableColumn.find("tr").index($el.closest("tr")),
                    $parentTr = $el.closest("tr");

                // Обновляем значение высоты внутри атрибута style у элемента
                $parentTr
                    .height("")
                    .height($parentTr.height());

                var height = $parentTr.height();

                $fixedColumn.find("tr").eq(idx).height(height);

                // Задаём высоту таблицы фиксированного столбца
                this.setTbodyFixedHeight($fixedColumnThead, $fixedColumnTbody, $fixedColumnTfoot);
            }

            // Задаём высоту основной таблицы
            this.setTbodyFixedHeight($tableThead, $tableTbody, $tableTfoot);

            // Обновление прокрутки содержимого основной таблицы
            //$tableTbody.scrollTop($tableTbody.height());
            $tableTbody.perfectScrollbar("update");

            e.preventDefault();
        }
    },

    // Добавление столбца таблицы
    add : function(id) {
        var self_object = this,
            $root = $(this.element),
            url = this.option('url.product'),
            $tableContainer = $root.find(".comparison-container__table-container"),
            $tableThead = $root.find(".comparison-container__fixed-thead"),
            $tableTbody = $root.find(".comparison-container__fixed-tbody"),
            $tableTfoot = $root.find(".comparison-container__fixed-tfoot");

        if (this.options.isFixedColumn) {
            var $fixedColumn = $root.find(".comparison-container__fixed-column"),
                $scrollableColumn = $root.find(".comparison-container__scrollable-column"),
                $fixedColumnThead = $fixedColumn.find(".comparison-container__fixed-column-thead"),
                $fixedColumnTbody = $fixedColumn.find(".comparison-container__fixed-column-tbody"),
                $fixedColumnTfoot = $fixedColumn.find(".comparison-container__fixed-column-tfoot"),
                tableWidth = $tableContainer.find("table").outerWidth(),
                tdWidth = $tableContainer.find("td").outerWidth();
        }

        $.ajax({url: url+id+'.json', type: 'POST', dataType: 'json', data: {
            query: id
        }})
            .done(function(data){
                var tdCount = $tableContainer.find("tr").eq(0).children("td").length,
                    prevIndex = 0;

                for (var key in data) {
                    var selector = "item-" + key,
                        $tr = $tableContainer.find("tr[data-key=" + selector + "]"),
                        $td = $("<td>");

                    // Если в таблице есть строка с полученным ключом
                    if ($tr.length === 1) {
                        prevIndex = $tableContainer.find("tr").index($tr);

                        switch (key) {
                            case "title" :
                                $td.html('<div class="comparison-table__item-title"><a href="' + data[key].url + '">' + data[key].text + '</a></div>');
                                break;

                            case "photos" :
                            /**
                             * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
                             * @param  iNumber Integer Число на основе которого нужно сформировать окончание
                             * @param  aEndings Array Массив слов или окончаний для чисел (1, 2-4, 5),
                             *         например ['яблоко', 'яблока', 'яблок']
                             * @return String
                             */
                            function getNumEnding(iNumber, aEndings)
                            {
                                var sEnding, i;
                                iNumber = iNumber % 100;
                                if (iNumber>=11 && iNumber<=19) {
                                    sEnding=aEndings[2];
                                }
                                else {
                                    i = iNumber % 10;
                                    switch (i)
                                    {
                                        case (1): sEnding = aEndings[0]; break;
                                        case (2):
                                        case (3):
                                        case (4): sEnding = aEndings[1]; break;
                                        default: sEnding = aEndings[2];
                                    }
                                }
                                return sEnding;
                            }

                                var photoBg = data[key].bg,
                                    photoCount = data[key].count;

                                $td.addClass("comparison-table__item-photos");

                                if (photoCount > 0) {
                                    var photoText = getNumEnding(photoCount, ["фотография", "фотографии", "фотографий"]);

                                    $td.html('<div class="comparison-table__item-photos-image"><img src="' + photoBg + '"></div><div class="comparison-table__item-photos-text">' + photoCount + ' ' + photoText + '</div>');
                                } else {
                                    $td.html('<div class="comparison-table__item-photos-image"></div><div class="comparison-table__item-photos-text">Нет фотографий</div>');
                                }

                                break;

                            case "price" :
                                var perOnePrice = data[key]["per-one"],
                                    totalPrice = data[key].total;

                                if (perOnePrice > 0) {
                                    // Добавляем пробелов через каждые три цифры
                                    $td.html('<div class="price">' + perOnePrice.toString().replace(/(\d)(?=(\d{3})+(\D|$))/g, "$1 ") + ' тг.</div>');

                                    if (totalPrice > 0) {
                                        // Добавляем пробелов через каждые три цифры
                                        $td.append('<div class="total-price">' + totalPrice.toString().replace(/(\d)(?=(\d{3})+(\D|$))/g, "$1 ") + ' тг.</div>');
                                    }

                                    if (data[key]["is-base"] === 1) {
                                        $td.append('<div class="label">Базовая цена</div>');
                                    }
                                } else {
                                    $td.html('<div class="price no-price">Цена по уточнению</div>');
                                }

                                break;

                            case "modifications" :
                                // TODO: Доработать показ модификаций. Нужно уточнить у backend-программиста в каком
                                // формате хранятся модификации для товаров. Должно что-то вроде массива приходить.

                                if (data[key] === "Нет модификаций") {
                                    $td.text(data[key]);
                                } else {
                                    $td.html('<div class="popup-dropdown"><a href="#" class="popup-toggle modifications-popup js-keeper">Все модификации</a></div>');
                                }

                                break;

                            case "commerce-validity" :
                                $td.text(data[key] + " дней");
                                break;

                            case "commerce-batch-size" :
                                $td.text(data[key].quantity + " " + data[key].unit);
                                break;

                            case "commerce-stock-availability" :
                                $td.text(data[key]);
                                break;

                            case "commerce-terms-of-payment" :
                                $td.html(data[key]["pre-payment"] + '% - предоплата<br>' + data[key]["post-payment"] + '% - постоплата');
                                break;

                            case "delivery-conditions" :
                            case "power" :
                                $td.text(data[key]);
                                break;

                            case "delivery-address" :
                                $td.html(data[key].address + '<br><span>Около ' + data[key].distance + ' км</span>');
                                break;

                            case "manufacturer-info" :
                                $td.html('<div class="comparison-table__item-manufacturer"><a href="' + data[key].url + '">' + data[key].name + '</a></div>');

                                if (!!$.trim(data[key].contacts)) {
                                    $td.append('<div class="comparison-table__item-manufacturer-contacts"></div><a href="#" class="comparison-table__item-manufacturer-contacts-link">Показать контакты</a>');
                                } else {
                                    $td.append('<span class="comparison-table__item-manufacturer-contacts-link">Контакты скрыты</span>');
                                }

                                break;

                            default:
                                $td.text(data[key].specValue);
                        }

                        $tr.append($td);
                    } else { // Если в таблице нет строки с полученным ключом

                        // Если ключ является технической характеристикой и в таблице до этого не было технических характеристик
                        if (key.slice(0, 4) === "tech" && $root.find(self_object.options.collapseToggle).length === 0) {
                            var $trToggle = $("<tr>");

                            $trToggle.html('<th><div class="' + self_object.options.collapseToggle.slice(1) + '">Технические характеристики</div></th>');

                            if (self_object.options.isFixedColumn) {
                                var $trToggle2 = $("<tr>");

                                for (var i = 0; i === tdCount; i++) {
                                    $trToggle2.append($("<td>"));
                                }

                                $fixedColumn.find("tr").eq(prevIndex).after($trToggle);
                                $tableContainer.find("tr").eq(prevIndex).after($trToggle2);
                            } else {
                                for (var i = 0; i === tdCount; i++) {
                                    $trToggle.append($("<td>"));
                                }

                                $tableContainer.find("tr").eq(prevIndex).after($trToggle);
                            }

                            prevIndex++;
                        }

                        $tr = $("<tr>").attr("data-key", "item-" + key);

                        if (key.slice(0,4) === "tech") {
                            $tr.addClass("comparison-container__tech-specs");
                        }

                        for (var i = 0; i < tdCount; i++) {
                            $tr.append($("<td>&mdash;</td>"));
                        }

                        $td.text(data[key].specValue);
                        $tr.append($td);

                        $tableContainer.find("tr").eq(prevIndex).after($tr);

                        if (self_object.options.isFixedColumn) {
                            $fixedColumn.find("tr").eq(prevIndex).after($('<tr data-key="'+key+'" class="comparison-container__tech-specs"><th>'+data[key].specTitle+'</th></tr>'));
                        } else {
                            $tr.prepend($('<tr data-key="'+key+'" class="comparison-container__tech-specs"><th>'+data[key].specTitle+'</th></tr>'));
                        }

                        prevIndex++;
                    }
                    // Если включена настройка сворачивания рядов
                    if (self_object.options.haveCollapsibleRows) {
                        self_object.handleCollapse();
                    }
                }

                // Добавление ячейки с кнопкой в конце таблицы
                var $lastTr = $tableContainer.find("tr:last"),
                    $lastTd = $("<td>");

                $lastTd.html('<div class="popup-dropdown"><div data-vertical-align="bottom" class="btn btn-blue btn-block btn-outline btn-comparison btn-comparison_dropdown popup-toggle send-contacts-popup">Выслать контакты</div></div>');
                $lastTr.append($lastTd);

                // Добавление пустых ячеек для незаполненных рядов
                $tableContainer.find("tr").each(function (index, element) {
                    if ($(element).children("td").length === tdCount) {
                        $(element).append("<td>&mdash;</td>");
                    }
                });

                // Увеличиваем ширину таблицы на одну ячейку
                $tableContainer.find("table").width(tableWidth + tdWidth);
                $scrollableColumn.perfectScrollbar("update");

                // После успешного добавления столбца скрываем блок поиска и показываем кнопку добавления
                $(self_object.options.addBtn).removeClass("is-hidden");
                $(self_object.options.searchBlock).addClass("is-hidden");

                // Если первый столбец таблицы фиксирован
                if (self_object.options.isFixedColumn) {
                    // Осуществляем прокрутку вправо
                    $scrollableColumn.animate({scrollLeft : 1000000}, 0);
                    $scrollableColumn.perfectScrollbar("update");

                    // Синхронизируем высоты рядов фиксированного и прокручиваемого блока
                    $tableContainer.find("tr").each(function (index, element) {
                        var $scrollTd = $(element),
                            $fixedTd = $fixedColumn.find("tr").eq(index);

                        if ($scrollTd.height() > $fixedTd.height()) {
                            $scrollTd.height($scrollTd.height());
                            $fixedTd.height($scrollTd.height());
                        } else {
                            $fixedTd.height($fixedTd.height());
                            $scrollTd.height($fixedTd.height());
                        }
                    });
                }

                // Корректировка высоты шапки, тела и подвала таблиц, чтобы не появлялась прокрутка у body
                self_object.setTbodyFixedHeight($tableThead, $tableTbody, $tableTfoot);
                self_object.setTbodyFixedHeight($fixedColumnThead, $fixedColumnTbody, $fixedColumnTfoot);

                // Обновление прокрутки содержимого основной таблицы
                $tableTbody.scrollTop(0);
                $tableTbody.perfectScrollbar("update");
            });

    },

    // Удаление столбца таблицы
    del : function($element) {
        var tdx = $element.closest("td").index(),
            tdWidth = $element.closest("td").outerWidth();

        if (this.options.isFixedColumn) {
            var $scrollableColumn = $element.closest(".comparison-container__scrollable-column");
        }

        $("body").find("table").each(function (index, elem) {
            var haveTds = false;

            $(elem).find("tr").each(function (index, el) {
                if ($(el).children("td").is("td")) {
                    haveTds = true;
                }

                $(el).children("td").eq(tdx).remove();
            });

            if (haveTds) {
                $(elem).width("-=" + tdWidth);
            }
        });

        // Если первый столбец таблицы фиксирован, то обновляем прокрутку
        if (this.options.isFixedColumn) {
            $scrollableColumn.perfectScrollbar("update");
        }
    },

    /**
     * Метод задаёт максимальную высоту таблицы.
     * @param $theadDivEl - шапка таблицы
     * @param $tbodyDivEl - тело таблицы
     * @param $tfootDivEl - подвал таблицы
     */
    setTbodyFixedHeight : function ($theadDivEl, $tbodyDivEl, $tfootDivEl) {
        // Отнимаем высоту верхней плашки пользователя сайта
        var fixedHeight = $(window).height() - 60;

        if (this.options.isFixedThead) {
            fixedHeight -= $theadDivEl.height();
        }

        if (this.options.isFixedTfoot) {
            fixedHeight -= $tfootDivEl.height();
        }

        $tbodyDivEl.css({
            "min-height": fixedHeight + "px",
            "max-height": fixedHeight + "px"
        });
    },

    handleCollapse : function () {
        var self_object = this,
            $root = this.element;

        if ($root.find(".comparison-container__collapsible-container").length !== 0) {
            return false;
        }

        // В каждой таблице находим сворачиваемые ряды и помещаем в отдельный блок с таблицей
        $root.find("table").has(this.options.collapsibleRow).each(function (index, element) {
            var $collapsibleBlock = $("<div>").addClass("comparison-container__collapsible-container"),
                $collapsibleRows = $(element).find(self_object.options.collapsibleRow),
                $mainBlock = $collapsibleRows.closest("div"),
                $parentBlock = $("<div>"),
                $parentTable = $collapsibleRows.closest("table"),
                $nextBlock = $("<div>"),
                $nextRows = $collapsibleRows.last().next("tr"),

                $collapsibleTable = $parentTable.clone(),
                $nextTable = $parentTable.clone();

            $parentBlock.append($parentTable);

            $collapsibleBlock.append($collapsibleTable)
                .find("table").html("<tbody></tbody>")
                .find("tbody").append($collapsibleRows);

            $nextBlock.append($nextTable)
                .find("table").html("<tbody></tbody>")
                .find("tbody").append($nextRows);

            $mainBlock.append($parentBlock, $collapsibleBlock, $nextBlock);
        });

        var $collapseToggle = $root.find(this.options.collapseToggle),
            $collapseBlock = $root.find(this.options.collapsibleRow).closest("div"),
            $fixedTbodyBlockDiv = $root.find(".comparison-container__fixed-tbody");

        // Щелчок по переключателю
        $collapseToggle.on("click", function (e) {
            $(e.currentTarget).toggleClass("comparison-table__tech-specs-title_closed");

            // Плавное сворачивание блоков
            $collapseBlock.slideToggle(300, function () {
                $fixedTbodyBlockDiv.perfectScrollbar('update');
            });

            // Плавная прокрутка содержимого основной таблицы
            $fixedTbodyBlockDiv.animate({scrollTop : 0}, 300, function () {
                $fixedTbodyBlockDiv.perfectScrollbar('update');
            });

            e.preventDefault();
        });
    }
});