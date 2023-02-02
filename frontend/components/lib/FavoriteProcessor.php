<?php

namespace frontend\components\lib;

use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\libs\Logger;
use common\libs\StringHelper;
use common\libs\traits\Singleton;
use common\models\goods\Product;

/**
 * Class FavoriteProcessor
 * @package frontend\components\lib
 * @author Артём Широких kowapssupport@gmail.com
 */
class FavoriteProcessor {
    use Singleton;

    /** ключ cookie для хранения id компании. */
    const COOKIE_COMPANY_KEY = 'favorite_company';

    /** ключ cookie для хранения id товаров. */
    const COOKIE_PRODUCT_KEY = 'favorite_product';

    const PROP_FRIM_OBJECT = 0;
    const PROP_PRODUCT_IDS = 1;


    /**
     * @return string
     */
    public static function getCompanyCookieKey() {
        if(!empty(\Yii::$app->user->id)) {
            return self::COOKIE_COMPANY_KEY.\Yii::$app->user->id;
        }
        return self::COOKIE_COMPANY_KEY;
    }

    /**
     * @return string
     */
    public static function getProductCookieKey() {
        if(!empty(\Yii::$app->user->id)) {
            return self::COOKIE_PRODUCT_KEY.\Yii::$app->user->id;
        }
        return self::COOKIE_PRODUCT_KEY;
    }

    /**
     * Возвращает идентификаторы товаров/компании с избранного.
     * @return array
     */
    private function getCompareIds($key) {
        $favorite = ArrayHelper::getValue($_COOKIE, $key, '');
        $ids = [];
        if (StringHelper::isJson($favorite) && !empty($favorite)) {
            try {
                $ids = Json::decode($favorite);
                $ids = array_map('intval', $ids);
            }
            catch (Exception $exception) {
                Logger::get()->error($exception->getMessage());
            }
        }
        return $ids;
    }

    /**
     * Возвращает избранные товары (id) /комапнии
     * @return array
     */
    public function getFavoriteIds() {
        $product_ids = $this->getCompareIds(self::getProductCookieKey());


        $firm_product_ids = (new Query())
            ->select(['p.firm_id', 'group_concat(p.id) product_ids'])
            ->from(Product::tableName().' p')
            ->where([
                'p.id' => $product_ids,
            ])
            ->groupBy('p.firm_id')
            ->all();


        $data= [];
        foreach ($firm_product_ids as $firm_product_id) {
            $data[(int)$firm_product_id['firm_id']] = explode(',', $firm_product_id['product_ids']);
        }

        $firm_ids = $this->getCompareIds(self::getCompanyCookieKey());
        foreach ($firm_ids as $firm_id) {
            if(!array_key_exists((int)$firm_id, $data)) {
                $data[(int)$firm_id] = [];
            }
        }

        return $data;
    }
}