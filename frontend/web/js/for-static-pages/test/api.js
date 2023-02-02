$(function() {

    $('input[name=product-find-submit]').on('click', function(){
        var query = $('input[name=product-find]').val();
        $.ajax({url: '/api/product/find', type: 'POST', data: {
            query: query,
            limit: 3
        }})
        .done(function(data){
            $('code#result-product-find').html(JSON.stringify(data));
        });
    });

    $('input[name=category-find-submit]').on('click', function(){
        var query = $('input[name=category-find]').val();
        $.ajax({url: '/api/activity/get-by-category', type: 'POST', data: {
            category_id: parseInt(query)
        }})
        .done(function(data){
            $('code#result-category-find').html(JSON.stringify(data));
        });
    });

    $('select[name=vocabulary-find]').selectize({

        valueField: 'id',
        labelField: 'vocabulary',
        searchField: 'vocabulary',

        create: false,

        load: function(query, callback) {
            if (!query.length) return callback();
            $.ajax({
                url: '/api/vocabulary/find',
                type: 'POST',
                dataType: 'json',
                data: {query: query},
                error: function() {
                    callback();
                },
                success: function(res) {
                    $('code#result-vocabulary-find').html(JSON.stringify(res));
                    callback(res.data);
                }
            });
        }
    });

    $('input[name=get-terms]').click(function() {
        var vocabulary_id = $('input[name=vocabulary_id]').val();
        $.ajax({url: '/api/vocabulary/terms', type: 'POST', data: {
            vocabulary_id: vocabulary_id
        }})
        .done(function(data){
            $('code#result-get-terms').html(JSON.stringify(data));
        });
    });

    $('input[name=search-firm]').click(function() {
        var query = $('input[name=firm]').val();
        $.ajax({url: '/api/firm/find', type: 'POST', data: {
            query: query
        }})
        .done(function(data){
            $('code#result-firm').html(JSON.stringify(data));
        });
    });

    $('input[name=search-profile-firm]').click(function() {
        var query = $('input[name=profile]').val();
        $.ajax({url: '/api/profile/find', type: 'POST', data: {
            query: query
        }})
        .done(function(data){
            $('code#result-profile').html(JSON.stringify(data));
        });
    });

    $('input[name=get-firm]').click(function() {
        var id = $('input[name=firm_id]').val();
        var firm_columns = $('input[name=firm_columns]').val();
        $.ajax({url: '/api/profile/get', type: 'POST', data: {
            id: id,
            columns: firm_columns
        }})
        .done(function(data){
            $('code#result-get-firm').html(JSON.stringify(data));
        });
    });

    $('input[name=search-location]').click(function() {
        var location = $('input[name=location]').val();
        $.ajax({url: '/api/location/find', type: 'POST', data: {
            query: location
        }})
        .done(function(data){
            $('code#result-location').html(JSON.stringify(data));
        });
    });

    $('#file-upload').fileupload({
        dataType: 'json',
        // если не нужна миниатюра - убираем formData
        formData: {
            thumbnails: JSON.stringify({
                width: 50, // обязательна для ресайза
                height: 50,
                type: 'CROP' // возможные типы (CROP, NONE, WIDTH, HEIGHT, AUTO)
            })
        },
        done: function (e, data) {
            console.log(data.result.files);
        }
    });
});