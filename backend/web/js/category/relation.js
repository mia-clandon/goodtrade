/**
 * Работа с связями категории.
 */
class Relation {

    constructor() {
        this.body = $('body');
    }

    /**
     * Создаёт связь категории.
     * @param category_id
     * @param related_category_id
     * @param callback
     */
    createRelation(category_id, related_category_id, callback) {
        $.ajax({
            data: {category_id: category_id, related_category_id: related_category_id},
            type: 'POST',
            url: '/category/do-relation'
        })
        .success(function(data) {
            if (data.message === 'success') {
                callback(data);
            }
        })
        .error(function() {
            alert('Произошла ошибка, попробуйте позже.');
        });
    }

    /**
     * Создаёт дубликат.
     * @param category_id
     * @param parent_category_id
     * @param callback
     */
    createDuplicate(category_id, parent_category_id, callback) {
        $.ajax({
            data: {category_id: category_id, parent_category_id: parent_category_id},
            type: 'POST',
            url: '/category/do-duplicate'
        })
        .success(function(data) {
            if (data.message === 'success') {
                callback(data);
            }
        })
        .error(function() {
            alert('Произошла ошибка, попробуйте позже.');
        });
    }

    /**
     * Создаёт новую подкатегорию.
     * @param category_title
     * @param parent_category_id
     * @param callback
     */
    createNewCategory(category_title, parent_category_id, callback) {
        $.ajax({
            data: {category_title: category_title, parent_category_id: parent_category_id},
            type: 'POST',
            url: '/category/do-new-category'
        })
        .success(function(data) {
            if (data.message === 'success') {
                callback(data);
            }
        })
        .error(function() {
            alert('Произошла ошибка, попробуйте позже.');
        });
    }

    /**
     * Удаление связи категорий.
     * @param category_id
     * @param related_category_id
     * @param callback
     */
    removeRelation(category_id, related_category_id, callback) {
        $.ajax({
            data: {category_id: category_id, related_category_id: related_category_id},
            type: 'POST',
            url: '/category/remove-relation'
        })
        .success(function(data) {
            if (data.message === 'success') {
                callback(data);
            }
        })
        .error(function() {
            $('.related_category').removeClass('removed');
            alert('Произошла ошибка, попробуйте позже.');
        });
    }

    /**
     * Получение списка категорий.
     * @param parent_id
     */
    getRelationCategoryList(parent_id) {
        $.ajax({
            data: {parent_id: parent_id},
            type: 'POST',
            url: '/category/get-relation-category-list'
        })
        .success(function(data) {
            $('.relation .category-content').html(data);
        })
        .error(function() {
            alert('Произошла ошибка, попробуйте позже.');
        })
        .done(function() {
            if(parent_id !== 0) {
                $('.duplicate-here-button').show();
                $('.create-here-button').show();
            }
            $('.duplicate-button').hide();
            $('.duplicate-in-button').hide();
            $('.create-new-category').hide();
        });
    }

    init() {
        let th = this;

        // Подгрузка сфер деятельностей.
        this.body.on('click', '.duplicate-start', function () {
            $(this).hide();
            $('.close-button').show();
            th.getRelationCategoryList(0);
            return false;
        });

        // Подгрузка категорий сферы деятельности.
        this.body.on('click', '.relation .activity-block', function () {
            let category_id = $(this).data('category-id');
            th.getRelationCategoryList(category_id);
            return false;
        });

        // Назад к родительской категории.
        this.body.on('click', '.relation .parent_category', function () {
            let category_id = $(this).data('parent-category-id');
            th.getRelationCategoryList(category_id);
            return false;
        });

        // Подгрузка категорий по parent-id.
        this.body.on('click', '.relation .categories-list__category-item_parent', function () {
            let category_id = $(this).data('category-id');
            th.getRelationCategoryList(category_id);
            return false;
        });

        // Подгрузка категорий по parent-id.
        this.body.on('click', '.relation .categories-list__category-item', function () {
            $('.relation .categories-list__category-item').removeClass('selected');
            $(this).addClass('selected');
            $('.duplicate-button').show();
            $('.duplicate-in-button').show();
            $('.create-new-category').show();
            $('.duplicate-here-button').hide();
            $('.create-here-button').hide();
            return false;
        });

        // Закрытие каталога.
        this.body.on('click', '.close-button', function () {
            location.reload();
            return false;
        });

        // Удаление связи категорий.
        this.body.on('click', '.delete_relation', function () {
            let related_category_id = $(this).parents('.related_category:first').data('related-category-id');
            let category_id = $(this).closest('.relation').data('category-id');

            $(this).parent().addClass('removed');

            th.removeRelation(category_id, related_category_id, function() {
                $('.related_category.removed').remove();
            });
            return false;
        });

        // Связывание категорий.
        this.body.on('click', '.duplicate-here-button', function () {
            let category_id = $('.relation').data('category-id');
            let parent_category_id = $('.relation .parent_category').data('category-id');

            th.createDuplicate(category_id, parent_category_id, function() {
                th.getRelationCategoryList(parent_category_id);
            });
            return false;
        });

        // Создать категорий.
        this.body.on('click', '.create-here-button', function () {
            let category_title = prompt("Введите название категории");
            if (category_title != null) {
                let parent_category_id = $('.relation .parent_category').data('category-id');

                th.createNewCategory(category_title, parent_category_id, function() {
                    th.getRelationCategoryList(parent_category_id);
                });
            }

            return false;
        });

        // Создатние подкотегории
        this.body.on('click', '.create-new-category', function () {
            let category_title = prompt("Введите название категории");
            if (category_title != null) {
                let parent_category_id = $('.relation li.selected').data('category-id');

                th.createNewCategory(category_title, parent_category_id, function() {
                    th.getRelationCategoryList(parent_category_id);
                });
            }

            return false;
        });

        // Связывание категорий.
        this.body.on('click', '.duplicate-button', function () {
            let category_id = $('.relation').data('category-id');
            let related_category_id = $('.relation li.selected').data('category-id');

            th.createRelation(category_id, related_category_id, function() {
                location.reload();
            });
            return false;
        });

        // Создать копию внутри.
        this.body.on('click', '.duplicate-in-button', function () {
            let category_id = $('.relation').data('category-id');
            let parent_category_id = $('.relation li.selected').data('category-id');

            th.createDuplicate(category_id, parent_category_id, function() {
                th.getRelationCategoryList(parent_category_id);
            });
            return false;
        });

        // поиск
        let timeout = null;
        this.body.on('input', 'input.search', function () {
            let self = $(this);
            let search_text = self.val();
            clearTimeout(timeout);

            timeout = setTimeout(function () {
                if(self.val() !== '') {
                    $( '.relation li, .relation .activity-block' ).each(function() {
                        let found = $(this).text().toLowerCase().search(search_text.toLowerCase());
                        if(found === -1) {
                            $(this).toggle();
                        }
                    });
                }
                else {
                    $('.relation li, .relation .activity-block').show();
                }
            }, 500);
            return false;
        });
    }
}

$(function () {
    new Relation().init();
});