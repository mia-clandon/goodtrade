/**
 * Работа с тегами (пока используется для виджета js/widgets/ui-category-new.js:3)
 * @author Артём Широких kowapssupport@gmail.com
 */
define('widgets/ui-input-tags', ['jquery', 'jquery-ui',], function ($) {
    return $.widget('ui.inputTags', {
        options: {
            tag_template: null,
            values: [],
        },
        _init: function () {
        },
        _create: function () {
            // шаблон вставляемого тега.
            this.options.tag_template = this.element.find('.ui-tag-template:first').clone().html();
            this._bindEvents();
        },
        _bindEvents: function () {
            let self_object = this;

            // удаление тега.
            this.element.on('click', '.ui-tags-item-close', function (e) {
                e.preventDefault();
                self_object.removeTag($(this).parents('span'));
                return false;
            })
        },
        /**
         * Удаление тега.
         * @param tag
         * @return {ui.inputTags}
         */
        removeTag: function (tag) {
            let tag_value = tag.find('input[type=hidden]').val();
            this.options.values.splice($.inArray(tag_value, this.options.values), 1);
            tag.remove();
            this.element.trigger('tagRemoved', {value: tag_value});
            return this;
        },
        /**
         * Удаление тега по значению.
         * @param value
         * @return {ui.inputTags}
         */
        removeTagByValue: function (value) {
            this.element.find('input[type=hidden][value=' + value + ']')
                .parents('span.ui-tags-item')
                .remove();
            this.options.values.splice($.inArray(value, this.options.values), 1);
            this.element.trigger('tagRemoved', {value: value});
            return this;
        },
        /**
         * Возвращает массив со значениями тегов.
         * @return {Array}
         */
        getValues: function () {
            return this.options.values;
        },
        /**
         * Получение шаблона тега.
         * @param object
         * @return {*|jQuery|HTMLElement}
         * @private
         */
        _getTagTemplate: function (object) {
            let $tag = $(this.options.tag_template);
            $tag.removeAttr('style');
            // label
            if (object.hasOwnProperty('label')) {
                $tag.find('span.ui-tags-item-label').text(object.label);
            }
            // class
            if (object.hasOwnProperty('icon_class')) {
                //noinspection JSUnresolvedletiable
                $tag.find('i.icon:first').addClass(object.icon_class);
            }
            // Если класс иконки пустой, то у тега убрать иконку вообще
            if (object.icon_class === "") {
                $tag.find('i.icon:first').remove();
            }
            // value
            if (object.hasOwnProperty('value')) {
                $tag.find('input[type=hidden]').val(object.value);
            }
            return $tag;
        },
        /**
         * Добавление тега.
         * @param object
         * пример приходящего объекта: {
     *    label: 'Название тега',
     *    icon_class: 'Класс иконки',
     *    value: 'Значение вставляемое в hidden инпут'
     * }
         */
        addTag: function (object) {
            let $tag = this._getTagTemplate(object);
            this.options.values.push(object.value);
            $tag.appendTo(this.element.find('.tags-container'));

            // Если в блоке с тегами есть ссылка, открывающая блок со сферами деятельности, то после добавления тега
            // её нужно помещать в конец блока
            this.element
                .find(".tags-container a.ui-category-button")
                .appendTo(this.element.find('.tags-container'));
            return this;
        }
    });
});