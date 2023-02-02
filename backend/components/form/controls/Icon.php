<?php

namespace backend\components\form\controls;

use common\libs\form\components\Control;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * Class Icon
 * @package backend\components\bootstrap\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class Icon extends Control {

    /** Общий класс иконок */
    const ICON_CLASS = 'glyphicon';

    /** @var null|string */
    private $icon = null;

    /**
     * Icon constructor.
     * @param array $params
     */
    public function __construct(array $params) {
        $class = ArrayHelper::getValue($params, 'class', '');
        if (!empty($class)) {
            $this->icon = $class;
        }
    }

    /**
     * @param $class
     * @return Icon
     */
    public static function createIconByClass($class) {
        return new self([
            'class' => $class,
        ]);
    }

    /**
     * Рендеринг span'a с классом иконки.
     * @return string
     */
    public function render(): string {
        if (in_array($this->icon, self::$possible_icons) && !is_null($this->icon)) {
            parent::render();
            $this->addClass('glyphicon');
            $this->addClass($this->icon);
            return Html::tag('span', '', $this->getAttributes());
        }
        else {
            return '';
        }
    }

    CONST GLYPHICON_ASTERISK = 'glyphicon-asterisk';
    CONST GLYPHICON_PLUS = 'glyphicon-plus';
    CONST GLYPHICON_EURO = 'glyphicon-euro';
    CONST GLYPHICON_EUR = 'glyphicon-eur';
    CONST GLYPHICON_MINUS = 'glyphicon-minus';
    CONST GLYPHICON_CLOUD = 'glyphicon-cloud';
    CONST GLYPHICON_ENVELOPE = 'glyphicon-envelope';
    CONST GLYPHICON_PENCIL = 'glyphicon-pencil';
    CONST GLYPHICON_GLASS = 'glyphicon-glass';
    CONST GLYPHICON_MUSIC = 'glyphicon-music';
    CONST GLYPHICON_SEARCH = 'glyphicon-search';
    CONST GLYPHICON_HEART = 'glyphicon-heart';
    CONST GLYPHICON_STAR = 'glyphicon-star';
    CONST GLYPHICON_STAR_EMPTY = 'glyphicon-star-empty';
    CONST GLYPHICON_USER = 'glyphicon-user';
    CONST GLYPHICON_FILM = 'glyphicon-film';
    CONST GLYPHICON_TH_LARGE = 'glyphicon-th-large';
    CONST GLYPHICON_TH = 'glyphicon-th';
    CONST GLYPHICON_TH_LIST = 'glyphicon-th-list';
    CONST GLYPHICON_OK = 'glyphicon-ok';
    CONST GLYPHICON_REMOVE = 'glyphicon-remove';
    CONST GLYPHICON_ZOOM_IN = 'glyphicon-zoom-in';
    CONST GLYPHICON_ZOOM_OUT = 'glyphicon-zoom-out';
    CONST GLYPHICON_OFF = 'glyphicon-off';
    CONST GLYPHICON_SIGNAL = 'glyphicon-signal';
    CONST GLYPHICON_COG = 'glyphicon-cog';
    CONST GLYPHICON_TRASH = 'glyphicon-trash';
    CONST GLYPHICON_HOME = 'glyphicon-home';
    CONST GLYPHICON_FILE = 'glyphicon-file';
    CONST GLYPHICON_TIME = 'glyphicon-time';
    CONST GLYPHICON_ROAD = 'glyphicon-road';
    CONST GLYPHICON_DOWNLOAD_ALT = 'glyphicon-download-alt';
    CONST GLYPHICON_DOWNLOAD = 'glyphicon-download';
    CONST GLYPHICON_UPLOAD = 'glyphicon-upload';
    CONST GLYPHICON_INBOX = 'glyphicon-inbox';
    CONST GLYPHICON_PLAY_CIRCLE = 'glyphicon-play-circle';
    CONST GLYPHICON_REPEAT = 'glyphicon-repeat';
    CONST GLYPHICON_REFRESH = 'glyphicon-refresh';
    CONST GLYPHICON_LIST_ALT = 'glyphicon-list-alt';
    CONST GLYPHICON_LOCK = 'glyphicon-lock';
    CONST GLYPHICON_FLAG = 'glyphicon-flag';
    CONST GLYPHICON_HEADPHONES = 'glyphicon-headphones';
    CONST GLYPHICON_VOLUME_OFF = 'glyphicon-volume-off';
    CONST GLYPHICON_VOLUME_DOWN = 'glyphicon-volume-down';
    CONST GLYPHICON_VOLUME_UP = 'glyphicon-volume-up';
    CONST GLYPHICON_QRCODE = 'glyphicon-qrcode';
    CONST GLYPHICON_BARCODE = 'glyphicon-barcode';
    CONST GLYPHICON_TAG = 'glyphicon-tag';
    CONST GLYPHICON_TAGS = 'glyphicon-tags';
    CONST GLYPHICON_BOOK = 'glyphicon-book';
    CONST GLYPHICON_BOOKMARK = 'glyphicon-bookmark';
    CONST GLYPHICON_PRINT = 'glyphicon-print';
    CONST GLYPHICON_CAMERA = 'glyphicon-camera';
    CONST GLYPHICON_FONT = 'glyphicon-font';
    CONST GLYPHICON_BOLD = 'glyphicon-bold';
    CONST GLYPHICON_ITALIC = 'glyphicon-italic';
    CONST GLYPHICON_TEXT_HEIGHT = 'glyphicon-text-height';
    CONST GLYPHICON_TEXT_WIDTH = 'glyphicon-text-width';
    CONST GLYPHICON_ALIGN_LEFT = 'glyphicon-align-left';
    CONST GLYPHICON_ALIGN_CENTER = 'glyphicon-align-center';
    CONST GLYPHICON_ALIGN_RIGHT = 'glyphicon-align-right';
    CONST GLYPHICON_ALIGN_JUSTIFY = 'glyphicon-align-justify';
    CONST GLYPHICON_LIST = 'glyphicon-list';
    CONST GLYPHICON_INDENT_LEFT = 'glyphicon-indent-left';
    CONST GLYPHICON_INDENT_RIGHT = 'glyphicon-indent-right';
    CONST GLYPHICON_FACETIME_VIDEO = 'glyphicon-facetime-video';
    CONST GLYPHICON_PICTURE = 'glyphicon-picture';
    CONST GLYPHICON_MAP_MARKER = 'glyphicon-map-marker';
    CONST GLYPHICON_ADJUST = 'glyphicon-adjust';
    CONST GLYPHICON_TINT = 'glyphicon-tint';
    CONST GLYPHICON_EDIT = 'glyphicon-edit';
    CONST GLYPHICON_SHARE = 'glyphicon-share';
    CONST GLYPHICON_CHECK = 'glyphicon-check';
    CONST GLYPHICON_MOVE = 'glyphicon-move';
    CONST GLYPHICON_STEP_BACKWARD = 'glyphicon-step-backward';
    CONST GLYPHICON_FAST_BACKWARD = 'glyphicon-fast-backward';
    CONST GLYPHICON_BACKWARD = 'glyphicon-backward';
    CONST GLYPHICON_PLAY = 'glyphicon-play';
    CONST GLYPHICON_PAUSE = 'glyphicon-pause';
    CONST GLYPHICON_STOP = 'glyphicon-stop';
    CONST GLYPHICON_FORWARD = 'glyphicon-forward';
    CONST GLYPHICON_FAST_FORWARD = 'glyphicon-fast-forward';
    CONST GLYPHICON_STEP_FORWARD = 'glyphicon-step-forward';
    CONST GLYPHICON_EJECT = 'glyphicon-eject';
    CONST GLYPHICON_CHEVRON_LEFT = 'glyphicon-chevron-left';
    CONST GLYPHICON_CHEVRON_RIGHT = 'glyphicon-chevron-right';
    CONST GLYPHICON_PLUS_SIGN = 'glyphicon-plus-sign';
    CONST GLYPHICON_MINUS_SIGN = 'glyphicon-minus-sign';
    CONST GLYPHICON_REMOVE_SIGN = 'glyphicon-remove-sign';
    CONST GLYPHICON_OK_SIGN = 'glyphicon-ok-sign';
    CONST GLYPHICON_QUESTION_SIGN = 'glyphicon-question-sign';
    CONST GLYPHICON_INFO_SIGN = 'glyphicon-info-sign';
    CONST GLYPHICON_SCREENSHOT = 'glyphicon-screenshot';
    CONST GLYPHICON_REMOVE_CIRCLE = 'glyphicon-remove-circle';
    CONST GLYPHICON_OK_CIRCLE = 'glyphicon-ok-circle';
    CONST GLYPHICON_BAN_CIRCLE = 'glyphicon-ban-circle';
    CONST GLYPHICON_ARROW_LEFT = 'glyphicon-arrow-left';
    CONST GLYPHICON_ARROW_RIGHT = 'glyphicon-arrow-right';
    CONST GLYPHICON_ARROW_UP = 'glyphicon-arrow-up';
    CONST GLYPHICON_ARROW_DOWN = 'glyphicon-arrow-down';
    CONST GLYPHICON_SHARE_ALT = 'glyphicon-share-alt';
    CONST GLYPHICON_RESIZE_FULL = 'glyphicon-resize-full';
    CONST GLYPHICON_RESIZE_SMALL = 'glyphicon-resize-small';
    CONST GLYPHICON_EXCLAMATION_SIGN = 'glyphicon-exclamation-sign';
    CONST GLYPHICON_GIFT = 'glyphicon-gift';
    CONST GLYPHICON_LEAF = 'glyphicon-leaf';
    CONST GLYPHICON_FIRE = 'glyphicon-fire';
    CONST GLYPHICON_EYE_OPEN = 'glyphicon-eye-open';
    CONST GLYPHICON_EYE_CLOSE = 'glyphicon-eye-close';
    CONST GLYPHICON_WARNING_SIGN = 'glyphicon-warning-sign';
    CONST GLYPHICON_PLANE = 'glyphicon-plane';
    CONST GLYPHICON_CALENDAR = 'glyphicon-calendar';
    CONST GLYPHICON_RANDOM = 'glyphicon-random';
    CONST GLYPHICON_COMMENT = 'glyphicon-comment';
    CONST GLYPHICON_MAGNET = 'glyphicon-magnet';
    CONST GLYPHICON_CHEVRON_UP = 'glyphicon-chevron-up';
    CONST GLYPHICON_CHEVRON_DOWN = 'glyphicon-chevron-down';
    CONST GLYPHICON_RETWEET = 'glyphicon-retweet';
    CONST GLYPHICON_SHOPPING_CART = 'glyphicon-shopping-cart';
    CONST GLYPHICON_FOLDER_CLOSE = 'glyphicon-folder-close';
    CONST GLYPHICON_FOLDER_OPEN = 'glyphicon-folder-open';
    CONST GLYPHICON_RESIZE_VERTICAL = 'glyphicon-resize-vertical';
    CONST GLYPHICON_RESIZE_HORIZONTAL = 'glyphicon-resize-horizontal';
    CONST GLYPHICON_HDD = 'glyphicon-hdd';
    CONST GLYPHICON_BULLHORN = 'glyphicon-bullhorn';
    CONST GLYPHICON_BELL = 'glyphicon-bell';
    CONST GLYPHICON_CERTIFICATE = 'glyphicon-certificate';
    CONST GLYPHICON_THUMBS_UP = 'glyphicon-thumbs-up';
    CONST GLYPHICON_THUMBS_DOWN = 'glyphicon-thumbs-down';
    CONST GLYPHICON_HAND_RIGHT = 'glyphicon-hand-right';
    CONST GLYPHICON_HAND_LEFT = 'glyphicon-hand-left';
    CONST GLYPHICON_HAND_UP = 'glyphicon-hand-up';
    CONST GLYPHICON_HAND_DOWN = 'glyphicon-hand-down';
    CONST GLYPHICON_CIRCLE_ARROW_RIGHT = 'glyphicon-circle-arrow-right';
    CONST GLYPHICON_CIRCLE_ARROW_LEFT = 'glyphicon-circle-arrow-left';
    CONST GLYPHICON_CIRCLE_ARROW_UP = 'glyphicon-circle-arrow-up';
    CONST GLYPHICON_CIRCLE_ARROW_DOWN = 'glyphicon-circle-arrow-down';
    CONST GLYPHICON_GLOBE = 'glyphicon-globe';
    CONST GLYPHICON_WRENCH = 'glyphicon-wrench';
    CONST GLYPHICON_TASKS = 'glyphicon-tasks';
    CONST GLYPHICON_FILTER = 'glyphicon-filter';
    CONST GLYPHICON_BRIEFCASE = 'glyphicon-briefcase';
    CONST GLYPHICON_FULLSCREEN = 'glyphicon-fullscreen';
    CONST GLYPHICON_DASHBOARD = 'glyphicon-dashboard';
    CONST GLYPHICON_PAPERCLIP = 'glyphicon-paperclip';
    CONST GLYPHICON_HEART_EMPTY = 'glyphicon-heart-empty';
    CONST GLYPHICON_LINK = 'glyphicon-link';
    CONST GLYPHICON_PHONE = 'glyphicon-phone';
    CONST GLYPHICON_PUSHPIN = 'glyphicon-pushpin';
    CONST GLYPHICON_USD = 'glyphicon-usd';
    CONST GLYPHICON_GBP = 'glyphicon-gbp';
    CONST GLYPHICON_SORT = 'glyphicon-sort';
    CONST GLYPHICON_SORT_BY_ALPHABET = 'glyphicon-sort-by-alphabet';
    CONST GLYPHICON_SORT_BY_ALPHABET_ALT = 'glyphicon-sort-by-alphabet-alt';
    CONST GLYPHICON_SORT_BY_ORDER = 'glyphicon-sort-by-order';
    CONST GLYPHICON_SORT_BY_ORDER_ALT = 'glyphicon-sort-by-order-alt';
    CONST GLYPHICON_SORT_BY_ATTRIBUTES = 'glyphicon-sort-by-attributes';
    CONST GLYPHICON_SORT_BY_ATTRIBUTES_ALT = 'glyphicon-sort-by-attributes-alt';
    CONST GLYPHICON_UNCHECKED = 'glyphicon-unchecked';
    CONST GLYPHICON_EXPAND = 'glyphicon-expand';
    CONST GLYPHICON_COLLAPSE_DOWN = 'glyphicon-collapse-down';
    CONST GLYPHICON_COLLAPSE_UP = 'glyphicon-collapse-up';
    CONST GLYPHICON_LOG_IN = 'glyphicon-log-in';
    CONST GLYPHICON_FLASH = 'glyphicon-flash';
    CONST GLYPHICON_LOG_OUT = 'glyphicon-log-out';
    CONST GLYPHICON_NEW_WINDOW = 'glyphicon-new-window';
    CONST GLYPHICON_RECORD = 'glyphicon-record';
    CONST GLYPHICON_SAVE = 'glyphicon-save';
    CONST GLYPHICON_OPEN = 'glyphicon-open';
    CONST GLYPHICON_SAVED = 'glyphicon-saved';
    CONST GLYPHICON_IMPORT = 'glyphicon-import';
    CONST GLYPHICON_EXPORT = 'glyphicon-export';
    CONST GLYPHICON_SEND = 'glyphicon-send';
    CONST GLYPHICON_FLOPPY_DISK = 'glyphicon-floppy-disk';
    CONST GLYPHICON_FLOPPY_SAVED = 'glyphicon-floppy-saved';
    CONST GLYPHICON_FLOPPY_REMOVE = 'glyphicon-floppy-remove';
    CONST GLYPHICON_FLOPPY_SAVE = 'glyphicon-floppy-save';
    CONST GLYPHICON_FLOPPY_OPEN = 'glyphicon-floppy-open';
    CONST GLYPHICON_CREDIT_CARD = 'glyphicon-credit-card';
    CONST GLYPHICON_TRANSFER = 'glyphicon-transfer';
    CONST GLYPHICON_CUTLERY = 'glyphicon-cutlery';
    CONST GLYPHICON_HEADER = 'glyphicon-header';
    CONST GLYPHICON_COMPRESSED = 'glyphicon-compressed';
    CONST GLYPHICON_EARPHONE = 'glyphicon-earphone';
    CONST GLYPHICON_PHONE_ALT = 'glyphicon-phone-alt';
    CONST GLYPHICON_TOWER = 'glyphicon-tower';
    CONST GLYPHICON_STATS = 'glyphicon-stats';
    CONST GLYPHICON_SD_VIDEO = 'glyphicon-sd-video';
    CONST GLYPHICON_HD_VIDEO = 'glyphicon-hd-video';
    CONST GLYPHICON_SUBTITLES = 'glyphicon-subtitles';
    CONST GLYPHICON_SOUND_STEREO = 'glyphicon-sound-stereo';
    CONST GLYPHICON_SOUND_DOLBY = 'glyphicon-sound-dolby';
    CONST GLYPHICON_SOUND_5_1 = 'glyphicon-sound-5-1';
    CONST GLYPHICON_SOUND_6_1 = 'glyphicon-sound-6-1';
    CONST GLYPHICON_SOUND_7_1 = 'glyphicon-sound-7-1';
    CONST GLYPHICON_COPYRIGHT_MARK = 'glyphicon-copyright-mark';
    CONST GLYPHICON_REGISTRATION_MARK = 'glyphicon-registration-mark';
    CONST GLYPHICON_CLOUD_DOWNLOAD = 'glyphicon-cloud-download';
    CONST GLYPHICON_CLOUD_UPLOAD = 'glyphicon-cloud-upload';
    CONST GLYPHICON_TREE_CONIFER = 'glyphicon-tree-conifer';
    CONST GLYPHICON_TREE_DECIDUOUS = 'glyphicon-tree-deciduous';
    CONST GLYPHICON_CD = 'glyphicon-cd';
    CONST GLYPHICON_SAVE_FILE = 'glyphicon-save-file';
    CONST GLYPHICON_OPEN_FILE = 'glyphicon-open-file';
    CONST GLYPHICON_LEVEL_UP = 'glyphicon-level-up';
    CONST GLYPHICON_COPY = 'glyphicon-copy';
    CONST GLYPHICON_PASTE = 'glyphicon-paste';
    CONST GLYPHICON_ALERT = 'glyphicon-alert';
    CONST GLYPHICON_EQUALIZER = 'glyphicon-equalizer';
    CONST GLYPHICON_KING = 'glyphicon-king';
    CONST GLYPHICON_QUEEN = 'glyphicon-queen';
    CONST GLYPHICON_PAWN = 'glyphicon-pawn';
    CONST GLYPHICON_BISHOP = 'glyphicon-bishop';
    CONST GLYPHICON_KNIGHT = 'glyphicon-knight';
    CONST GLYPHICON_BABY_FORMULA = 'glyphicon-baby-formula';
    CONST GLYPHICON_TENT = 'glyphicon-tent';
    CONST GLYPHICON_BLACKBOARD = 'glyphicon-blackboard';
    CONST GLYPHICON_BED = 'glyphicon-bed';
    CONST GLYPHICON_APPLE = 'glyphicon-apple';
    CONST GLYPHICON_ERASE = 'glyphicon-erase';
    CONST GLYPHICON_HOURGLASS = 'glyphicon-hourglass';
    CONST GLYPHICON_LAMP = 'glyphicon-lamp';
    CONST GLYPHICON_DUPLICATE = 'glyphicon-duplicate';
    CONST GLYPHICON_PIGGY_BANK = 'glyphicon-piggy-bank';
    CONST GLYPHICON_SCISSORS = 'glyphicon-scissors';
    CONST GLYPHICON_BITCOIN = 'glyphicon-bitcoin';
    CONST GLYPHICON_BTC = 'glyphicon-btc';
    CONST GLYPHICON_XBT = 'glyphicon-xbt';
    CONST GLYPHICON_YEN = 'glyphicon-yen';
    CONST GLYPHICON_JPY = 'glyphicon-jpy';
    CONST GLYPHICON_RUBLE = 'glyphicon-ruble';
    CONST GLYPHICON_RUB = 'glyphicon-rub';
    CONST GLYPHICON_SCALE = 'glyphicon-scale';
    CONST GLYPHICON_ICE_LOLLY = 'glyphicon-ice-lolly';
    CONST GLYPHICON_ICE_LOLLY_TASTED = 'glyphicon-ice-lolly-tasted';
    CONST GLYPHICON_EDUCATION = 'glyphicon-education';
    CONST GLYPHICON_OPTION_HORIZONTAL = 'glyphicon-option-horizontal';
    CONST GLYPHICON_OPTION_VERTICAL = 'glyphicon-option-vertical';
    CONST GLYPHICON_MENU_HAMBURGER = 'glyphicon-menu-hamburger';
    CONST GLYPHICON_MODAL_WINDOW = 'glyphicon-modal-window';
    CONST GLYPHICON_OIL = 'glyphicon-oil';
    CONST GLYPHICON_GRAIN = 'glyphicon-grain';
    CONST GLYPHICON_SUNGLASSES = 'glyphicon-sunglasses';
    CONST GLYPHICON_TEXT_SIZE = 'glyphicon-text-size';
    CONST GLYPHICON_TEXT_COLOR = 'glyphicon-text-color';
    CONST GLYPHICON_TEXT_BACKGROUND = 'glyphicon-text-background';
    CONST GLYPHICON_OBJECT_ALIGN_TOP = 'glyphicon-object-align-top';
    CONST GLYPHICON_OBJECT_ALIGN_BOTTOM = 'glyphicon-object-align-bottom';
    CONST GLYPHICON_OBJECT_ALIGN_HORIZONTAL = 'glyphicon-object-align-horizontal';
    CONST GLYPHICON_OBJECT_ALIGN_LEFT = 'glyphicon-object-align-left';
    CONST GLYPHICON_OBJECT_ALIGN_VERTICAL = 'glyphicon-object-align-vertical';
    CONST GLYPHICON_OBJECT_ALIGN_RIGHT = 'glyphicon-object-align-right';
    CONST GLYPHICON_TRIANGLE_RIGHT = 'glyphicon-triangle-right';
    CONST GLYPHICON_TRIANGLE_LEFT = 'glyphicon-triangle-left';
    CONST GLYPHICON_TRIANGLE_BOTTOM = 'glyphicon-triangle-bottom';
    CONST GLYPHICON_TRIANGLE_TOP = 'glyphicon-triangle-top';
    CONST GLYPHICON_CONSOLE = 'glyphicon-console';
    CONST GLYPHICON_SUPERSCRIPT = 'glyphicon-superscript';
    CONST GLYPHICON_SUBSCRIPT = 'glyphicon-subscript';
    CONST GLYPHICON_MENU_LEFT = 'glyphicon-menu-left';
    CONST GLYPHICON_MENU_RIGHT = 'glyphicon-menu-right';
    CONST GLYPHICON_MENU_DOWN = 'glyphicon-menu-down';
    CONST GLYPHICON_MENU_UP = 'glyphicon-menu-up';

    public static $possible_icons = [
        self::GLYPHICON_ASTERISK,
        self::GLYPHICON_PLUS,
        self::GLYPHICON_EURO,
        self::GLYPHICON_EUR,
        self::GLYPHICON_MINUS,
        self::GLYPHICON_CLOUD,
        self::GLYPHICON_ENVELOPE,
        self::GLYPHICON_PENCIL,
        self::GLYPHICON_GLASS,
        self::GLYPHICON_MUSIC,
        self::GLYPHICON_SEARCH,
        self::GLYPHICON_HEART,
        self::GLYPHICON_STAR,
        self::GLYPHICON_STAR_EMPTY,
        self::GLYPHICON_USER,
        self::GLYPHICON_FILM,
        self::GLYPHICON_TH_LARGE,
        self::GLYPHICON_TH,
        self::GLYPHICON_TH_LIST,
        self::GLYPHICON_OK,
        self::GLYPHICON_REMOVE,
        self::GLYPHICON_ZOOM_IN,
        self::GLYPHICON_ZOOM_OUT,
        self::GLYPHICON_OFF,
        self::GLYPHICON_SIGNAL,
        self::GLYPHICON_COG,
        self::GLYPHICON_TRASH,
        self::GLYPHICON_HOME,
        self::GLYPHICON_FILE,
        self::GLYPHICON_TIME,
        self::GLYPHICON_ROAD,
        self::GLYPHICON_DOWNLOAD_ALT,
        self::GLYPHICON_DOWNLOAD,
        self::GLYPHICON_UPLOAD,
        self::GLYPHICON_INBOX,
        self::GLYPHICON_PLAY_CIRCLE,
        self::GLYPHICON_REPEAT,
        self::GLYPHICON_REFRESH,
        self::GLYPHICON_LIST_ALT,
        self::GLYPHICON_LOCK,
        self::GLYPHICON_FLAG,
        self::GLYPHICON_HEADPHONES,
        self::GLYPHICON_VOLUME_OFF,
        self::GLYPHICON_VOLUME_DOWN,
        self::GLYPHICON_VOLUME_UP,
        self::GLYPHICON_QRCODE,
        self::GLYPHICON_BARCODE,
        self::GLYPHICON_TAG,
        self::GLYPHICON_TAGS,
        self::GLYPHICON_BOOK,
        self::GLYPHICON_BOOKMARK,
        self::GLYPHICON_PRINT,
        self::GLYPHICON_CAMERA,
        self::GLYPHICON_FONT,
        self::GLYPHICON_BOLD,
        self::GLYPHICON_ITALIC,
        self::GLYPHICON_TEXT_HEIGHT,
        self::GLYPHICON_TEXT_WIDTH,
        self::GLYPHICON_ALIGN_LEFT,
        self::GLYPHICON_ALIGN_CENTER,
        self::GLYPHICON_ALIGN_RIGHT,
        self::GLYPHICON_ALIGN_JUSTIFY,
        self::GLYPHICON_LIST,
        self::GLYPHICON_INDENT_LEFT,
        self::GLYPHICON_INDENT_RIGHT,
        self::GLYPHICON_FACETIME_VIDEO,
        self::GLYPHICON_PICTURE,
        self::GLYPHICON_MAP_MARKER,
        self::GLYPHICON_ADJUST,
        self::GLYPHICON_TINT,
        self::GLYPHICON_EDIT,
        self::GLYPHICON_SHARE,
        self::GLYPHICON_CHECK,
        self::GLYPHICON_MOVE,
        self::GLYPHICON_STEP_BACKWARD,
        self::GLYPHICON_FAST_BACKWARD,
        self::GLYPHICON_BACKWARD,
        self::GLYPHICON_PLAY,
        self::GLYPHICON_PAUSE,
        self::GLYPHICON_STOP,
        self::GLYPHICON_FORWARD,
        self::GLYPHICON_FAST_FORWARD,
        self::GLYPHICON_STEP_FORWARD,
        self::GLYPHICON_EJECT,
        self::GLYPHICON_CHEVRON_LEFT,
        self::GLYPHICON_CHEVRON_RIGHT,
        self::GLYPHICON_PLUS_SIGN,
        self::GLYPHICON_MINUS_SIGN,
        self::GLYPHICON_REMOVE_SIGN,
        self::GLYPHICON_OK_SIGN,
        self::GLYPHICON_QUESTION_SIGN,
        self::GLYPHICON_INFO_SIGN,
        self::GLYPHICON_SCREENSHOT,
        self::GLYPHICON_REMOVE_CIRCLE,
        self::GLYPHICON_OK_CIRCLE,
        self::GLYPHICON_BAN_CIRCLE,
        self::GLYPHICON_ARROW_LEFT,
        self::GLYPHICON_ARROW_RIGHT,
        self::GLYPHICON_ARROW_UP,
        self::GLYPHICON_ARROW_DOWN,
        self::GLYPHICON_SHARE_ALT,
        self::GLYPHICON_RESIZE_FULL,
        self::GLYPHICON_RESIZE_SMALL,
        self::GLYPHICON_EXCLAMATION_SIGN,
        self::GLYPHICON_GIFT,
        self::GLYPHICON_LEAF,
        self::GLYPHICON_FIRE,
        self::GLYPHICON_EYE_OPEN,
        self::GLYPHICON_EYE_CLOSE,
        self::GLYPHICON_WARNING_SIGN,
        self::GLYPHICON_PLANE,
        self::GLYPHICON_CALENDAR,
        self::GLYPHICON_RANDOM,
        self::GLYPHICON_COMMENT,
        self::GLYPHICON_MAGNET,
        self::GLYPHICON_CHEVRON_UP,
        self::GLYPHICON_CHEVRON_DOWN,
        self::GLYPHICON_RETWEET,
        self::GLYPHICON_SHOPPING_CART,
        self::GLYPHICON_FOLDER_CLOSE,
        self::GLYPHICON_FOLDER_OPEN,
        self::GLYPHICON_RESIZE_VERTICAL,
        self::GLYPHICON_RESIZE_HORIZONTAL,
        self::GLYPHICON_HDD,
        self::GLYPHICON_BULLHORN,
        self::GLYPHICON_BELL,
        self::GLYPHICON_CERTIFICATE,
        self::GLYPHICON_THUMBS_UP,
        self::GLYPHICON_THUMBS_DOWN,
        self::GLYPHICON_HAND_RIGHT,
        self::GLYPHICON_HAND_LEFT,
        self::GLYPHICON_HAND_UP,
        self::GLYPHICON_HAND_DOWN,
        self::GLYPHICON_CIRCLE_ARROW_RIGHT,
        self::GLYPHICON_CIRCLE_ARROW_LEFT,
        self::GLYPHICON_CIRCLE_ARROW_UP,
        self::GLYPHICON_CIRCLE_ARROW_DOWN,
        self::GLYPHICON_GLOBE,
        self::GLYPHICON_WRENCH,
        self::GLYPHICON_TASKS,
        self::GLYPHICON_FILTER,
        self::GLYPHICON_BRIEFCASE,
        self::GLYPHICON_FULLSCREEN,
        self::GLYPHICON_DASHBOARD,
        self::GLYPHICON_PAPERCLIP,
        self::GLYPHICON_HEART_EMPTY,
        self::GLYPHICON_LINK,
        self::GLYPHICON_PHONE,
        self::GLYPHICON_PUSHPIN,
        self::GLYPHICON_USD,
        self::GLYPHICON_GBP,
        self::GLYPHICON_SORT,
        self::GLYPHICON_SORT_BY_ALPHABET,
        self::GLYPHICON_SORT_BY_ALPHABET_ALT,
        self::GLYPHICON_SORT_BY_ORDER,
        self::GLYPHICON_SORT_BY_ORDER_ALT,
        self::GLYPHICON_SORT_BY_ATTRIBUTES,
        self::GLYPHICON_SORT_BY_ATTRIBUTES_ALT,
        self::GLYPHICON_UNCHECKED,
        self::GLYPHICON_EXPAND,
        self::GLYPHICON_COLLAPSE_DOWN,
        self::GLYPHICON_COLLAPSE_UP,
        self::GLYPHICON_LOG_IN,
        self::GLYPHICON_FLASH,
        self::GLYPHICON_LOG_OUT,
        self::GLYPHICON_NEW_WINDOW,
        self::GLYPHICON_RECORD,
        self::GLYPHICON_SAVE,
        self::GLYPHICON_OPEN,
        self::GLYPHICON_SAVED,
        self::GLYPHICON_IMPORT,
        self::GLYPHICON_EXPORT,
        self::GLYPHICON_SEND,
        self::GLYPHICON_FLOPPY_DISK,
        self::GLYPHICON_FLOPPY_SAVED,
        self::GLYPHICON_FLOPPY_REMOVE,
        self::GLYPHICON_FLOPPY_SAVE,
        self::GLYPHICON_FLOPPY_OPEN,
        self::GLYPHICON_CREDIT_CARD,
        self::GLYPHICON_TRANSFER,
        self::GLYPHICON_CUTLERY,
        self::GLYPHICON_HEADER,
        self::GLYPHICON_COMPRESSED,
        self::GLYPHICON_EARPHONE,
        self::GLYPHICON_PHONE_ALT,
        self::GLYPHICON_TOWER,
        self::GLYPHICON_STATS,
        self::GLYPHICON_SD_VIDEO,
        self::GLYPHICON_HD_VIDEO,
        self::GLYPHICON_SUBTITLES,
        self::GLYPHICON_SOUND_STEREO,
        self::GLYPHICON_SOUND_DOLBY,
        self::GLYPHICON_SOUND_5_1,
        self::GLYPHICON_SOUND_6_1,
        self::GLYPHICON_SOUND_7_1,
        self::GLYPHICON_COPYRIGHT_MARK,
        self::GLYPHICON_REGISTRATION_MARK,
        self::GLYPHICON_CLOUD_DOWNLOAD,
        self::GLYPHICON_CLOUD_UPLOAD,
        self::GLYPHICON_TREE_CONIFER,
        self::GLYPHICON_TREE_DECIDUOUS,
        self::GLYPHICON_CD,
        self::GLYPHICON_SAVE_FILE,
        self::GLYPHICON_OPEN_FILE,
        self::GLYPHICON_LEVEL_UP,
        self::GLYPHICON_COPY,
        self::GLYPHICON_PASTE,
        self::GLYPHICON_ALERT,
        self::GLYPHICON_EQUALIZER,
        self::GLYPHICON_KING,
        self::GLYPHICON_QUEEN,
        self::GLYPHICON_PAWN,
        self::GLYPHICON_BISHOP,
        self::GLYPHICON_KNIGHT,
        self::GLYPHICON_BABY_FORMULA,
        self::GLYPHICON_TENT,
        self::GLYPHICON_BLACKBOARD,
        self::GLYPHICON_BED,
        self::GLYPHICON_APPLE,
        self::GLYPHICON_ERASE,
        self::GLYPHICON_HOURGLASS,
        self::GLYPHICON_LAMP,
        self::GLYPHICON_DUPLICATE,
        self::GLYPHICON_PIGGY_BANK,
        self::GLYPHICON_SCISSORS,
        self::GLYPHICON_BITCOIN,
        self::GLYPHICON_BTC,
        self::GLYPHICON_XBT,
        self::GLYPHICON_YEN,
        self::GLYPHICON_JPY,
        self::GLYPHICON_RUBLE,
        self::GLYPHICON_RUB,
        self::GLYPHICON_SCALE,
        self::GLYPHICON_ICE_LOLLY,
        self::GLYPHICON_ICE_LOLLY_TASTED,
        self::GLYPHICON_EDUCATION,
        self::GLYPHICON_OPTION_HORIZONTAL,
        self::GLYPHICON_OPTION_VERTICAL,
        self::GLYPHICON_MENU_HAMBURGER,
        self::GLYPHICON_MODAL_WINDOW,
        self::GLYPHICON_OIL,
        self::GLYPHICON_GRAIN,
        self::GLYPHICON_SUNGLASSES,
        self::GLYPHICON_TEXT_SIZE,
        self::GLYPHICON_TEXT_COLOR,
        self::GLYPHICON_TEXT_BACKGROUND,
        self::GLYPHICON_OBJECT_ALIGN_TOP,
        self::GLYPHICON_OBJECT_ALIGN_BOTTOM,
        self::GLYPHICON_OBJECT_ALIGN_HORIZONTAL,
        self::GLYPHICON_OBJECT_ALIGN_LEFT,
        self::GLYPHICON_OBJECT_ALIGN_VERTICAL,
        self::GLYPHICON_OBJECT_ALIGN_RIGHT,
        self::GLYPHICON_TRIANGLE_RIGHT,
        self::GLYPHICON_TRIANGLE_LEFT,
        self::GLYPHICON_TRIANGLE_BOTTOM,
        self::GLYPHICON_TRIANGLE_TOP,
        self::GLYPHICON_CONSOLE,
        self::GLYPHICON_SUPERSCRIPT,
        self::GLYPHICON_SUBSCRIPT,
        self::GLYPHICON_MENU_LEFT,
        self::GLYPHICON_MENU_RIGHT,
        self::GLYPHICON_MENU_DOWN,
        self::GLYPHICON_MENU_UP,
    ];
}