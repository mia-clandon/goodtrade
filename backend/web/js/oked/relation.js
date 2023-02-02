/**
 * Работа со связями ОКЭД'ов.
 * @type {{options: {active_table: null}, find: Relation.find, createRelation: Relation.createRelation, removeRelation: Relation.removeRelation, bindEvents: Relation.bindEvents, init: Relation.init}}
 */
var Relation = {
    options: {
        active_table: null
    },
    find: function(key, name) {
        var self_object = this;
        $.ajax({url: '/oked/find-oked', data: {key: key, name: name}, type: 'POST'})
        .success(function(data) {
            self_object.options.active_table.find('tbody').html(data);
        });
    },
    createRelation: function () {
        var from = [], to = [];
        $('.oked-list:eq(0) input[type="checkbox"]:checked').each(function () {
            from.push(parseInt($(this).val()));
        });
        $('.oked-list:eq(1) input[type="checkbox"]:checked').each(function () {
            to.push(parseInt($(this).val()));
        });
        $.ajax({data: {from: from, to: to}, type: 'POST', url: '/oked/create-relations'})
        .success(function(message) {
            if(message.success == true) {
                window.location.reload();
            }
            else {
                alert('Произошла ошибка, попробуйте позже.');
            }
        });
    },
    removeRelation: function (from_key, to_key) {
        $.ajax({url: '/oked/remove-relation', data: {from: from_key, to: to_key}, type: 'post'})
        .success(function(message) {
            if(message.success == true) {
                window.location.reload();
            }
            else {
                alert('Произошла ошибка, попробуйте позже.');
            }
        });
    },
    bindMainActions: function () {
        var self_object = this;
        $('.main .items .item').click(function () {
            $(this).siblings('.item').removeClass('selected');
            $(this).addClass('selected');
            var main_code = $(this).find('.code').text();
            self_object.findLinks(main_code, 1);
            self_object.findLinks(main_code, 0);
        })

    },
    bindLinksActions: function (items_div) {
        this.revealEvent(items_div);
        this.deleteEvent(items_div);
    },
    bindAddActions: function (items_add_div) {
        items_add_div.siblings('input[name="connect"]').css('display', 'block');
        this.checkedEvent(items_add_div);
        this.revealEvent(items_add_div);
    },
    checkedEvent: function (items_div) {
        items_div.find('input[type=checkbox]').click(function () {
            $(this).siblings('.code').addClass('selected');
        });
    },
    revealEvent: function (items_div) {
        items_div.find('.reveal').click(function () {
            var code = $(this).siblings('.code').text();
            $('input[name="main-code"]').val(code);
            $('input[name="main-code"]').trigger('input');
        });
    },
    deleteEvent: function (items_div) {
        var self_object = this;
        items_div.find('.delete').click(function () {
            var code = $(this).siblings('.code').text();

            var from_key;
            var to_key;
            var is_customer = 1;
            if(items_div.parent().hasClass('provider')){
                is_customer = 0;
            }

            if(is_customer) {
                from_key = code;
                to_key = $('input[name="main-code"]').val();
            } else {
                from_key = $('input[name="main-code"]').val();
                to_key = code;
            }
            self_object.removeRelationKey(from_key, to_key, is_customer);
        });
    },
    clear: function () {
        $('.oked .items').html('');
        $('.oked .items-add').html('');
        $('input[name="customer-code"]').val('');
        $('input[name="customer-code"]').attr('readonly', 'readonly');
        $('input[name="provider-code"]').val('');
        $('input[name="provider-code"]').attr('readonly', 'readonly');
        $('input[name="connect"]').hide();
    },
    findMain: function(text) {
        var self_object = this;
        $.ajax({url: '/oked/find-main', data: {text: text}, type: 'POST'})
            .success(function(data) {
                $('.main .items').html(data);
                self_object.bindMainActions();
            });
    },
    findLinks: function(code, is_customer) {
        var self_object = this;
        $.ajax({url: '/oked/find-links', data: {code: code, is_customer: is_customer}, type: 'POST'})
            .success(function(data) {
                var items_div;
                if(is_customer) {
                    $('input[name="customer-code"]').removeAttr('readonly');
                    items_div = $('.customer .items');
                } else {
                    $('input[name="provider-code"]').removeAttr('readonly');
                    items_div = $('.provider .items');
                }
                items_div.html(data);

                self_object.bindLinksActions(items_div);
            });
    },
    findAdd: function(code, text, is_customer) {
        var self_object = this;
        $.ajax({url: '/oked/find-add', data: {code: code, text: text, is_customer: is_customer}, type: 'POST'})
            .success(function(data) {
                var items_add_div;
                if(is_customer) {
                    items_add_div = $('.customer .items-add');
                } else {
                    items_add_div = $('.provider .items-add');
                }
                items_add_div.siblings('.items').hide();
                items_add_div.siblings('.items-title').find('i.fa-angle-up').show();
                items_add_div.html(data);
                items_add_div.show();
                self_object.bindAddActions(items_add_div);
            });
    },
    removeRelationKey: function (from_key, to_key, is_customer) {
        var self_object = this;
        $.ajax({url: '/oked/remove-relation', data: {from: from_key, to: to_key}, type: 'post'})
            .success(function(message) {
                if(message.success == true) {
                    var code;
                    if(is_customer) {
                        code = to_key;
                    } else {
                        code = from_key;
                    }
                    self_object.findLinks(code, is_customer);
                } else {
                    alert('Произошла ошибка, попробуйте позже.');
                }
            });
    },
    createRelationKey: function (from_key, to_key, is_customer) {
        var self_object = this;
        $.ajax({url: '/oked/create-relations', data: {from: from_key, to: to_key}, type: 'POST'})
            .success(function(message) {
                if(message.success == true) {
                    var code;
                    if(is_customer) {
                        code = to_key;
                        $('.customer').find('i.fa-angle-up').trigger('click');
                    } else {
                        code = from_key;
                        $('.provider').find('i.fa-angle-up').trigger('click');
                    }
                    self_object.findLinks(code[0], is_customer);
                }
                else {
                    alert('Произошла ошибка, попробуйте позже.');
                }
            });
    },
    bindEvents: function() {
        var self_object = this;
        $('input.key-input').change(function() {
            self_object.options.active_table = $(this).parents('div.oked-list').find('table');
            var name = $(this).parents('div.oked-list').find('input.name-input').val();
            self_object.find($(this).val(), name);
        });
        $('input.name-input').change(function() {
            self_object.options.active_table = $(this).parents('div.oked-list').find('table');
            var key = $(this).parents('div.oked-list').find('input.key-input').val();
            self_object.find(key, $(this).val());
        });
        var $body = $('body');
        $body.on('change', '.oked-list:eq(0) input[type="checkbox"]', function() {
           if ($(this).is(':checked')) {
                $('.oked-list:eq(0) input[type="checkbox"]').not(this).attr('disabled', 'disabled');
                $('.relation-oked-button').removeAttr('disabled')
           }
           else {
               $('.oked-list:eq(0) input[type="checkbox"]').not(this).removeAttr('disabled');
               $('.relation-oked-button').attr('disabled', 'disabled');
           }
        });
        $body.on('click', '.relation-oked-button', function() {
            self_object.createRelation();
        });
        $body.on('click', '.remove-oked-relation', function (e) {
            e.preventDefault();
            var from_oked = parseInt($(this).data('from'));
            var to_oked = parseInt($(this).data('to'));
            self_object.removeRelation(from_oked, to_oked);
            return false;
        });

        $('input[name="main-code"]').focus();
        var timeout = null;
        $body.on('input', 'input[name="main-code"]', function() {
            self_object.clear();
            var self = $(this);
            clearTimeout(timeout);

            timeout = setTimeout(function () {
                if(self.val() != '') {
                    self_object.findMain(self.val());
                } else {
                    self_object.clear();
                }
            }, 1000);
        });
        $body.on('input', 'input[name="customer-code"], input[name="provider-code"]', function() {
            var self = $(this);
            var is_customer = 1;
            if(self.attr('name') == 'provider-code') {
                is_customer = 0;
            }
            clearTimeout(timeout);

            timeout = setTimeout(function () {
                if(self.val() != '') {
                    self_object.findAdd($('input[name="main-code"]').val(), self.val(), is_customer);
                } else {
                    self.siblings('.items-title').find('i.fa-angle-up').trigger('click');
                }
            }, 1000);
        });
        $body.on('click', 'i.fa-angle-up', function() {
            $(this).hide();
            $(this).parent().siblings('.items-add').html('').hide();
            $(this).parent().siblings('.items').show();
            $(this).parent().siblings('input[type=text]').val('');
            $(this).parent().siblings('input[type=button]').hide();
        });
        $body.on('click', 'input[name="connect"]', function() {
            var codes = [];
            $(this).siblings('.items-add').find('.code.selected').each(function( index ) {
                codes.push($( this ).text());
            });

            var is_customer = 1;
            if($(this).parent().hasClass('provider')){
                is_customer = 0;
            }

            if(is_customer) {
                from_key = codes;
                to_key = [$('input[name="main-code"]').val()];
            } else {
                from_key = [$('input[name="main-code"]').val()];
                to_key = codes;
            }

            self_object.createRelationKey(from_key, to_key, is_customer);
        });
    },
    init: function(options) {
        this.options = $.extend(this.options, options);
        this.bindEvents();
        return this;
    }
};

$(function() {
    Relation.init();
});