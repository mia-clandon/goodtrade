import './ui-input-autocomplete';
import './ui-tags';
import './ui-multiply';

/**
 * Виджет характеристик
 * @example Пример использования:
 * // $('.input-hash').inputHash();
 * @exports widgets/ui-input-hash
 * TODO: названия характеристик повторяются - запретить повторы.
 * @author Артём Широких kowapssupport@gmail.com
 */
export default $.widget('ui.inputHash', {
    options: {
        search: {
            url: '/api/vocabulary/find'
        },
        // [ {vocabulary_id: n, text: 'Процессор', terms: [{term_id: 0, text: 'core i7'}]} ]
        data: {}
    },
    _create: function () {
        let self_object = this;

        // возможности для клонирования.
        this.element.multiply();

        // удалили строку.
        this.element.on('del', function () {
            self_object.updateData();
        });

        // добавился еще один элемент.
        this.element.on('add', function (event, data) {
            //noinspection JSUnresolvedFunction
            self_object.bindSearchEvents(data.new_item, true);
            //noinspection JSUnresolvedFunction
            self_object.bindTagsPlugin(data.new_item.find('input[name^=tech-specs]'), true);
        });

        this.bindSearchEvents(this.element);
        this.bindTagsPlugin(this.element.find('input[name^=tech-specs]'));
    },
    /**
     * Метод подгружает список возможных значений характеристик.
     * Метод выполняется как только пользователь выберет из списка одну из характеристик.
     */
    loadVocabularyTerms: function (data, callback) {
        if (data == undefined || !data.hasOwnProperty('value')) {
            return false;
        }
        let value = parseInt(data.value),
            th = this;
        $.ajax({url: '/api/vocabulary/terms', type: 'POST', data: {
            vocabulary_id: value
        }})
        .done(function(data) {
            callback(data);
        });
    },
    /**
     * Метод инициализирует / добавляет тег.
     */
    initTags: function (data, $term_input) {
        let th = this;
        if (data.error == 0 && data.data.length > 0) {
            // инициализация значений характеристик.
            $term_input.search({
                live_search_enabled: false,
                local_data: data.data,
                auto_open: true
            });
        }
    },
    /**
     * Инициализация плагина тегов и поиска к инпуту для значений характеристики.
     * @param element
     * @param is_clone
     */
    bindTagsPlugin: function (element, is_clone = false) {
        let $terms_tags = this.getTagsElementByTermElement(element),
            th = this;
        if (is_clone) {
            if ($terms_tags.data("tags")) {
                $terms_tags.tags("destroy");
            }
        }
        $terms_tags.tags();
        $terms_tags.on('removeTag', function () {
            // обновление данных виджета.
            //noinspection JSUnresolvedFunction
            th.updateData();
        });
    },
    /**
     * Метод ищет элемент для тегов по инпуту значений характеристик.
     * @param element
     */
    getTagsElementByTermElement(element) {
        return element.parents('div.input-tech-specs:first').find('.ui-tags');
    },
    /**
     * Инициализирует инпуты для поиска по названию характеристики.
     * @param element
     * @param is_clone
     */
    bindSearchEvents: function (element, is_clone = false) {
        let search_input = element.find('input[name^=specification-name]:last'),
            terms = element.find('input[name^=tech-specs]'),
            th = this
        ;

        if (is_clone) {
            // возможно элемент склонировался с чужим блоком подсказок - очищаю.
            let menu = search_input.parents('div').find('.ui-menu:last');
            if (menu.length) {
                menu.remove();
            }
            // возможно элемент склонировался с тегами - очищаю.
            //noinspection JSUnresolvedFunction
            let $tags_element = th.getTagsElementByTermElement(terms);
            if ($tags_element.length) {
                $tags_element.find('.tags-container').html('');
            }
            if (search_input.data("search")) {
                search_input.search("destroy");
            }
        }

        search_input.search(this.options.search);

        // изменение названия характеристики.
        search_input.bind("input propertychange", function () {
            // проверка на изменение только свойства value;
            if (window.event && event.type == "propertychange" && event.propertyName != "value") {
                return false;
            }
            // нужно подчищать подсказки значений характеристик.
            try {
                terms.search('destroy');
            }
            catch (e) {}
            // обновление данных виджета.
            //noinspection JSUnresolvedFunction
            th.updateData();
        });

        // нажали на enter - добавляю тег.
        terms.on('keypress', function (e) {
            if(e.which == 13) {
                // enter pressed;
                //noinspection JSUnresolvedFunction
                let $tags = th.getTagsElementByTermElement($(this));
                if (!$tags.data("tags")) {
                    $tags.tags();
                }
                $tags.tags("addTag", {value: '0', label: $(this).val()});
                $(this).val('');
                // обновление данных виджета.
                //noinspection JSUnresolvedFunction
                th.updateData();
                return false;
            }
        });

        // выбрали техническую характеристику.
        search_input.on('elementSelected', function (e, data) {
            //noinspection JSUnresolvedFunction
            let $term_input = $(this).parents('.input-group').find('input[name^=tech-specs]');

            try {
                $term_input.search('destroy');
            }
            catch (e) {}

            //todo: думаю нужно удалять теги значений если выбрали характеристику.

            th.loadVocabularyTerms(data, function (selected_term) {
                //noinspection JSUnresolvedFunction
                th.initTags(selected_term, $term_input);
            });
            // обновление данных виджета.
            //noinspection JSUnresolvedFunction
            th.updateData();
        });

        // добавление тега при нажатии на подсказку.
        terms.on('elementSelected', function (e, data) {
            //noinspection JSUnresolvedFunction
            let $tags_element = th.getTagsElementByTermElement($(this));
            $tags_element.tags("addTag", {value: data.value, label: data.text});
            $(this).val('');
            // обновление данных виджета.
            //noinspection JSUnresolvedFunction
            th.updateData();
        });
    },
    /**
     * Метод для сбора имеющихся данных.
     */
    getData: function () {
        let data = [];
        this.element.find('li.row-item').each(function() {
            let vocabulary = $(this).find('input[name^=specification-name]'),
                 id = parseInt(vocabulary.attr('data-value')),
                 text = vocabulary.val();
            if (text.length > 0) {
                // значения характеристик.
                let terms = [];
                $(this).find('.tags-container span input[type=hidden]').each(function () {
                    let term_val = $(this).val();
                    if (term_val !== undefined) {
                        term_val = JSON.parse(term_val);
                        let term_id = term_val.value,
                            term_text = term_val.name;
                        terms.push({
                            term_id: id && term_id ? term_id : 0,
                            text: term_text
                        });
                    }
                });
                if (terms.length) {
                    data.push({
                        vocabulary_id: id ? id : 0,
                        text: text,
                        terms: terms
                    });
                }
            }
        });
        return data;
    },
    /**
     * Обновление данных в hidden input;
     */
    updateData: function () {
        let data = this.getData();
        data = JSON.stringify(data);
        this.element.parents('.form-control:first').find('input.specification-data-input').val(
            encodeURIComponent(data)
        );
    },
});