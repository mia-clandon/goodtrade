<?

namespace frontend\components\widgets;

use common\models\goods\Product;

use yii\base\Widget;

/**
 * Class CommercialRequest
 * Виджет для отправки коммерческого запроса на товар.
 * @package frontend\components\widgets
 * @author Артём Широких kowapssupport@gmail.com
 */
class CommercialRequest extends Widget {

    public const MODAL_NEW = 1;
    public const MODAL_OLD = 2;

    /** скрытое модальное окно для формы коммерческого запроса. */
    public const REQUEST_TYPE_MODAL = 0;

    /** строка над футером с коммерческим запросом. */
    public const REQUEST_TYPE_BOTTOM = 1;

    /** @var int тип виджета. */
    public $type = self::REQUEST_TYPE_MODAL;

    /** @var int версия модального окна(для новой вёрстки/старой вёрстки) */
    public $version = self::MODAL_OLD;

    /** @var null|Product */
    public $product = null;

    /**
     * @return bool
     */
    private function isGuest() {
        return (bool)\Yii::$app->user->isGuest;
    }

    public function run() {
        // нижняя полоска с коммерческим запросом.
        if ($this->type == self::REQUEST_TYPE_BOTTOM) {
            return $this->getBottomRequest();
        }
        // рендеринг модального окна коммерческого запроса.
        else if ($this->type == self::REQUEST_TYPE_MODAL) {
            return $this->getRequestModal();
        }
        else {
            return '';
        }
    }

    /**
     * Модальное окно для формы коммерческого запроса.
     * @return string
     */
    private function getRequestModal() {
        $view_file_name = $this->version === self::MODAL_OLD
            ? 'modal-request'
            : 'modal-request-b2b';
        return $this->render('commercial/'. $view_file_name);
    }

    /**
     * Метод рендерит нижнюю полоску с кнопкой отправки коммерческого запроса.
     * @return string
     */
    private function getBottomRequest() {
        if (!$this->isGuest() && !is_null($this->product)) {
            return $this->render('commercial/bottom-request', [
                'product' => $this->product,
            ]);
        }
        else {
            return '';
        }
    }
}