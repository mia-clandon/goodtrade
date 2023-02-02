define('widgets/jq-keeper', ['jquery', 'jquery-ui', 'jquery.cookie'], function($){
    /**
     * @example Пример использования
     * // $('body').jsKeeper();
     * @exports widgets/jq-keeper
     */

   $.fn.jsKeeper = function(options) {

       // Если не подключен плагин jquery.cookie
       if($.cookie === undefined ) {
           return;
       } else {
           // Включить поддержку JSON
           $.cookie.json = true;
       }

       // Настройки по умолчанию
       var options = $.extend({}, {expires : 7}, options);

       // Инициализация кнопок в куках
         $.each($('.js-keeper'), function(i, el) {
             var $el = $(el),
                 key = $el.data('key'),
                 id = $el.data('id'),
                 cookie = $.cookie(key);
            if(cookie instanceof Array === true) {
                if(cookie.indexOf(id) !== -1){
                    $el.addClass('is-active');
                }
            }
         });

       //Если элементом не является body

        $(this).on('click', '.js-keeper', function(event){
            var $el = $(event.currentTarget),
                key = $(this).data('key'),
                id = $(this).data('id'),
                cookie = $.cookie(key);

            if(cookie instanceof Array) {
                var index = cookie.indexOf(id);
                if(index === -1 ) {
                    cookie.push(id);
                    $el.addClass('is-active');
                } else {
                    cookie.splice(index, 1);
                    $el.removeClass('is-active');
                    if(cookie.length === 0) {
                        $.removeCookie(key);
                        cookie = undefined;
                    }
                }
            } else {
                $.removeCookie(key);
                $el.addClass('is-active');
                cookie = [id];
            }
            $.cookie(key, cookie, options);
            event.preventDefault();
        });
   }
});