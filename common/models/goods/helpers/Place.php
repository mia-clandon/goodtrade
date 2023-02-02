<?php

namespace common\models\goods\helpers;

use common\models\goods\Place as PlaceModel;

/**
 * Хелпер для работы с местами реализации.
 * Class Place
 * @author yerganat
 */
class Place extends Base {

    /** @var array */
    private $product_place_form_data = [];

    /**
     * Устанавливает данные мест реализации с формы товара.
     * @param array $place_data
     * @return $this
     */
    public function setData(array $place_data) {
        $this->product_place_form_data = $place_data;
        return $this;
    }
    
    /**
     * Обновление значений мест реализации.
     * @return bool
     */
    public function updatePlaceValues(): bool {

        if (empty($this->product_place_form_data)) {
            return true;
        }

        $product_id = $this->getProductModel()->id;
        if (!$this->getProductModel()->isNewRecord) {
            $this->clearPlaceValues();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($this->product_place_form_data as $key => $value) {

            $product_place = new PlaceModel();
            $product_place->product_id = (int)$product_id;
            $product_place->country_id = (int)$value['country_id'] ?? 0;
            $product_place->region_id = (int)$value['region_id'] ?? 0;
            $product_place->city_id = (int)$value['city_id'] ?? 0;

            $result = $product_place->save();

            if (!$result) {
                $transaction->rollBack();
                return false;
            }
        }
        $transaction->commit();
        return true;
    }

    /**
     * Очищает значения мест реализации.
     * @return $this
     */
    public function clearPlaceValues() {
        $product_model = $this->getProductModel();
        if ($product_model->isNewRecord) {
            return $this;
        }
        PlaceModel::deleteAll([
            'product_id' => $product_model->id,
        ]);
        return $this;
    }
}