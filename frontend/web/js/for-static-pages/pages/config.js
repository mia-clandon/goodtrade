var require = {
    baseUrl: '/js/for-static-pages/',
    paths: {
        'jquery'                  : 'libs/jquery-3.2.1.min',
        'jquery-ui'               : 'libs/jquery-ui',
        'jquery.mask'             : 'libs/jquery.mask',
        'jquery.cookie'           : 'libs/jquery.cookie',
        'jquery.nanoscroller'     : 'libs/jquery.nanoscroller.min',
        'jquery.perfectScrollbar' : 'libs/perfect-scrollbar.jquery.min',
        'jquery.fileupload'       : 'libs/jquery.fileupload',
        'jquery.scrollmagic'      : 'libs/jquery.scrollmagic',
        'jquery.steps'            : 'libs/jquery.steps',
        'validator'               : 'libs/jquery.form-validator.min',
        'jsrender'                : 'libs/jsrender.min',
        'slick'                   : 'libs/slick.min',
        'QUnit'                   : 'libs/qunit-2.0.1',
        'underscore'              : 'libs/underscore',
        'ScrollMagic'             : 'libs/ScrollMagic',
        'notify'                  : 'libs/notify.min',
        'lazyload'                : 'libs/jquery.lazyload'
    },
    shim : {
        'QUnit': {
            exports: 'QUnit',
            init: function() {
                QUnit.config.autoload = false;
                QUnit.config.autostart = false;
            }
        },
        'jquery.steps' : {
            deps : ['jquery']
        }
    },
    waitSeconds: 30
};