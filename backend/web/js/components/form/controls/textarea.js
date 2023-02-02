$(function() {
    $('textarea.tinymce-control').each(function() {
        tinymce.init({
            selector: $(this).selector,
            language: 'ru'
        });
    });
    $('textarea.ckeditor-control').each(function() {
        CKEDITOR.replaceClass = 'ckeditor-control';
    });
});