<?php

namespace frontend\modules\api\controllers;

use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use common\models\goods\Product;
use common\models\goods\Images as ProductImage;
use common\modules\image\helpers\Image as ImageHelper;
use common\models\goods\search\Product as ProductFilter;

/**
 * Class ProductController
 * API контроллер для работы с товарами.
 * @package frontend\modules\api\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ProductController extends ApiController {

    /** Максимальное количество выдаваемых результатов. */
    const FIND_LIMIT = 50;

    const FIND_IMAGE_WIDTH = 65;
    const FIND_IMAGE_HEIGHT = 65;

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'find' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Похожие товары.
     * @return array
     */
    public function actionFind() {

        $data = \Yii::$app->request->post();

        $query = ArrayHelper::getValue($data, 'query');
        $limit = ArrayHelper::getValue($data, 'limit', self::FIND_LIMIT);
        $limit = $limit > self::FIND_LIMIT ? self::FIND_LIMIT : $limit;

        $response_format = ['error' => null, 'data' => []];

        // запрос не может быть пустым.
        if (null === $query || empty(trim($query))) {
            $response_format['error'] = 1;
            return $response_format;
        }

        try {

            /** @var Product[] $product_list */
            $product_list = (new ProductFilter())
                ->setFilterTitle($query)
                ->get()
                ->select(['id', 'title'])
                ->limit($limit)
                ->all()
            ;

            $products = [];
            foreach ($product_list as $product) {
                /** @var ProductImage $image */
                $image = $product->getImages()->select(['image'])->one();
                $image = ($image) ? ImageHelper::i()->generateRelativeImageLink($image->image, self::FIND_IMAGE_WIDTH, self::FIND_IMAGE_HEIGHT) : '';
                $products[] = [
                    'id' => $product->id,
                    'title' => $product->getTitle(),
                    'image' => $image,
                    'category' => '',
                ];
            }

            $response_format['error'] = 0;
            $response_format['data'] = (array)$products;
            return $response_format;

        } catch (Exception $e) {
            // произошла ошибка в запросах.
            Yii::error($e->getMessage(), 'apiRequest');
            $response_format['error'] = 2;
            return $response_format;
        }
    }

    /**
     * Метод отдаёт характеристики товаров.
     * @return array
     */
    public function actionGetVocabularyTerms() {

        $data = \Yii::$app->request->post();
        $product_id = ArrayHelper::getValue($data, 'product_id');
        $response_format = ['error' => 0, 'data' => []];

        // запрос не может быть пустым.
        if (null === $product_id || empty(trim($product_id)) || !is_numeric($product_id)) {
            $response_format['error'] = 1;
            return $response_format;
        }

        try {

            $product = Product::getByIdCached($product_id);
            // товар не найден.
            if (!$product) {
                $response_format['error'] = 3;
                return $response_format;
            }

            $vocabulary_terms = $product->getVocabularyTermsArray();
            $response_format['data'] = $vocabulary_terms;

            return $response_format;

        }
        catch (Exception $e) {
            // произошла ошибка в запросах.
            Yii::error($e->getMessage(), 'apiRequest');
            $response_format['error'] = 2;
            return $response_format;
        }
    }
}