<?php

namespace backend\components;

use yii\db\Query;
use yii\helpers\ArrayHelper;

use common\libs\Logger;
use common\libs\traits\Singleton;
use common\models\goods\Images;
use common\models\goods\Product;
use common\models\goods\VocabulariesTerm;
use common\models\goods\Categories;
use common\models\goods\DeliveryTerms;
use common\modules\image\helpers\Image;

/**
 * Class CleanProcessor
 * @package backend\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class CleanProcessor {

    use Singleton;

    /**
     * @return $this
     */
    public function clean() {
        $this->processProductImages();
        $this->processProductVocabularyTerms();
        $this->processProductDeliveryTerms();
        $this->processProductCategories();
        return $this;
    }

    /**
     * Удаляет связи с фото и файл - у товаров которых нет.
     */
    public function processProductImages() {
        Logger::get()->info('--- Начало очистки не связанных с товаром изображений.');

        $product_query = new Query();
        $product_query->from(Product::TABLE_NAME)
            ->select(['id']);

        $query = new Query();
        $query->select(['product_id', 'image'])
            ->from(Images::TABLE_NAME)
            ->where(['NOT IN', 'product_id', $product_query]);

        $images = $query->all();

        Logger::get()->info('Найдено '.count($images). ' записей.');

        foreach ($images as $image) {
            $path = ArrayHelper::getValue($image, 'image', false);
            $product_id = (int)ArrayHelper::getValue($image, 'product_id', 0);
            if ($path) {
                Image::getInstance()->removeAllThumbnails($path, true);
            }
            // remove record.
            (new Query())->createCommand()
                ->delete(Images::TABLE_NAME, ['product_id' => $product_id])
                ->execute()
            ;
        }
        Logger::get()->info('--- end.');
    }

    /**
     * Удаляет связи с характеристиками - у товаров которых нет.
     */
    public function processProductVocabularyTerms() {
        Logger::get()->info('--- Начало очистки не связанных с товаром характеристик.');

        $product_query = new Query();
        $product_query->from(Product::TABLE_NAME)
            ->select(['id']);

        $query = new Query();
        $query->select(['product_id'])
            ->from(VocabulariesTerm::TABLE_NAME)
            ->where(['NOT IN', 'product_id', $product_query]);

        $vocabulary_term = $query->all();
        $ids = ArrayHelper::getColumn($vocabulary_term, 'product_id');

        Logger::get()->info('Найдено '.count($ids). ' записей.');

        // remove records.
        (new Query())->createCommand()
            ->delete(VocabulariesTerm::TABLE_NAME, ['product_id' => $ids])
            ->execute()
        ;
        Logger::get()->info('--- end.');
    }

    /**
     * Удаляет связи со способами доставки - у товаров которых нет.
     */
    public function processProductDeliveryTerms() {
        Logger::get()->info('--- Начало очистки не связанных с товаром способов доставки.');

        $product_query = new Query();
        $product_query->from(Product::TABLE_NAME)
            ->select(['id']);

        $query = new Query();
        $query->select(['product_id'])
            ->from(DeliveryTerms::TABLE_NAME)
            ->where(['NOT IN', 'product_id', $product_query]);

        $delivery_terms = $query->all();
        $ids = ArrayHelper::getColumn($delivery_terms, 'product_id');

        Logger::get()->info('Найдено '.count($ids). ' записей.');

        // remove records.
        (new Query())->createCommand()
            ->delete(DeliveryTerms::TABLE_NAME, ['product_id' => $ids])
            ->execute()
        ;
        Logger::get()->info('--- end.');
    }

    /**
     * Удаляет связи с категориями - у товаров которых нет.
     */
    public function processProductCategories() {
        Logger::get()->info('--- Начало очистки не связанных с товаром категорий.');

        $product_query = new Query();
        $product_query->from(Product::TABLE_NAME)
            ->select(['id']);

        $query = new Query();
        $query->select(['product_id'])
            ->from(Categories::TABLE_NAME)
            ->where(['NOT IN', 'product_id', $product_query]);

        $categories = $query->all();
        $ids = ArrayHelper::getColumn($categories, 'product_id');

        Logger::get()->info('Найдено '.count($ids). ' записей.');

        // remove records.
        (new Query())->createCommand()
            ->delete(Categories::TABLE_NAME, ['product_id' => $ids])
            ->execute()
        ;
        Logger::get()->info('--- end.');
    }
}