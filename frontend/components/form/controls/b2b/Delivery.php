<?

namespace frontend\components\form\controls\b2b;

use common\libs\form\components\Control;
use common\models\goods\Product;

/**
 * Контрол выбора условий доставки товара.
 * Class Delivery
 * @package frontend\components\form\controls\b2b
 */
class Delivery extends Control {

    protected $template_name = 'b2b/delivery';

    /**
     * @return array
     */
    private function getDeliveryTerms(): array {
        return (new Product())
            ->getDeliveryTermsHelper()
            ->getAllDeliveryTerms();
    }

    /**
     * Возвращает массив выбранных условий доставки товара.
     * @return array
     */
    public function getValue() {
        $value = parent::getValue();
        if (null === $value) {
            return [];
        }
        if (!is_array($value)) {
            return [$value];
        }
        return array_keys($value);
    }

    public function render(): string {
        return $this->renderTemplate([
            'all_delivery_terms' => $this->getDeliveryTerms(),
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'value' => $this->getValue(),
        ]);
    }
}