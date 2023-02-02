<?php

namespace frontend\components\form\controls;

use common\libs\form\components\Input as BaseInput;
use common\libs\StringHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Region
 * Контрол для поиска региона.
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Region extends BaseInput {

    const KEY_CITY_ID = 'cityID';
    const KEY_REGION_ID = 'regionID';

    /** @var int - id города. */
    private $city_id = 0;
    /** @var int - id области. */
    private $region_id = 0;

    /** @var null|BaseInput */
    private $input_control = null;

    public function beforeRender() {
        parent::beforeRender();
        $this->setType(self::TYPE_HIDDEN);
    }

    public function setValue($data) {

        // данные с $_POST ?
        if (StringHelper::isJson($data)) {
            $data = Json::decode($data);
        }

        // засетили в контрол данные самостоятельно.
        $this->city_id = ArrayHelper::getValue($data, self::KEY_CITY_ID, 0);
        $this->region_id = ArrayHelper::getValue($data, self::KEY_REGION_ID, 0);

        return parent::setValue(Json::encode([
            self::KEY_CITY_ID => $this->city_id,
            self::KEY_REGION_ID => $this->region_id,
        ]));
    }

    public function getValue() {
        return [
            self::KEY_CITY_ID => $this->city_id,
            self::KEY_REGION_ID => $this->region_id,
        ];
    }

    /**
     * @return BaseInput
     */
    private function getInputControl() {
        if (is_null($this->input_control)) {
            $this->input_control = (new BaseInput())
                ->setId('popup-geo-region')
                ->addClass('input-field');
        }
        return $this->input_control;
    }

    public function render(): string {
        $control = parent::render();
        return parent::renderTemplate([
            'hidden_control' => $control,
            'input_control' => $this->getInputControl()->render(),
        ]);
    }
}