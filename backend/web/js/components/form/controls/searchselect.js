$(function() {

    /**
     * Подготавливает приходящие данные с сервера для selectize.
     * Данные могут быть в 2х форматах
     * 1 - просто массив объектов [{id:1, title: 2}, ...]
     * 2 - данные которые приходят с api - у них другой формат  {"error":0,"data":[ {id:1, title: 2}...,{},{} ]}
     * @param response
     */
    function prepareData(response) {

        if (response.data !== undefined) {
            return response.data;
        }
        else {
            return response;
        }
    }

    $('select[class^=search-select-]').each(function() {

        var url = $(this).data('url')
            ,value_field    = $(this).data('value-field')
            ,label_field    = $(this).data('label-field')
            ,search_field   = $(this).data('search-field')
            ,query_field    = $(this).data('query-field')
            ,query_data     = $(this).data('query-data')
            ,request_method = $(this).data('request-method')
        ;

        query_data[query_field] = '';

        $(this).selectize({

            valueField: value_field,
            labelField: label_field,
            searchField: search_field,

            create: false,

            load: function(query, callback) {
                if (!query.length) return callback();
                // отправляемые данные.
                query_data[query_field] = query;
                $.ajax({
                    url: url,
                    type: request_method,
                    dataType: 'json',
                    data: query_data,
                    error: function() {
                        callback();
                    },
                    success: function(res) {
                        callback(prepareData(res));
                    }
                });
            }
        });
    });
});