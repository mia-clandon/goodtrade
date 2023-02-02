<?php

namespace frontend\components\form\controls;

use yii\helpers\Json;

use common\libs\StringHelper;
use common\libs\form\components\Input as BaseInput;
use common\models\goods\Product;

/**
 * Class Delivery
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Delivery extends BaseInput {

    /** @var array */
    private $selected_delivery_terms = [];

    protected function beforeRender() {
        parent::beforeRender();
        $this->addClass('delivery-condition-field');
        $this->setId('delivery-condition');
        $this->setType(static::TYPE_HIDDEN);
    }

    public function setValue($value) {
        if (is_array($value)) {
            $this->selected_delivery_terms = $value;
            $value = Json::encode($value);
        }
        if (StringHelper::isJson($value)) {
            $this->selected_delivery_terms = array_filter(Json::decode($value), 'intval');
        }
        // значение для input hidden.
        return parent::setValue($value);
    }

    /**
     * Возвращает значение контрола в необходимом формате. Array.
     * @return array
     */
    public function getValue() {
        return $this->selected_delivery_terms;
    }

    /**
     * @return array
     */
    private function getDeliveryTerms() {
        return (new Product())->getDeliveryTermsHelper()->getAllDeliveryTerms();
    }

    public function render(): string {
        $control = parent::render();
        return $this->renderTemplate([
            'control' => $control,
            'delivery_terms' => $this->getDeliveryTerms(),
            'selected_delivery_terms' => $this->selected_delivery_terms,
        ]);
    }
}