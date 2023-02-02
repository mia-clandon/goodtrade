<?php

namespace frontend\components\lib;

use common\models\commercial\Response;
use common\models\goods\helpers\DeliveryTerms;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\libs\Logger;
use common\libs\StringHelper;
use common\libs\traits\Singleton;
use common\models\goods\Categories;
use common\models\goods\Product;
use common\modules\image\helpers\Image as ImageHelper;
use common\libs\Declension;
use common\models\commercial\Request;
use common\models\firms\Firm;
use common\models\goods\helpers\Vocabulary as VocabularyHelper;

/**
 * Class CompareProcessor
 * @package frontend\components\lib
 * @author Артём Широких kowapssupport@gmail.com
 */
class CompareProcessor {
    use Singleton;

    /** ключ cookie для хранения id товаров. */
    const COOKIE_KEY = 'compare';

    const PROP_PRODUCT_OBJECT = 0;
    const PROP_PRODUCT_ID_COMPARE = 1;
    const PROP_PRODUCT_TITLE_COMPARE = 2;
    const PROP_PRODUCT_LINK_COMPARE = 3;
    const PROP_PRODUCT_IMAGE_COUNT_COMPARE = 4;
    const PROP_PRODUCT_MAIN_IMAGE_COMPARE = 5;
    const PROP_PRODUCT_PRICE_COMPARE = 6;
    const PROP_PRODUCT_PRICE_FORMATTED_COMPARE = 7;
    const PROP_PRODUCT_REQUEST_VALIDITY = 8;
    const PROP_PRODUCT_REQUEST_PART_SIZE = 9;
    const PROP_PRODUCT_DELIVERY_TERMS = 10;
    const PROP_PRODUCT_REQUEST_PRODUCT_ADDRESS = 11;
    const PROP_PRODUCT_CAPACITY = 12;
    const PROP_PRODUCT_SPECIFICATIONS = 13;
    const PROP_PRODUCT_FIRMS = 14;
    const PROP_PRODUCT_RESPONSES = 15;
    const PROP_PRODUCT_PLACE = 16;
    const PROP_PRODUCT_PAY_PERSENT = 17;
    const PROP_PRODUCT_IN_STOCK = 18;
    const PROP_PRODUCT_RESPONSE_VALIDITY = 19;
    const PROP_PRODUCT_WITH_VAT = 20;

    /** @var null|integer */
    private $category_id = null;

    /**
     * @param int $category_id
     * @return $this
     */
    public function setCategoryId($category_id) {
        $this->category_id = (int)$category_id;
        return $this;
    }

    /**
     * @return string
     */
    public static function getCookieKey() {
        if(!empty(\Yii::$app->user->id)) {
            return self::COOKIE_KEY.\Yii::$app->user->id;
        }
        return self::COOKIE_KEY;
    }

    /**
     * Возвращает идентификаторы товаров с избранного.
     * @return array
     */
    public function getCompareProductIds() {
        $compare = ArrayHelper::getValue($_COOKIE, $this->getCookieKey(), '');
        $product_ids = [];
        if (StringHelper::isJson($compare) && !empty($compare)) {
            try {
                $product_ids = Json::decode($compare);
                $product_ids = array_map('intval', $product_ids);
            }
            catch (Exception $exception) {
                Logger::get()->error($exception->getMessage());
            }
        }
        return $product_ids;
    }

    /**
     * Возвращает избранные товары (id) по категории.
     * @return array
     */
    private function getFavoriteProductId() {
        if (!$this->category_id) {
            return [];
        }
        $product_ids = $this->getCompareProductIds();
        if (empty($product_ids)) {
            return [];
        }
        $product_ids = (new Query())
            ->select('pc.product_id')
            ->from(Categories::tableName().' pc')
            ->where([
                'pc.product_id' => $product_ids,
                'pc.category_id' => $this->category_id,
            ])
            ->groupBy('pc.product_id')
            ->all();
        return ArrayHelper::getColumn($product_ids, 'product_id', []);
    }

