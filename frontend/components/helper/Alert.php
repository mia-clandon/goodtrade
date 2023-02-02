<?

namespace frontend\components\helper;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\libs\StringHelper;

/**
 * Class Alert
 * @package frontend\components\helper
 * @author Артём Широких kowapssupport@gmail.com
 */
class Alert {

    private const COOKIE_KEY = 'flash_messages';

    #region - типы сообщений.
    const MESSAGE_SUCCESS = 1;
    const MESSAGE_ERROR = 2;

    /**
     * @return array
     */
    public static function getTypes(): array {
        return [
            self::MESSAGE_SUCCESS => 'success',
            self::MESSAGE_ERROR => 'error',
        ];
    }

    /**
     * @param string $message
     * @param int $type
     * @throws Exception
     */
    public static function setMessage(string $message, int $type = self::MESSAGE_SUCCESS) {
        if (!array_key_exists($type, static::getTypes())) {
            throw new Exception('Undefined type.');
        }
        $session = \Yii::$app->session;
        $session->open();
        $session->setFlash(self::COOKIE_KEY, Json::encode([
            'message' => $message,
            'type' => $type,
        ]));
        $session->close();
    }

    /**
     * Регистрирует FlashMessage на странице.
     * @return bool|null
     */
    public static function register(): ?bool {
        $session = \Yii::$app->session;
        $flash_data = $session->getFlash(self::COOKIE_KEY);
        if (empty($flash_data)) {
            return null;
        }
        if (!StringHelper::isJson($flash_data)) {
            return null;
        }
        $flash_data = Json::decode($flash_data);
        $message = ArrayHelper::getValue($flash_data, 'message', '');
        $type = ArrayHelper::getValue($flash_data, 'type', 0);
        $script = '$.notify("'.$message.'", "'.self::getTypes()[$type].'");';
        \Yii::$app->getView()->registerJs($script);
        return true;
    }
}