<?php

namespace common\models\goods;

use common\models\Base;

/**
 * This is the model class for table "product_delivery_terms".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $delivery_term_id
 * @author Артём Широких kowapssupport@gmail.com
 */
class DeliveryTerms extends Base {

    const TABLE_NAME = 'product_delivery_terms';

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['product_id', 'delivery_term_id'], 'required'],
            [['product_id', 'delivery_term_id'], 'integer'],
            [['product_id', 'delivery_term_id'], 'unique', 'targetAttribute' => ['product_id', 'delivery_term_id'], 'message' => 'The combination of Product ID and Delivery Term ID has already been taken.'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'product_id' => 'Product ID',
            'delivery_term_id' => 'Delivery Term ID',
        ];
    }
}