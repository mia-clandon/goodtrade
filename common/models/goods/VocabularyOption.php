<?php

namespace common\models\goods;

use yii\behaviors\TimestampBehavior;
use common\models\Base;

/**
 * Class VocabularyTerm
 * @property int $id
 * @property int $product_id
 * @property int $vocabulary_id
 * @property int $position
 * @property int $option_id
 * @property string $value
 * @property int $created_at
 * @property int $updated_at
 * @package common\models\goods
 * @author Артём Широких kowapssupport@gmail.com
 */
class VocabularyOption extends Base {

    const TABLE_NAME = 'product_vocabulary_option';

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules() {
        return [
            [['product_id', 'vocabulary_id', 'option_id', 'created_at', 'updated_at', 'position'], 'integer'],
            [['product_id', 'vocabulary_id',], 'required'],
            [['value'], 'string', 'max' => 255],
        ];
    }

    /*
    public static function getValue($product_id, $vocabulary_id) {
        //todo.
        $result = static::find()
            ->select(['value'])
            ->where([
                'product_id' => (int)$product_id,
                'vocabulary_id' => (int)$vocabulary_id,
            ])
        ;
    }
    */
}