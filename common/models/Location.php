<?php /** @noinspection DuplicatedCode */

namespace common\models;

use common\libs\manticore\Client;
use yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property string $title
 * @property int $region
 * @author Артём Широких kowapssupport@gmail.com
 */
class Location extends Base
{
    public const COUNTRY_KAZAKHSTAN = 1;

    /** кеш ключ всех городов. */
    public const ALL_CITIES_CACHE_KEY = 'all_cities_cache_key';

    public static function tableName(): string
    {
        return 'location';
    }

    public static function indexName(): string
    {
        return 'locations';
    }

    //Список областей.
    public function getPossibleRegions(): array
    {
        return [
            'Восточно-Казахстанская область' => 1,
            'Южно-Казахстанская область' => 2,
            'Карагандинская область' => 3,
            'Алматинская область' => 4,
            'Акмолинская область' => 5,
            'Жамбылская  область' => 6,
            'Кызылординская область' => 7,
            'Западно-Казахстанская область' => 8,
            'Кустанайская область' => 9,
            'Павлодарская область' => 10,
            'Северо-Казахстанская область' => 11,
            'Атырауская область' => 12,
            'Актюбинская область' => 13,
            'Мангистауская область' => 14,
        ];
    }

    //Список стран.
    public function getPossibleCountries(): array
    {
        return [
            'Казахстан' => self::COUNTRY_KAZAKHSTAN,
        ];
    }

    //Получение списка городов.
    public function getCitiesArray(): array
    {
        if (!$cities = Yii::$app->cache->get(self::ALL_CITIES_CACHE_KEY)) {
            $city_list = static::find()->select(['id', 'title'])->all();
            $cities = [];
            /** @var static $city */
            foreach ($city_list as $city) {
                $cities[$city->id] = $city->title;
            }
            Yii::$app->cache->set(self::ALL_CITIES_CACHE_KEY, $cities);
        }
        return $cities;
    }

    public function beforeSave($insert): bool
    {
        if ($result = parent::beforeSave($insert)) {
            $this->clearCitiesCache();
        }
        return $result;
    }

    public function beforeDelete(): bool
    {
        if ($result = parent::beforeDelete()) {
            $this->clearCitiesCache();
        }
        return $result;
    }

    //Очистка кеша списка городов проекта.
    public function clearCitiesCache(): self
    {
        Yii::$app->cache->set(self::ALL_CITIES_CACHE_KEY, null);
        return $this;
    }

    //Метод возвращает в форматированном виде {city_id}, {region_id} строку.
    public function getFormattedLocationText(int $city_id, int $region_id): string
    {
        $out = '';
        if ($city_id) {
            $city_name = $this->getCityNameById($city_id);
            if ($city_name) {
                $out = $city_name;
            }
        }
        if ($region_id) {
            $region_name = $this->getRegionNameById($region_id);
            if (!empty($region_name) && !empty($out)) {
                $out .= ', ' . $region_name;
            } else if (!empty($region_name)) {
                $out = $region_name;
            }
        }
        return $out;
    }

    public function getCityNameById(int $id): string
    {
        $location = self::findOne($id);
        if ($location) {
            return $location->title;
        }
        return '';
    }

    public function getCountryNameById(int $id): string
    {
        $country_map = $this->getPossibleCountries();
        $country_map = array_flip($country_map);
        if (isset($country_map[$id])) {
            return $country_map[$id];
        }
        return '';
    }

    public function getRegionNameById(int $id): string
    {
        $regions_map = $this->getPossibleRegions();
        $regions_map = array_flip($regions_map);
        if (isset($regions_map[$id])) {
            return $regions_map[$id];
        }
        return '';
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = self::find();

        $title = arr_get_val($params, 'title', '');
        $region = arr_get_val($params, 'region', 0);

        if (!empty($title)) {
            $result = (new Client())->search(self::indexName(), "*{$title}*");
            if ($result->getTotal()) {
                $query->andWhere(['id' => $result->getIds()]);
            }
        }
        if ($region) {
            $query->andWhere(['region' => $region]);
        }
        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $data_provider;
        }
        return $data_provider;
    }

    public function rules(): array
    {
        return [
            [['title', 'region'], 'required'],
            [['region'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => '#',
            'title' => 'Название города',
            'region' => 'Идентификатор области',
        ];
    }

    //Обновление записи в индексе.
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        static::updateIndex($this->id);
    }

    public function afterDelete(): void
    {
        parent::afterDelete();
        (new Client())->deleteRow(self::indexName(), $this->id);
    }

    //Обновление записей в manticore index.
    public static function indexAll(): void
    {
        /** @var Location[] $records */
        $records = self::find()->all();
        foreach ($records as $record) {
            self::updateIndex($record->id);
        }
    }

    public static function updateIndex(int $id): bool
    {
        /** @var Location $row */
        if ($row = self::findOne($id)) {
            $index_row = [
                'id' => (int)$row->id,
                'm_id' => (int)$row->id,
                'title' => (string)$row->title,
                'region' => (int)$row->region,
            ];
            return (new Client())->saveRow(self::indexName(), $index_row);
        }
        return false;
    }
}
