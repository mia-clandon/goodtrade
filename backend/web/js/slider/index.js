/**
 * Работа с слайдером главной страницы.
 */
class Slide {

    constructor() {
        this.body = $('body');
    }

    /**
     * Получения описании компании
     * @param firm_id
     * @param callback
     */
    getFirmDesc(firm_id, callback) {
        $.ajax({
            data: {firm_id: firm_id},
            type: 'POST',
            url: '/slider/get-ajax-firm-desc'
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

    toggleFields(){
        if($('input[name=tag]:checked').val() == 'firm') {
            $('select[name=firm_id]').parent().show();

            $('input[name=title]').parent().hide();
            $('input[name=button]').parent().hide();
            $('input[name=link]').parent().hide();
            $('input[name=tip]').parent().hide();
        } else {
            $('select[name=firm_id]').parent().hide();

            $('input[name=title]').parent().show();
            $('input[name=button]').parent().show();
            $('input[name=link]').parent().show();
            $('input[name=tip]').parent().show();
        }
    }

    toggleTypes(){
        $('.slide-type .example').hide();

        switch($('input[name=type]:checked').val()) {
            case 'small_square':
                $('.slide-type .one-one').show();
                break;
            case 'horizontal_square':
                $('.slide-type .one-two').show();
                break;
            case 'vertical_square':
                $('.slide-type .two-one').show();
                break;
            case 'big_square':
                $('.slide-type .two-two').show();
                break;
        }
    }

    init() {
        let th = this;

        $('.slider .js-remove').on('click',function () {
            return confirm('Вы деситвительно хотите удалить элемент?');
        });

        $('input[name=tag]').on('change',function () {
            th.toggleFields();
        });

        $('input[name=type]').on('change',function () {
            th.toggleTypes();
        });

        $('select[name=firm_id]').on('change',function () {
            th.getFirmDesc($('select[name=firm_id]').val(), function (data) {
                CKEDITOR.instances.description.setData(data.description);
            });
        });

        th.toggleFields();
        th.toggleTypes();
    }
}

$(function () {
    new Slide().init();
});