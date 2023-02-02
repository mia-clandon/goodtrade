define('widgets/ui-animation-mixin', ['jquery', 'jquery-ui'], function( $ ) {
    var animationMixin = $.ui.animationMixin = {
        _showElement : function ( element , event ) {
            element.fadeIn({
                duration : 150,
                queue : false
            }).animate({
                marginTop : 0
            },{
                duration : 150,
                queue : false
            });

            if( event )  {
                this._bindBodyClickEvent( element );
                event.stopPropagation();
            }
        },
        _hideElement : function ( element ) {
            element.fadeOut({
                duration : 150,
                queue : false
            }).animate({
                marginTop : 5
            },{
                duration : 150,
                queue : false
            });
        },
        _bindBodyClickEvent : function( element ) {
            if( element instanceof jQuery === false || element.length === 0 ) {
                return;
            }
            this._on( $('body'), {
                'click' : function( event ) {
                    var $target = $( event.target );
                    if( $target.is( element ) === true || element.has( $target ).length > 0 ) {
                        return false;

                    }
                    this._hideElement( element );
                    $('body').off(event);
                }
            });
        }
    };
    return animationMixin;
});