<?php

namespace common\models\goods;

use common\libs\Declension;
use common\libs\RegionHelper;
use common\libs\sphinx\QueryBuilder;
use common\libs\Storage;
use common\models\Base;
use common\models\Category;
use common\models\Chrono;
use common\models\commercial\Request as CommercialRequest;
use common\models\firms\Firm;
use common\models\goods\Categories as ProductCategory;
use common\models\goods\helpers\DeliveryTerms as DeliveryTermsHelper;
use common\models\goods\helpers\Place as PlaceHelper;
use common\models\goods\helpers\TitleGenerator as TitleGeneratorHelper;
use common\models\goods\helpers\Vocabulary as VocabularyHelper;
use common\models\goods\search\Product as ProductSearch;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\log\Logger;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property string $title
 * @property integer $status
 * @property integer $mark_type
 * @property integer $user_id
 * @property integer $firm_id
 * @property string $price
 * @property integer $unit
 * @property bool $price_with_vat
 * @property string $text
 * @property integer $capacities_from
 * @property integer $capacities_to
 * @property integer $from_import
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @method static findOne($condition)
 * @method static[] findAll($condition)
 *
 * @author Артём Широких kowapssupport@gmail.com
 */
class Product extends Base {

    const TABLE_NAME = 'product';

    #region - статусы товара;
    // товар на модерации.
    const STATUS_PRODUCT_MODERATION = 0;
    // товар активен.
    const STATUS_PRODUCT_ACTIVE = 1;
    #endregion;

    #region - типы выделения товара;
    // товар без выделения.
    const MARK_TYPE_PRODUCT_NO = 0;
    // товар выделен маленьким.
    const MARK_TYPE_PRODUCT_SMALL = 1;
    // товар выделен большим.
    const MARK_TYPE_PRODUCT_BIG = 2;
    #endregion;

    const CACHE_PRODUCT_ID = 'cache_product_id_';
    // идентификаторы категорий товара.
    const CACHE_PRODUCT_CATEGORY_IDS = 'cache_product_category_ids_';

    /** @var array Категории товара. */
    public $categories;

    /** @var array */
    public $images = [];

    /** @var null|VocabularyHelper */
    private $vocabulary_helper;

    /** @var null|PlaceHelper */
    private $place_helper = NULL;

    /** @var null|DeliveryTermsHelper */
    private $delivery_terms_helper;

    /** @var null|TitleGeneratorHelper */
    private $title_generator_helper;

    /** @var array Единицы измерения */
    private $units_allowed = [
        1 => 'штука',
        2 => 'килограмм',
        3 => 'тонна',
    ];

    /** @var array Единицы измерения (склонения) */
    private $unit_decline = [
        1 => ['штука', 'штуки', 'штук'],
        2 => ['килограмм', 'килограмма', 'килограмм'],
        3 => ['тонна', 'тонны', 'тонн'],
    ];

    /** @var array Единицы измерения (склонения, дательный падеж) */
    private $unit_dative = [
        1 => 'штуку',
        2 => 'килограмм',
        3 => 'тонну',
    ];

    /** @var array Единицы измерения (краткие) */
    private $unit_short = [
        1 => 'шт',
        2 => 'кг',
        3 => 'тн',
    ];

    protected bool $need_call_after_save = true;
    protected bool $need_call_before_save = true;

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Возвращает форматированное название товара в зависимости от характеристик категории.
     * @return string
     */
    public function getTitle(): string {
        if (null === $this->title || empty($this->title)) {
            return $this->getTitleGeneratorHelper()->getGeneratedTitle();
        }
        return $this->title;
    }

