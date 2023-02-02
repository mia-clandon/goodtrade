<?php

namespace frontend\controllers;

use backend\components\ProductVocabularyProcessor;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

use common\models\firms\Firm;
use common\models\goods\Product;
use common\models\goods\search\Product as ProductFilter;
use common\libs\Env;

/**
 * Class ProductController
 * @package frontend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ProductController extends BaseController {

    /** Количество товаров в строке (вывод похожих/моих) */
    const COUNT_IN_ROW = 2;
/*
    public function beforeAction($action) {
        $this->getBreadcrumbs()
            ->addBreadcrumbsLink(
                \Yii::$app->urlManager->createUrl(['/product']),
                'Каталог'
            );
        return parent::beforeAction($action);
    }
*/
    /**
     * Страница просмотра товара.
     * @return string
     * @throws Exception
     */
    public function actionShow() {
        $this->layout = 'b2b';
        $this->registerScriptBundleB2B();
        $product = $this->getProductModel();

        /** @var Firm $firm */
        $firm = $product->getFirm()->one();
        if (null !== $firm && !$firm->isActiveStatus()) {
            throw new Exception(404);
        }

        if ($product->status == Product::STATUS_PRODUCT_MODERATION
            && !$product->isMine()
            // админ может просматривать любой товар.
            && !Firm::get()->isAdmin()
        ) {
            throw new Exception(404);
        }

        $this->getBreadcrumbsB2B()
            ->addBreadcrumbsLink(
                \Yii::$app->urlManager->createUrl(['product/show', 'id' => $product->id]),
                $product->getTitle()
            );

        $this->seo->title = $product->getTitle();

        // страница перехода.
        $referrer = $this->getSearchReferrer();

        return $this->render('show', [
            'product'   => $product,
            'referrer'  => $referrer,
            'firm'      => $firm,
        ]);
    }

    /**
     * todo:
     * Поиск товаров организации.
     * @throws Exception
     * @return string
     */
    public function actionGetFirmProductList() {
        $this->layout = false;
        $default_limit = Env::i()->isProd() ? Firm::FIRM_PRODUCT_LIMIT : 2;

        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $page = (int)\Yii::$app->request->post('page', 1);
        $offset = ($page - 1) * $default_limit;
        $firm_id = (int)\Yii::$app->request->post('firm_id', 0);

        if (!$firm_id) {
            throw new Exception('Организация не найдена.');
        }

        /** @var Firm $firm */
        $firm = Firm::findOne($firm_id);

        $category_id = (array)\Yii::$app->request->post('category');
        $category_id = array_map('intval', $category_id);

        // поиск товаров организации.
        $product_search = (new ProductFilter())
            ->setFilterFirmId($firm->id)
        ;

        if(!empty($category_id)) {
            $product_search->setFilterCategories($category_id);
        }

        $product_count = (int)$product_search->count();

        $product_list = $product_search->get()
            ->limit($default_limit)
            ->offset($offset)
            ->all();
        $product_list = array_chunk($product_list, self::COUNT_IN_ROW);

        if ($page == 1) {
            // для первой страницы.
            return $this->render('get-firm-product-list', [
                'product_list'   => $product_list,
                'product_count'  => $product_count,
                'default_limit'  => $default_limit,
                'page'           => $page,
            ]);
        }
        else {
            if (count($product_list)) {
                // только товары, без пагинатора.
                return \Yii::$app->getView()->renderFile(\Yii::getAlias('@frontend/views/product/parts/product_list.php'),[
                    'product_list'  => $product_list,
                    'page'          => $page,
                ]);
            }
            else {
                return '';
            }
        }
    }

    /**
     * @return null|string
     */
    private function getSearchReferrer() {
        $referrer = \Yii::$app->request->referrer;
        if (!is_null($referrer)) {
            // перешли с поиска ?
            if (strpos($referrer, 'product/index')) {
                $parsed_url = parse_url($referrer);
                $query = ArrayHelper::getValue($parsed_url, 'query');
                $query_data = [];
                parse_str($query, $query_data);
                $query = ArrayHelper::getValue($query_data, 'query');
                return \Yii::$app->urlManager->createUrl(['product/index', 'query' => $query]);
            }
        }
        return null;
    }

    /**
     * Подгрузка характеристик и их значений.
     * @throws
     * @return string
     */
    public function actionGetVocabularyTermsForm() {
        $category_id = \Yii::$app->request->post('category_id');
        if (!$category_id) {
            throw new Exception('Не передана категория товара.');
        }
        $this->layout = false;
        $product_id = (int)\Yii::$app->request->post('product_id');
        $processor = ProductVocabularyProcessor::i()
            ->setCategoryId($category_id)
            ->setEnvelope(ProductVocabularyProcessor::ENVELOPE_FRONTEND);
        if ($product_id > 0) {
            $processor->setProductId($product_id);
        }
        return $processor->renderVocabularyTerms();
    }

    /**
     * @return Product
     * @throws Exception
     */
    protected function getProductModel() {
        $product_id = \Yii::$app->request->get('id', 0);
        if ($product_id) {
            $product = Product::getByIdCached($product_id);
            if (!$product) {
                throw new Exception('Товар не найден', 404);
            }
            return $product;
        }
        else {
            throw new Exception('Товар не найден', 404);
        }
    }
}