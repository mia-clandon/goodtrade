$(function () {
    PartIndexer.init({
        form: $('#product-indexer-form'),
        indexer_url: '/product/indexer',
        clear_index_url: '/product/clear-index',
    });
});