    public function rules() {
        return [
            [
                ['status', 'mark_type', 'unit', 'user_id', 'firm_id', 'price_with_vat', 'created_at', 'updated_at', 'capacities_from', 'capacities_to', 'from_import']
                , 'integer'
            ],
            [['price'], 'number', 'when' => function($model) {
                /** @var static $model */
                return !empty($model->price);
            }],
            [['text'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['status'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function getStatuses() {
        return [
            self::STATUS_PRODUCT_MODERATION => 'На модерации',
            self::STATUS_PRODUCT_ACTIVE => 'Активен',
        ];
    }

    /**
     * Статус товара.
     * @return string
     */
    public function getCurrentStatusText() {
        $statuses = $this->getStatuses();
        if (array_key_exists($this->status, $statuses)) {
            return $statuses[$this->status];
        }
        return '';
    }

    /**
     * @return array
     */
    public static function getMarks(): array {
        return [
            self::MARK_TYPE_PRODUCT_NO => 'Без выделения',
            self::MARK_TYPE_PRODUCT_SMALL => 'Маленкий (один простой товар)',
            self::MARK_TYPE_PRODUCT_BIG => 'Большой (два простых товара)',
        ];
    }

    /**
     * Получение категорий товара.
     * @return \yii\db\ActiveQuery
     */
    public function getCategories() {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable(ProductCategory::tableName(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirm() {
        return $this->hasOne(Firm::class, ['id' => 'firm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages() {
        return $this->hasMany(Images::class, ['product_id' => 'id']);
    }

    /**
     * @return Images|ActiveRecord
     */
    public function getMainImage() {
        /** @var Images $image */
        return $this->getImages()->orderBy('position')->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaces() {
        return $this->hasMany(Place::class, ['product_id' => 'id']);
    }

    /**
     * @return string|null
     */
    public function getLocation() {
        /**
         * @var Place $place
         */
        $place = $this->getPlaces()->one();
        if(null!== $place) {
            $region_helper = RegionHelper::i()
                ->setCountryId($place->country_id)
                ->setCityId($place->city_id)
                ->setRegionId($place->region_id);
            return $region_helper->get();
        }

        return null;
    }

    /**
     * @param $id
     * @return null|static
     */
    public static function getByIdCached($id) {
        $id = intval($id);
        if (!$id) {
            return null;
        }
        $key = self::CACHE_PRODUCT_ID. $id;
        if (!$product = Yii::$app->cache->get($key)) {
            $product = static::findOne($id);
            Yii::$app->cache->set($key, $product);
        }
        return $product;
    }

    /**
     * @param string $comma
     * @return string
     */
    public function getFormattedPrice($comma = '.') {
        return number_format($this->price, 0, '.', $comma);
    }

    /**
     * @return int
     */
    public function getPrice() {
        return (int)$this->price;
    }

    public function beforeSave($insert) {
        if ($this->need_call_before_save) {
            if ($insert === false) {

                // не даю менять категорию товара.
                if (null !== $this->categories) {
                    $category_id_from_form = (int)ArrayHelper::getValue($this->categories, 0, 0);
                    $current_category_id = $this->getCategoryId();
                    $vocabulary_values = $this->getVocabularyHelper()->getValues();
                    if ($current_category_id !== $category_id_from_form && count($vocabulary_values) > 0) {
                        $this->addError('category_id', 'Временно не даю возможность менять категорию товара.');
                        return false;
                    }
                }

            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * Обновление связей + Обновление записи в индексе.
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        if ($this->need_call_after_save) {

            $this->getDeliveryTermsHelper()->updateDeliveryTerms();
            $this->getVocabularyHelper()->updateVocabularyValues();
            $this->getPlaceHelper()->updatePlaceValues();

            $this->updateImages();
            $this->updateCategories();

            //if ($this->title === null || empty($this->title)) {
            $this->title = $this->getTitleGeneratorHelper()->getGeneratedTitle();
            $this->need_call_after_save = false;
            $this->need_call_before_save = false;
            $this->save();
            //}

            if($this->firm_id) {
                Chrono::log(Chrono::TYPE_PRODUCT, $this->firm_id, 'Обновление товара: ' . $this->getTitle());
            }

            $this->clearCache();
            $this->updateSphinxIndex();
        }
    }

    /**
     * Обновление фотографий.
     * @return bool
     */
    public function updateImages() {
        $images = (array)$this->images;
        if (empty($images)) {
            return true;
        }

        $max_position = $this->getImages()->max('position');
        $uploaded_files = Storage::i()->moveFilesToStorage($images, self::TABLE_NAME);
        foreach ($uploaded_files as $file) {
            $image = new Images();
            $image->product_id = $this->id;
            $image->image = $file;
            $image->position = ++$max_position;
            $image->save();
        }
        return true;
    }

    /**
     * Обновление категорий товара. (возвращает ошибки.)
     * @param boolean $remove_not_exist
     * @return array
     */
    public function updateCategories($remove_not_exist = true) {
        $categories = (array)$this->categories;
        if ($remove_not_exist && !is_null($this->categories)) {
            // удаляю связи которые есть в базе но нет в $items;
            $current_firm_categories = ProductCategory::find()->where(['product_id' => $this->id])
                ->select(['category_id'])
                ->asArray()
                ->all();
            $current_firm_categories = ArrayHelper::getColumn($current_firm_categories, 'category_id');
            $for_remove_relations = array_diff($current_firm_categories, $categories);
            ProductCategory::deleteAll(['product_id' => $this->id, 'category_id' => $for_remove_relations]);
        }
        $errors = [];
        foreach ($categories as $category_id) {

            $product_category = ProductCategory::findOne(['product_id' => $this->id, 'category_id'=> $category_id]);
            if (null === $product_category) {
                $product_category = new ProductCategory();
                $product_category->product_id = (int)$this->id;
                $product_category->category_id = (int)$category_id;
                if ((int)$this->firm_id) {
                    $product_category->firm_id = (int)$this->firm_id;
                }
            } else  {
                $product_category->firm_id = ((int)$this->firm_id)
                    ? (int)$this->firm_id
                    : null;
            }
            $result = $product_category->save();

            if (!$result) {
                $errors = ArrayHelper::merge($product_category->getErrors(), $errors);
            }
        }
        return $errors;
    }

    /**
     * @param null|int $firm_id
     * @param bool $only_active_products
     * @return ActiveQuery
     */
    public static function getProductCategoryQueryByFirm($firm_id = null, bool $only_active_products = false): ActiveQuery {
        $firm_id = $firm_id ?? Firm::get()->id;
        $category_id_list = ProductCategory::find()
            ->alias('pc')
            ->select(['category_id'])
            ->where(['pc.firm_id' => $firm_id])
            ->groupBy(['pc.category_id']);
        if ($only_active_products) {
            $category_id_list->innerJoin(self::TABLE_NAME.' p', 'pc.product_id = p.id');
            $category_id_list->andWhere(['p.status' => static::STATUS_PRODUCT_ACTIVE]);
        }
        $category_list = ArrayHelper::getColumn($category_id_list->asArray()->all(), 'category_id', []);
        return Category::find()->where(['id' => $category_list]);
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Базовая цена ?.
     * @return bool
     */
    public function isPriceWitVAT() {
        return (bool)$this->price_with_vat;
    }

    /**
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id) {
        $this->user_id = (int)$user_id;
        return $this;
    }

    /**
     * @param integer $firm_id
     * @return $this
     */
    public function setFirmId($firm_id) {
        $this->firm_id = (int)$firm_id;
        return $this;
    }

    /**
     * Установка статуса товару.
     * @param int $status
     * @return $this
     */
    public function setStatus($status) {
        $status = (int)$status;
        if (array_key_exists($status, $this->getStatuses())) {
            $this->status = $status;
        }
        return $this;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice($price) {
        $this->price = floatval($price);
        return $this;
    }

    /**
     * @param integer $unit_id
     * @return $this
     */
    public function setUnitId($unit_id) {
        if (array_key_exists($unit_id, $this->getAllUnits())) {
            $this->unit = (int)$unit_id;
        }
        return $this;
    }

    /**
     * @param int $from
     * @param int $to
     * @return $this
     */
    public function setCapacity(int $from = 0, int $to = 0) {
        $this->capacities_from = (int)$from;
        $this->capacities_to = (int)$to;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllUnits() {
        return $this->units_allowed;
    }

    /**
     * @param int $count
     * @param bool $use_short
     * @return string
     */
    public function getUnitText($count = null, $use_short = false) {
        if ($this->unit && isset($this->units_allowed[$this->unit])) {
            if (is_null($count)) {
                if ($use_short) {
                    return $this->unit_short[$this->unit];
                }
                return $this->units_allowed[$this->unit];
            } else {
                return Declension::number((int)$count,
                    $this->unit_decline[$this->unit][0],
                    $this->unit_decline[$this->unit][1],
                    $this->unit_decline[$this->unit][2]
                );
            }
        }
        return '';
    }

    /**
     * @param string $prefix
     * @return string
     */
    public function getUnitDativeText(string $prefix): string {
        if ($this->unit && isset($this->unit_dative[$this->unit])) {
            return $prefix.' '.$this->unit_dative[$this->unit];
        }
        return '';
    }

    /**
     * Возвращает идентификаторы категорий товара.
     * @return array
     */
    public function getCategoryIds() {
        if ($this->isNewRecord) {
            return [];
        }
        $cache_key = $this->getProductCategoryCacheKey();
        $category_ids = Yii::$app->cache->get($cache_key);
        if (!$category_ids) {
            $categories = $this->getCategories()
                ->select('id')
                ->asArray()
                ->all();
            $category_ids = ArrayHelper::getColumn($categories, 'id', []);
            Yii::$app->cache->set($cache_key, $category_ids);
        }
        return $category_ids;
    }

    /**
     * @return string
     */
    private function getProductCategoryCacheKey() {
        return self::CACHE_PRODUCT_CATEGORY_IDS. (int)$this->id;
    }

    /**
     * Т.к. в данный момент у товара 1 категория.
     * @return int|null
     */
    public function getCategoryId() {
        $category_id = ArrayHelper::getValue($this->getCategoryIds(), 0, null);
        return (null !== $category_id) ? (int)$category_id : null;
    }

    /**
     * Метод проверяет, мой ли товар.
     * @return bool
     */
    public function isMine() {
        if (\Yii::$app->user->isGuest) {
            return false;
        }
        return $this->firm_id == Firm::get()->id;
    }

    /**
     * Метод проверят отправлял ли я коммерческий запрос на товар.
     * @return bool
     */
    public function hasMineCommercialRequest() {
        if (\Yii::$app->user->isGuest) {
            return false;
        }
        return CommercialRequest::find()
                ->where([
                    'from_firm_id'  => Firm::get()->id,
                    'product_id'    => $this->id,
                ])
                ->andWhere(['!=', 'status', CommercialRequest::STATUS_DELETED])
                ->count() > 0;
    }

    /**
     * todo: всё что связанно с коммерческими запросами вынести в хелпер.
     * Метод возвращает срок действия коммерческого запроса на товар.
     * @param int|null $firm_id
     * @return integer
     */
    public function getCommercialRequestValidity(int $firm_id = null): ?int {
        if (\Yii::$app->user->isGuest) {
            return false;
        }
        return CommercialRequest::find()
            ->where([
                'from_firm_id'  => $firm_id ?? Firm::get()->id,
                'product_id'    => $this->id,
            ])
            ->andWhere(['!=', 'status', CommercialRequest::STATUS_DELETED])
            ->min('request_validity');
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params) {
        $query = self::find();
        unset($params['page']);
        $params = array_filter($params, function ($param) {
            return !empty($param);
        });
        $found_id = (new ProductSearch())
            ->setFilterParams($params)
            ->findIdList();
        if (!empty($params)) {
            $query->where(['id' => $found_id]);
        }
        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        if (!($this->setAttributes($params) && $this->validate())) {
            return $data_provider;
        }
        return $data_provider;
    }

    /**
     * Удаляет все фото товара.
     * @return $this
     */
    public function removeImages() {
        /** @var Images[] $images */
        $images = $this->getImages()->all();
        foreach ($images as $image) {
            $image->delete();
        }
        return $this;
    }

    public function afterDelete() {
        $this->clearRelations();
    }

    public function clearRelations(): bool
    {
        try {

            $this->removeImages();

            $this->getDeliveryTermsHelper()->setDeliveryTerms([])
                ->updateDeliveryTerms();

            $this->setCategories([])
                ->updateCategories();

            $this->clearCache();

            $this->getPlaceHelper()
                ->clearPlaceValues();

            $this->getVocabularyHelper()
                ->clearVocabularyValues();

            $this->removeSphinxIndex();

            return true;
        } catch (Exception $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
            return false;
        }
    }

    /**
     * Устанавливает категории в модель.
     * @param array $categories
     * @return $this
     */
    public function setCategories(array $categories) {
        if (empty($categories)) {
            $this->categories = [];
        }
        $this->categories = $categories;
        return $this;
    }

    /**
     * Метод возвращает строку с мощностями товара.
     * @return string
     */
    public function getCapacityString() {
        $out = '';
        if ($this->capacities_from) {
            $out .= 'мин. ' . Html::tag('strong', intval($this->capacities_from) . ' ' . $this->getUnitText(intval($this->capacities_from)));
        }
        if ($this->capacities_from && $this->capacities_to) {
            $out .= ' <span class="product-primary-specs__mdash item-spec-mdashs">&mdash;&mdash;&mdash;&mdash;</span> ';
        }
        if ($this->capacities_to) {
            //$out .= 'макс. '. Html::tag('strong', intval($this->capacities_to));
            $out .= 'макс. ' . Html::tag('strong', intval($this->capacities_to) . ' ' . $this->getUnitText(intval($this->capacities_to)));
        }
        if (!empty($out)) {
            $out .= ' / мес';
        }
        return $out;
    }

    /**
     * Устанавливает фото товара в модель.
     * @param array $images
     * @return $this
     */
    public function setImages(array $images) {
        $images = array_filter($images, function($image) {
            return trim($image) !== '';
        });
        $this->images = $images;
        return $this;
    }

    /**
     * Устанавливает порядок фото товара.
     * @param array $images_order
     * @return $this
     */
    public function setImagesOrder(array $images_order) {
        /** @var Images[] $images */
        $images = $this->getImages()->all();
        $images_order = array_flip($images_order);

        foreach ($images as $image) {
            $image->position = $images_order[$image->id] + 1;
            $image->save();
        }
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text) {
        $this->text = (string)$text;
        return $this;
    }

    /**
     * @param bool $from_import
     * @return $this
     */
    public function setFromImport($from_import = true) {
        $this->from_import = (bool)$from_import;
        return $this;
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
     * @param int $offset
     * @param int $limit
     */
    public function indexPart($offset, $limit) {
        /** @var Product[] $products */
        $products = Product::find()
            ->offset($offset)
            ->limit($limit)
            ->all();
        foreach ($products as $product) {
            $product->updateSphinxIndex();
        }
    }

    /**
     * Обновление товара в индексе.
     * @return int
     */
    public function updateSphinxIndex() {

        ini_set('memory_limit', '2048M');

        # характеристики товара.
        $vocabulary_values = $this->getVocabularyHelper()->getValues();
        $vocabulary_id_list = array_keys($vocabulary_values);
        $option_id_list = $this->getVocabularyHelper()->getOptionIds();

        # категории.
        $categories = $this->getCategoryIds();

        # способы доставки
        $delivery_terms = $this->getDeliveryTermsHelper()
            ->getDeliveryTermsIds();

        $product_title = $this->getTitle();

        # основная информация о товаре.
        $row = [
            'id' => (int)$this->id,
            'status' => (int)$this->status,
            'title' => (string)$product_title,
            'user_id' => (int)$this->user_id,
            'firm_id' => (int)$this->firm_id,
            'price' => (float)$this->price,
            'with_vat' => (bool)$this->price_with_vat,
            'capacities_from' => (int)$this->capacities_from,
            'capacities_to' => (int)$this->capacities_to,
        ];

        $this->addDataToIndexRow($vocabulary_id_list, $row, 'vocabularies');
        $this->addDataToIndexRow($option_id_list, $row, 'terms');
        $this->addDataToIndexRow($categories, $row, 'categories');
        $this->addDataToIndexRow($delivery_terms, $row, 'delivery_terms');

        $builder = new QueryBuilder(\Yii::$app->get('sphinx'));
        return $builder->sphinxSave(self::tableName(), $this->id, $row);
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
     * Очищает кеш товара.
     * @return $this
     */
    public function clearCache() {
        // кеш модели товара.
        Yii::$app->cache->set(self::CACHE_PRODUCT_ID. $this->id, null);
        // кеш категорий товара.
        Yii::$app->cache->set($this->getProductCategoryCacheKey(), null);
        return $this;
    }

    /**
     * Хелпер для работы с характеристиками товара.
     * @return VocabularyHelper
     */
    public function getVocabularyHelper(): VocabularyHelper {
        if (null === $this->vocabulary_helper) {
            $this->vocabulary_helper = (new VocabularyHelper())
                ->setProductModel($this);
        }
        return $this->vocabulary_helper;
    }

    /**
     * Хелпер для работы с названием товара.
     * @return TitleGeneratorHelper
     */
    public function getTitleGeneratorHelper(): TitleGeneratorHelper {
        if (null === $this->title_generator_helper) {
            $this->title_generator_helper = (new TitleGeneratorHelper())
                ->setProductModel($this);
        }
        return $this->title_generator_helper;
    }

    /**
     * Хелпер для работы с Местами реализации.
     * @return PlaceHelper
     */
    public function getPlaceHelper() {
        if (null === $this->place_helper) {
            $this->place_helper = (new PlaceHelper())
                ->setProductModel($this);
        }
        return $this->place_helper;
    }

    /**
     * @return DeliveryTermsHelper
     */
    public function getDeliveryTermsHelper(): DeliveryTermsHelper {
        if (null === $this->delivery_terms_helper) {
            $this->delivery_terms_helper = (new DeliveryTermsHelper())
                ->setProductModel($this);
        }
        return $this->delivery_terms_helper;
    }

    public function attributeLabels() {
        return [
            'id'                => '#',
            'title'             => 'Название товара',
            'status'            => 'Статус',
            'user_id'           => 'Пользователь',
            'firm_id'           => 'Организация владелец',
            'price'             => 'Цена',
            'unit'              => 'Единица измерения',
            'price_with_vat'    => 'Цена с НДС',
            'text'              => 'Описание',
            'capacities_from'   => 'Мощности от',
            'capacities_to'     => 'Мощности до',
            'from_import'       => 'Из прайс листа',
            'created_at'        => 'Created At',
            'updated_at'        => 'Updated At',
        ];
    }
}