<?php

namespace common\models;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\libs\sphinx\QueryBuilder;

/**
 * This is the model class for table "oked".
 *
 * @property integer $id
 * @property integer $key
 * @property string $name
 * @author Артём Широких kowapssupport@gmail.com
 */
class Oked extends Base {

    public static function tableName() {
        return 'oked';
    }

    public function rules() {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'key' => 'Код ОКЕД',
            'name' => 'Строковое название',
        ];
    }

    /**
     * Колонки используемые в oked rt index
     * [название колонки в индексе => название колонки в таблице]
     * @return array
     */
    private static function getSphinxCols() {
        return [
            'id'    => 'id',
            'key'   => 'key',
            'title' => 'name',
        ];
    }

    /**
     * Обновление записи в индексе.
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        static::updateSphinxIndex($this->id);
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = self::find();
        $from = intval(ArrayHelper::getValue($params, 'from', 0));
        $to = intval(ArrayHelper::getValue($params, 'to', 0));
        if ($from) {
            $query->andWhere(['>=', 'key', $from]);
        }
        if ($to) {
            $query->andWhere(['<=', 'key', $to]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['key' => SORT_ASC]]
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    /**
     * @return int
     */
    public function getMax() {
        return $this->getMin() + 1;
    }

    /**
     * @return int
     */
    public function getMin() {
        return (int)static::find()->select(['key'])->min('`key`');
    }

    /**
     * Обновление записей в sphinx index.
     */
    public static function indexAll() {
        /** @var Oked[] $records */
        $records = self::find()->all();
        foreach ($records as $record) {
            self::updateSphinxIndex($record->id);
        }
    }

    /**
     * @param integer $id
     * @return bool|int
     */
    public static function updateSphinxIndex($id) {
        $sphinx_cols = self::getSphinxCols();

        /** @var Location $row */
        if ($row = self::findOne($id)) {
            $insert_to_index_data = [];

            foreach ($sphinx_cols as $sphinx_col => $row_col) {
                $insert_to_index_data[$sphinx_col] = $row[$row_col];

                if (is_null($insert_to_index_data[$sphinx_col])) {
                    $insert_to_index_data[$sphinx_col] = '';
                }
            }

            $builder = new QueryBuilder(\Yii::$app->get('sphinx'));
            return $builder->sphinxSave(self::tableName(), $id, $insert_to_index_data);
        }
        return false;
    }
}