/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config
    config.language = 'ru';

    // Настройка панели инструментов
    config.toolbarGroups = [
        //{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        //{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        //{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
        //{ name: 'forms', groups: [ 'forms' ] },
        //'/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        //{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
        { name: 'paragraph', groups: [ 'list', 'align' ] },
        '/',
        { name: 'links', groups: [ 'links' ] },
        { name: 'insert', groups: [ 'insert' ] },
        //'/',
        { name: 'styles', groups: [ 'styles' ] },
        //{ name: 'colors', groups: [ 'colors' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'others', groups: [ 'others' ] },
        //{ name: 'about', groups: [ 'about' ] }
    ];

    // Убираем лишние кнопки
    //config.removeButtons = 'Source,Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Undo,Redo,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Bold,Italic,Underline,Strike,Subscript,Superscript,CopyFormatting,RemoveFormat,NumberedList,BulletedList,Outdent,Indent,Blockquote,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Link,Unlink,Anchor,Image,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Font,FontSize,TextColor,BGColor,Maximize,ShowBlocks,About';
    config.removeButtons = 'Anchor,Image,Flash,Table,HorizontalRule,Smiley,PageBreak,Iframe,Styles,Font,FontSize';

    // Настраиваем варианты форматирования
    //config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre;address;div';
    config.format_tags = 'p;div';

    // Simplify the dialog windows.
    //config.removeDialogTabs = 'image:advanced;link:advanced';

    // Убираем указание тегов в строке состояния
    config.removePlugins = 'elementspath';
};

// Настраиваем модальное окно вставки ссылки, убирая лишнее
CKEDITOR.on( 'dialogDefinition', function( ev )
{
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;
    ev.data.definition.resizable = CKEDITOR.DIALOG_RESIZE_NONE;

    if ( dialogName == 'link' ) {
        var infoTab = dialogDefinition.getContents( 'info' );
        infoTab.remove( 'protocol' );
        dialogDefinition.removeContents( 'target' );
        dialogDefinition.removeContents( 'advanced' );
    }
});
