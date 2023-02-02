<?php

namespace common\models\goods;

use common\models\Base;

/**
 * This is the model class for table "product_places".
 *
 * @property int $id
 * @property int $product_id
 * @property integer $country_id
 * @property integer $region_id
 * @property integer $city_id
 * @author Артём Широких kowapssupport@gmail.com
 */
class Place extends Base {

    const TABLE_NAME = 'product_places';

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['product_id'], 'required'],
            [['product_id', 'country_id', 'region_id', 'city_id'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'id'            => '#',
            'product_id'    => 'Product ID',
            'country_id'        => 'Страна',
            'region_id'         => 'Область',
            'city_id'           => 'Город',
        ];
    }
}