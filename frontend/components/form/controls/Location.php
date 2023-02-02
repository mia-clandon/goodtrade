<?php

namespace frontend\components\form\controls;

use common\libs\StringHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\libs\form\components\Input as BaseInput;
use common\models\Location as LocationModel;

/**
 * Class Location
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Location extends BaseInput {

    const LOCATION_DATA_KEY = 'location';

    const KEY_CITY_ID = 'cityID';
    const KEY_REGION_ID = 'regionID';
    const KEY_CITY_TEXT = 'city_text';

    private $old_name = null;

    /** @var string - строковое название города/региона. */
    private $control_text = '';
    /** @var int - id города. */
    private $city_id = 0;
    /** @var int - id области. */
    private $region_id = 0;

    protected function beforeRender() {
        parent::beforeRender();
        $this->setType(self::TYPE_HIDDEN);
        $this->old_name = $this->getName();
        $this->setName($this->getName(). '['.self::LOCATION_DATA_KEY.']');
    }

    public function setValue($data) {

        // данные с $_POST ?
        if (isset($data[self::LOCATION_DATA_KEY]) &&
            StringHelper::isJson(ArrayHelper::getValue($data, self::LOCATION_DATA_KEY))
        ) {
            $data = ArrayHelper::getValue($data, self::LOCATION_DATA_KEY);
            $data = Json::decode($data);
        }

        // засетили в контрол данные самостоятельно.
        $city_id = ArrayHelper::getValue($data, self::KEY_CITY_ID, 0);
        $region_id = ArrayHelper::getValue($data, self::KEY_REGION_ID, 0);

        $this->control_text = $this->getFormattedInputText($city_id, $region_id);

        return parent::setValue(Json::encode([
            self::KEY_CITY_ID => $city_id,
            self::KEY_REGION_ID => $region_id,
        ]));
    }

    /**
     * Метод возвращает в форматированном виде {city_id}, {region_id} строку.
     * @param integer $city_id
     * @param integer $region_id
     * @return string
     */
    private function getFormattedInputText($city_id, $region_id) {
        return (new LocationModel())->getFormattedLocationText($city_id, $region_id);
    }

    /**
     * Возврат значения в формате массива. ([self::KEY_CITY_ID => n, self::KEY_REGION_ID => n])
     * @return array
     */
    public function getValue() {
        $value = Json::decode(parent::getValue());
        return [
            self::KEY_CITY_ID => (int)ArrayHelper::getValue($value, self::KEY_CITY_ID, 0),
            self::KEY_REGION_ID => (int)ArrayHelper::getValue($value, self::KEY_REGION_ID, 0),
        ];
    }

    public function render(): string {
        $control = parent::render();
        return parent::renderTemplate([
            'control' => $control,
            'name' => $this->old_name,
            'city_value' => $this->control_text,
        ]);
    }

    /**
     * @return string
     */
    public function getControlText() {
        return (string)$this->control_text;
    }

    /**
     * @return int
     */
    public function getCityId() {
        return (int)$this->city_id;
    }

    /**
     * @return int
     */
    public function getRegionId() {
        return (int)$this->region_id;
    }
}