    /**
     * Метод собирает данные для таблицы сравнения.
     * @return array
     */
    public function getProductDataForCompare() {
        //TODO: метод необходимо кешировать до тех пор пока товары в избранном не поменяются.
        $product_ids = $this->getFavoriteProductId();
        $out_data = [];

        /** @var Product[] $products */
        $products = Product::find()->where(['id' => $product_ids])->all();

        $vocabularies = [];
        $product_id_list = [];

        foreach ($products as $product) {
            if(!\Yii::$app->user->isGuest) {
                $commercial_request = Request::getRequestByProduct($product->id, Firm::get()->id);
                $commercial_response = Response::getResponseByProduct($product->id, Firm::get()->id);
            }
            else {
                $commercial_request = null;
                $commercial_response = null;
            }
            $out_data[self::PROP_PRODUCT_RESPONSES][] = $commercial_response;

            if($commercial_response) {
                $time_to_send = $commercial_response->time_to_send?Declension::number($commercial_response->time_to_send, 'день', 'дня', 'дней', true).' до отправки':'';
                $out_data[self::PROP_PRODUCT_IN_STOCK][] = $commercial_response->in_stock?'В наличии':$time_to_send;
            } else {
                $out_data[self::PROP_PRODUCT_IN_STOCK][] = '&mdash;';
            }

            if($commercial_response) {
                $with_vat = $commercial_response->with_vat;
            } else {
                $with_vat = $product->price_with_vat;
            }

            $out_data[self::PROP_PRODUCT_WITH_VAT][] = $with_vat?'С учетом НДС':'Без учета НДС';

            if($commercial_response) {
                $out_data[self::PROP_PRODUCT_PAY_PERSENT][] = $commercial_response->pre_payment.'% &mdash; предоплата <br>'.$commercial_response->post_payment.'% &mdash; постоплата';
            } else {
                $out_data[self::PROP_PRODUCT_PAY_PERSENT][] = '&mdash;';
            }

            #компания
            $firm = $product->getFirm()->one();
            $out_data[self::PROP_PRODUCT_FIRMS][] = $firm;
            $out_data[self::PROP_PRODUCT_PLACE][] = $product->getLocation() ?? (is_null($firm)?'':$firm->getLocation());

            $product_id_list[] = $product->id;
            $out_data[self::PROP_PRODUCT_OBJECT][] = $product;
            $out_data[self::PROP_PRODUCT_ID_COMPARE][] = $product->id;
            $out_data[self::PROP_PRODUCT_TITLE_COMPARE][] = $product->getTitle();
            $out_data[self::PROP_PRODUCT_LINK_COMPARE][] = \Yii::$app->urlManager->createUrl([
                'product/show', 'id' => $product->id
            ]);
            $image_count = $product->getImages()->count();
            $out_data[self::PROP_PRODUCT_IMAGE_COUNT_COMPARE][] = $image_count
                ? Declension::number($image_count, 'фотография', 'фотографии', 'фотографий', true)
                : 'Нет фотографий'
            ;
            $main_image = $product->getMainImage();
            $out_data[self::PROP_PRODUCT_MAIN_IMAGE_COMPARE][] = $main_image
                ? ImageHelper::i()->generateRelativeImageLink($main_image->image, 207, 40)
                : '/img/placeholders/207x40.png';
            ;

            $price_compare = $product->price;
            if($commercial_response) {
                $price_compare = $commercial_response->product_price;
            }

            $out_data[self::PROP_PRODUCT_PRICE_COMPARE][] = $price_compare;
            $out_data[self::PROP_PRODUCT_PRICE_FORMATTED_COMPARE][] = number_format($price_compare, 0, '.', '.');;

            if($commercial_response) {
                $commercial_response_validity = $commercial_response->response_validity;
                $commercial_response_validity = $commercial_response_validity
                    ? Declension::number($commercial_response_validity, 'день', 'дня', 'дней', true)
                    : '';
            } else if($commercial_request){
                $commercial_response_validity = 'Ожидается ответ';
            } else {
                $commercial_response_validity = '';
            }

            $out_data[self::PROP_PRODUCT_RESPONSE_VALIDITY][] = $commercial_response_validity;

            $commercial_request_validity = ($commercial_request)
                ? $commercial_request->request_validity
                : null;
            $out_data[self::PROP_PRODUCT_REQUEST_VALIDITY][] = $commercial_request_validity
                ? Declension::number($commercial_request_validity, 'день', 'дня', 'дней', true)
                : '';

            $part_size = null;
            if($commercial_response) {
                $part_size = $commercial_response->product_count;
            }

            $out_data[self::PROP_PRODUCT_REQUEST_PART_SIZE][] = $part_size;

            $delivery_terms = $product->getDeliveryTermsHelper()->getDeliveryTermsString();
            if($commercial_response) {
                $delivery_term = new DeliveryTerms();
                $delivery_term_ids = $commercial_response->getResponseData()->getDeliveryTerms();
                $delivery_terms = $delivery_term->getDeliveryTermsString($delivery_term_ids);
            }
            $out_data[self::PROP_PRODUCT_DELIVERY_TERMS][] = $delivery_terms;
            $out_data[self::PROP_PRODUCT_REQUEST_PRODUCT_ADDRESS][] = $commercial_request
                ? $commercial_request->getAddressString()
                : null;
            $out_data[self::PROP_PRODUCT_CAPACITY][] = $product->getCapacityString();

            #технические характеристики.
            $vocabulary_decorated_data = $product->getVocabularyHelper()->getDecoratedValues();
            foreach ($vocabulary_decorated_data as $vocabulary_data) {
                $vocabulary_id = $vocabulary_data[VocabularyHelper::KEY_VOCABULARY][VocabularyHelper::KEY_VALUE];
                $vocabulary_value = $vocabulary_data[VocabularyHelper::KEY_VOCABULARY_VALUE][VocabularyHelper::KEY_VALUE];
                // по данному товару такой характеристики - нет.
                if (empty($vocabulary_value)) {
                    $vocabulary_value = NULL;
                }
                $vocabularies[$vocabulary_id][$product->id] = $vocabulary_value;
            }
        }

        if (!empty($products)) {
            foreach ($vocabularies as $vocabulary_name => $data) {
                ksort($vocabularies[$vocabulary_name]);
            }
            $out_data[self::PROP_PRODUCT_SPECIFICATIONS] = $vocabularies;
        }
        return $out_data;
    }
}