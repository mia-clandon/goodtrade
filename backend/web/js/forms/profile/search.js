$(function () {
    PartIndexer.init({
        form: $('#profile-indexer-form'),
        indexer_url: '/profile/indexer',
        clear_index_url: '/profile/clear-index',
    });
});