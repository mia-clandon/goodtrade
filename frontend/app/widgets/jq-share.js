export default $.fn.share = function (options) {
    var settings = $.extend({
        width: 400,
        height: 300,
        left: ( screen.width / 2 ) - ( 400 / 2 ),
        top: ( screen.height / 2 ) - ( 300 / 2 )
    }, options);
    return this.each(function () {
        $(this).on('click', '.jq-share-link', function (event) {
            var url = event.currentTarget.href;
            window.open(url, '', 'width=' + settings.width + ', height=' + settings.height + ', left=' + settings.left + ', top=' + settings.top);
            event.preventDefault();
        });
    });
};