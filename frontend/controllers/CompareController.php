<?php

namespace frontend\controllers;

use common\models\base\Category;
use common\models\goods\Product;
use common\models\goods\search\Product as ProductFilter;
use frontend\assets\PerfectScrollbar;
use frontend\components\lib\CompareProcessor;
use yii\base\Exception;

/**
 * Class CompareController
 * @package frontend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class CompareController extends BaseController {

    private const SUGGEST_PRODUCT_LIMIT = 5;


    private function registerPageScripts() {
        $this->registerScriptBundle();
        PerfectScrollbar::register($this->getView());
    }

    /**
     * Страница сравнения.
     * @return string
     * @throws Exception
     */
    public function actionIndex() {
        $this->seo->title = 'Таблица сравнения';
        $category_id = \Yii::$app->request->get('category');
        if (!$category_id) {
            throw new Exception(404);
        }
        /** @var Category $category */
        $category = Category::findOne($category_id);
        if (!$category) {
            throw new Exception(404);
        }
        $product_list = CompareProcessor::i()
            ->setCategoryId($category_id)
            ->getProductDataForCompare();
        // для сравнения нужны товары, не меньше 2х.
        if (empty($product_list)) {
            // товары для сравнения в данной категории не добавили.
            return $this->redirect([\Yii::$app->urlManager->createUrl(['category/show', 'id' => $category_id])]);
        }

        $this->getBreadcrumbs()
            ->addBreadcrumbsLink(
                '#',
                'Сравнение'
            )->addBreadcrumbsLink(
                \Yii::$app->urlManager->createUrl(['category/show', 'id' => $category_id]),
                $category->title
            );

        $product_ids = CompareProcessor::i()
            ->getCompareProductIds();

        // поиск товаров организации.
        $product_search = (new ProductFilter())
            ->setFilterCategories([$category_id])
            ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE)
            ->setFilterNotInId($product_ids)
        ;

        $product_count = $product_search->count();

        $this->registerPageScripts();
        return $this->render('index', [
            'product_list' => $product_list,
            'category'     => $category,
            'product_count'     => $product_count,
        ]);
    }

    /**
     * Подгрузка товара по ajax.
     * @throws Exception
     * @return string
     */
    public function actionGetProductList() {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $offset = \Yii::$app->request->post('offset', '');
        $category_id = (int)\Yii::$app->request->post('category_id', 0);
        $product_ids = (array)\Yii::$app->request->post('product_ids', []);
        $product_ids = array_map('intval', $product_ids);

        /** @var Category $category */
        $category = Category::findOne($category_id);

        if (!$category) {
            throw new Exception('Категория не найдена.');
        }


        // поиск товаров организации.
        $product_search = (new ProductFilter())
            ->setFilterCategories([$category_id])
            ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE)
            ->setFilterNotInId($product_ids)
        ;

        $product_count = $product_search->count();

        $products = $product_search->get()
            ->limit(self::SUGGEST_PRODUCT_LIMIT)
            ->offset($offset)
            ->all();

        return $this->render('products', [
            'products'  => $products,
            'product_count'  => $product_count,
            'product_limit'  => self::SUGGEST_PRODUCT_LIMIT,
            'product_offset' => $offset,
        ]);
    }
}