<?php

namespace common\models;

/**
 * This is the model class for table "units".
 *
 * @property integer $id
 * @property integer $code
 * @property string $title
 * @property string $symbol_national
 * @property string $symbol_international
 * @property string $code_symbol_national
 * @property string $code_symbol_international
 * @property integer $is_mine
 */
class Unit extends Base {
    /** @see: \common\models\Unit::findByCodeCached */
    const CACHE_CODE_CACHED = 'cache_code_';

    public static function tableName() {
        return 'units';
    }

    public function rules() {
        return [
            [['code', 'is_mine'], 'integer'],
            [['title', 'symbol_national', 'symbol_international', 'code_symbol_national', 'code_symbol_international'], 'string', 'max' => 50],
            [['code'], 'unique'],
        ];
    }

    /**
     * Возвращает кешированную модель по коду.
     * @param int $code
     * @return null|static
     */
    public static function findByCodeCached(int $code) {
        $unit = \Yii::$app->cache->get(self::CACHE_CODE_CACHED. $code);
        if (false === $unit) {
            $unit = static::find()->where(['code' => $code])->one();
            \Yii::$app->cache->set(self::CACHE_CODE_CACHED. $code, $unit);
        }
        return $unit;
    }

    /**
     * @return bool
     */
    public function isMine() {
        return (boolean)$this->is_mine;
    }

    /**
     * Возвращает список основных единиц измерения.
     * @return Unit[]
     */
    public function getMineUnits() {
        return static::find()->where(['is_mine' => 1])->all();
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'code' => 'Код единицы измерения',
            'title' => 'Наименование единицы измерения',
            'symbol_national' => 'Условное обозначение: Национальное',
            'symbol_international' => 'Условное обозначение: Международное',
            'code_symbol_national' => 'Кодовое обозначение: Национальное',
            'code_symbol_international' => 'Кодовое обозначение: Международное',
            'is_mine' => 'Основная единица измерения ?',
        ];
    }
}