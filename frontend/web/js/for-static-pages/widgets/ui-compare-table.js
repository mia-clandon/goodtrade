define(['jquery','jquery-ui','jsrender'],function($){
    return $.widget('ui.compareTable', {
        options : {
            url : {
              product : '../public/api/',
              search : '../public/api/'
            },
            head : null,
            body : null,
            foot : null,
            row : null,
            sidebar : null,
            spoiler : null,
            wrap : null,
            float : null,
            headHeight : 0,
            bodyHeight : 0,
            bodyOffset : 0,
            padding: 60,
            animation : null,
            navbarHeight : 0,
            popup : null
        },
        _create : function() {
            var self = this;
            this.options.head = this.element.find('thead');
            this.options.body = this.element.find('tbody');
            this.options.foot = this.element.find('tfoot');
            this.options.sidebar = $('#compare-sidebar');
            this.options.wrap = $('.compare-wrap');
            this.options.row = this.options.head.find('tr');
            this.options.popup = $('#popup-commerce');
            this.options.spoiler = this.element.find('.compare-spoiler');
            this.options.spoilerToggle = this.element.find('.compare-spoiler-toggle');
        },
        _handleClick : function(e) {
            this._addItem(1);
        },
        _handleCommerceBtnClick : function(e) {
                var $el = $(e.target),
                    $popup = this.option('popup');
            $popup.css({position: 'fixed',left : ($el.offset().left - 20) + 'px',bottom : 0+'px'});
        },
        _init :function() {
            var $head = this.option('head'),
                $body = this.option('body'),
                $float = $head.find('tr:first')
                              .clone()
                              .addClass('compare-float')
                              .prependTo($head);
            $float.css({top:'-60px',paddingTop:'60px'});
            $head.prepend($float);
            this.option('float',$float);
        },
        _addItem : function(id) {
            var $table = this.option('table'),
                $head = this.option('head'),
                $body = this.option('body');

            $.getJSON('../public/api/' + id + '.json',function(obj){
                var  $th = $('<th/>',{
                        'text' : obj.title
                    }).appendTo($head.find('tr')),
                    $thumb = $('<td/>',{
                        'text' : obj.images.count,
                        'style' : 'background-image:url(\"' + obj.images.bg + '\");'
                    }).appendTo($body.find('.compare-thumbs')),
                    $price = $('<td/>',{
                       text :  '380 000 тг.'
                    }).appendTo($body.find('.compare-price')),
                    $expires = $('<td/>',{
                        text : obj.expires
                    }).appendTo($body.find('.compare-expires'));
                    $('<td/>',{
                       text  : obj.size
                    }).appendTo($body.find('.compare-size'));
                    $('<td/>',{
                        text : obj.exists
                    }).appendTo($body.find('.compare-exists'));
                    $('<td/>', {
                        text : obj.billCondition.preBill
                    }).appendTo($body.find('.compare-bill'));
                    $('<td/>',{
                       text  : obj.deliveryCondition
                    }).appendTo($body.find('.compare-delivery'));
                    $('<td/>',{
                       text : obj.address
                    }).appendTo($body.find('.compare-address'));
                    $('<td/>', {
                       text : obj.power
                    }).appendTo($body.find('.compare-power'));

            });
        },
        _handleToggle : function(e) {
          var $el = $(e.target),
              $spoiler = this.option('spoiler'),
              scrollHeight = $spoiler.prop('scrollHeight');
            $el.toggleClass('is-active');
            $spoiler.animate({height : ($spoiler.hasClass('is-open')) ? '0px' : scrollHeight},1000).toggleClass('is-open');
        },
        _handleClick : function(e) {
          var $el = $(e.target),
              action = $el.data('action');
            if(action == 'add') {

            }
        },
        _handleAddBtn : function(e) {
            this._showSidebar();
        },
        _handleNavbarHide : function(e) {
            var $float = this.option('float'),
            scrollTop = $(window).scrollTop();
            $float.animate({paddingTop: 0},{
                duration:300,
                easing : 'linear',
                queue : false
            });
        },
        _handleNavbarShow : function(e) {
            var $float = this.option('float'),
                scrollTop = $(window).scrollTop();
            $float.animate({paddingTop: 60},{
                duration:300,
                easing : 'linear',
                queue : false
            });
            this.option('padding',60);
        },
        _handleScroll : function(e){
             var scrollTop = $(window).scrollTop(),
                 $body = this.option('body'),
                 $head = this.option('head'),
                 $sidebar = this.option('sidebar'),
                 offset = $('.compare').offset().top,
                 top = scrollTop - offset,
                 float = this.option('float'),
                 padding = this.option('padding'),
                 offsetBottom = $body.height() + $head.height() + offset;
            if(scrollTop >= offset  - padding  && scrollTop <= offsetBottom - padding ) {
                float.css({top : top});
                $sidebar.css({top: top});
            }
        },
        _showSidebar : function() {
            var $sidebar = this.option('sidebar'),
                $wrap = this.option('wrap');
            $wrap.css({paddingRight:480+'px',marginRight:-480+'px'});
            $sidebar.css({width:480+'px'});
        },
        _setOption : function (key, val) {

        },
        add : function(id) {
            var $root = this.element,
                url = this.option('url.product'),
                $table = $.templates('<div class="compare-column">' +
                    '<table class="compare">' +
                    '<thead>' +
                    '<th><a href="#">{{:title}}</a></th>' +
                    '</thead>' +
                    '<tbody>' +
                    '<tr class="compare-thumbs"><td style="background-image: url({{:images.bg}})">{{:images.count}}</td></tr>' +
                    '<tr class="compare-price"><td><div class="price">{{:price.current}}</div></td></tr>' +
                    '<tr><td>{{:expires}}</td></tr>' +
                    '<tr><td>{{:count}}</td></tr>' +
                    '<tr><td>{{:exists}}</td></tr>' +
                    '<tr><td>{{:billing.preBill}}<br> {{:billing.postBill}}</td></tr>' +
                    '<tr><td>{{:delivery}}</td></tr>' +
                    '<tr><td>{{:address}}</td></tr>' +
                    '<tr><td>{{:power}}</td></tr>' +
                    '</tbody>' +
                    '</table></div>');
            $.ajax({url: url+id+'.json', type: 'POST', dataType: 'json', data: {
                query: id
            }})
                .done(function(data){
                    console.log(data);
                    var html = $table.render(data);
                    $root.parent().after(html);
                });
        }
    });
});