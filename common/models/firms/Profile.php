<?php

namespace common\models\firms;

use yii\helpers\ArrayHelper;
use yii;

use common\libs\sphinx\Query;
use common\libs\sphinx\QueryBuilder;
use common\models\Base;

/**
 * This is the model class for table "firms_profiles".
 *
 * @property integer $id
 * @property integer $firm_id
 * @property integer $city_id
 * @property integer $bin
 * @property string $title
 * @property string $short_title
 * @property integer $oked
 * @property string $activity
 * @property integer $kato
 * @property string $locality
 * @property integer $krp
 * @property string $company_size
 * @property string $leader
 * @property string $legal_address
 * @property string $phone
 * @property string $email
 * @property string $site
 * @property string $is_parsed
 * @author Артём Широких kowapssupport@gmail.com
 */
class Profile extends Base {

    const TABLE_NAME = 'firms_profiles';

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['firm_id', 'city_id', 'bin', 'oked', 'kato', 'krp'], 'integer'],
            [['title'], 'required'],
            [['legal_address', 'phone', 'is_parsed', 'short_title'], 'string'],
            [['title', 'short_title', 'activity', 'locality', 'company_size', 'leader', 'email', 'site'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => '#',
            'firm_id' => 'Организация',
            'bin' => 'БИН',
            'title' => 'Наименование предприятия',
            'short_title' => 'Краткое название',
            'oked' => 'Общий классификатор видов экономической деятельности',
            'activity' => 'Вид деятельности предприятия',
            'kato' => 'Классификатор административно-территориальных объектов',
            'locality' => 'Населённый пункт',
            'krp' => 'Классификатор размерности предприятия',
            'company_size' => 'Размер организации',
            'leader' => 'Руководитель',
            'legal_address' => 'Юридический адресс',
            'phone' => 'Телефоны',
            'email' => 'Email адрес',
            'site' => 'Сайт',
            'is_parsed' => 'Is Parsed',
        ];
    }

    /**
     * Обновление записи в индексе.
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        Profile::updateSphinxIndex($this->id);
    }

    /**
     * TODO-F: рефакторинг.
     * @param $params
     * @return yii\data\ActiveDataProvider
     */
    public function search($params) {

        $query = self::find();
        $sphinx_match = array_intersect_key($params, $this->getSphinxCols());
        $sphinx_match = array_filter($sphinx_match);

        if ($sphinx_match) {
            $matches = (new Query())
                ->select(['id', new yii\db\Expression('weight() as weight')])
                ->from(Profile::tableName())
                ->setMatchTransliterate(true)
                ->andMatch($sphinx_match)
                ->orderBy('weight DESC')
                ->limit(1000)
                ->all(Yii::$app->get('sphinx'))
            ;
            $matches_id = ArrayHelper::getColumn($matches, 'id');
            $query->andWhere(['id' => $matches_id]);
        }

        $dataProvider = new yii\data\ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    /**
     * Краткое название организации.
     * @return string
     */
    protected function getTitle() {
        return (!empty($this->short_title)) ? $this->short_title : $this->title;
    }

    /**
     * Колонки используемые в firms_profiles rt index
     * [название колонки в индексе => название колонки в таблице]
     * @return array
     */
    protected function getSphinxCols() {
        return [
            'id' => 'id',
            'firm_id' => 'firm_id',
            'bin' => 'bin',
            'oked' => 'oked',
            'kato' => 'kato',
            'is_parsed' => 'is_parsed',
            'krp' => 'krp',
            'title' => 'title',
            'activity' => 'activity',
            'locality' => 'locality',
            'company_size' => 'company_size',
            'leader' => 'leader',
            'legal_address' => 'legal_address',
        ];
    }

    /**
     * Для индексации.
     * Расчитывает [offset => limit], в зависимости от количество обрабатываемых записей в партии.
     * @param $pack_count
     * @return array [offset => limit]
     */
    public static function calculateParts($pack_count) {
        $rows_count = intval(self::find()->count());
        // деление с округлением в большую сторону
        $parts_count = ceil($rows_count / $pack_count);
        // остаток от деления
        $balance = $rows_count % $pack_count;
        $out = [];
        $offset = 0;
        for ($i = 1; $i <= $parts_count; $i++) {
            $limit = $pack_count * $i;
            $out[] = [
                'offset' => $offset,
                'limit' => ($limit > $rows_count) ? $offset+$balance : $limit
            ];
            $offset = $pack_count * $i;
        }
        return $out;
    }

    /**
     * Индексация партии записей.
     * @param $offset
     * @param $limit
     */
    public static function indexPart($offset, $limit) {
        $rows = (new yii\db\Query())
            ->select('id')
            ->from(self::tableName())
            ->offset($offset)
            ->limit($limit)
            ->all()
        ;
        if ($rows) {
            foreach ($rows as $row) {
                $id = ArrayHelper::getValue($row, 'id', 0);
                self::updateSphinxIndex($id);
            }
        }
    }

    /**
     * TODO-F: рефакторинг.
     * @param int $id - идентификатор записи.
     * @return int
     * @throws yii\db\Exception
     */
    public static function updateSphinxIndex($id) {

        ini_set('memory_limit', '2048M');

        $sphinx_cols = self::getSphinxCols();

        // дабы уменьшить количество выделяемой памяти не юзав activeRecord объект.
        $row = (new yii\db\Query())
            ->select(array_values($sphinx_cols))
            ->from(self::tableName())
            ->where(['id' => (int)$id])
            ->one()
        ;

        if ($row) {

            $insert_to_index_data = [];

            foreach ($sphinx_cols as $sphinx_col => $row_col) {
                //TODO-f: похоже на костыль, нужно будет поправить гадость.
                if ($sphinx_col == 'is_parsed') {
                    $insert_to_index_data[$sphinx_col] = $row[$row_col] == 'yes' ? 1 : 0;
                }
                else {
                    $insert_to_index_data[$sphinx_col] = $row[$row_col];
                }
                if (is_null($insert_to_index_data[$sphinx_col])) {
                    $insert_to_index_data[$sphinx_col] = '';
                }
            }
            $builder = new QueryBuilder(\Yii::$app->get('sphinx'));
            return $builder->sphinxSave(self::tableName(), $id, $insert_to_index_data);
        }
        else {
            return false;
        }
    }

    /**
     * Метод очищает индекс.
     */
    public static function clearIndex() {
        /** @var \yii\db\Connection $sphinx */
        $sphinx = Yii::$app->get('sphinx');
        $sphinx->createCommand('TRUNCATE RTINDEX '.static::TABLE_NAME)
            ->execute();
    }

    /**
     * Переданная строка - бин?
     * @param $string
     * @return bool
     */
    public function isBin($string) {
        if (is_numeric($string) && strlen($string) == 12) {
            return true;
        }
        return false;
    }
}