<?php

namespace frontend\controllers;

use common\models\Category;
use common\models\goods\Product;
use common\models\CategorySlider;
use common\models\goods\search\Product as ProductFilter;
use common\models\firms\search\Firm as FirmSearch;

use frontend\forms\Search;
use yii\base\Exception;
use yii\db\Expression;

/**
 * Class CategoryController
 * todo: порефакторить контроллер после запуска, слишком много дублирующего кода, некоторый код legacy.
 * todo: часть поиска вырезать после запуска.
 * @package frontend\controllers
 * @author yerganat
 */
class CategoryController extends BaseController {

    const POPULAR_PRODUCT_LIMIT = 6;
    const POPULAR_FIRM_LIMIT = 6;
    const CATEGORY_LIMIT = 6;

    /**
     * Страница просмотра категории.
     * @return string
     * @throws Exception
     */
    public function actionShow() {
        $this->layout = 'b2b';
        $this->registerScriptBundleB2B();
        $category = $this->getCategoryModel();

        /**
         * @var $category_parents Category[]
         */
        $category_parents = [];
        $category->getAllParents($category, $category_parents);
        $category_parents = array_reverse($category_parents);
        $category_parents[] = $category;

        $breadcrumb = [];
        foreach ($category_parents as $parent) {
            $this->getBreadcrumbsB2B()
                ->addBreadcrumbsLink(
                    \Yii::$app->urlManager->createUrl(['category/show', 'id' => $parent->id]),
                    $parent->title,
                    $parent->icon_class
                );
            $breadcrumb[] = $parent->title;
        }


        $this->seo->title = $category->title;

        $slides = CategorySlider::find()
            ->where(['category_id' => $category->id])
            ->orderBy('updated_at')
            ->limit(4)
            ->all();

        $child_ids = $category->getAllChildIds();
        // поиск товаров.
        $product_filter = (new ProductFilter())
            ->setFilterCategories(array_merge([$category->id], $child_ids))
            ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE)
        ;
        $product_count = $product_filter->count();

        $products = $product_filter->get()
            ->limit(self::POPULAR_PRODUCT_LIMIT)
            ->all();

        // поиск компании
        $product_firm_filter = (new ProductFilter())
            ->select(['firm_id', new Expression('count(*) as product_count')])
            ->setFilterCategories(array_merge([$category->id], $child_ids))
            ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE)
            ->setFilterNotFirmId(0)
            ->setGroupBy(['firm_id'])
            ->setOrderBy('product_count desc');

        $firm_count = $product_firm_filter->count();

        $current_category_firms = $product_firm_filter->findColumnList('firm_id');

        $firms = [];
        if($current_category_firms) {
            $firm_filter = (new FirmSearch())
                ->setFilterInId($current_category_firms);
            $firms = $firm_filter->get()->offset(0)->limit(self::POPULAR_FIRM_LIMIT)->all();
        }

        /**
         * @var \common\models\Category[] $children
         * @var \common\models\Category[] $siblings
         * */
        $children = Category::find()
            ->where(['parent' => $category->id])
            ->limit(self::CATEGORY_LIMIT)
            ->all();

        $siblings = Category::find()
            ->where(['parent' => $category->parent])
            ->andWhere(['<>', 'id' , $category->id])
            ->limit(self::CATEGORY_LIMIT)
            ->all();


        return $this->render('show', [
            'product_limit'         =>  self::POPULAR_PRODUCT_LIMIT,
            'product_count'         =>  $product_count,
            'firm_limit'            =>  self::POPULAR_FIRM_LIMIT,
            'firm_count'            =>  $firm_count,
            'category'              =>  $category,
            'slides'                =>  $slides,
            'products'              =>  $products,
            'firms'                 =>  $firms,
            'children'              =>  $children,
            'siblings'              =>  $siblings,
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

        $offset = (int)\Yii::$app->request->post('offset', 0);
        $category_id = (int)\Yii::$app->request->post('category_id', 0);

        /** @var Category $category */
        $category = Category::findOne($category_id);

        if (!$category) {
            throw new Exception('Категория не найдена.');
        }

        $child_ids = $category->getAllChildIds();

        // поиск товаров организации.
        $product_search = (new ProductFilter())
            ->setFilterCategories(array_merge([$category_id], $child_ids))
            ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE)
        ;

        $products = $product_search->get()
            ->limit(self::POPULAR_PRODUCT_LIMIT)
            ->offset($offset)
            ->all();

        return $this->render('parts/products', [
            'products'  => $products,
            'category'  => $category,
        ]);
    }

    public function getSearchForm(): Search {
        $form = parent::getSearchForm();
        $form->setTemplatePath(\Yii::getAlias('@frontend/views/category'));
        // todo: установить текущую категорию.
        return $form;
    }

    /**
     * Подгрузка компании по ajax.
     * @throws Exception
     * @return string
     */
    public function actionGetFirmList() {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }

        $offset = (int)\Yii::$app->request->post('offset', 0);
        $category_id = (int)\Yii::$app->request->post('category_id', 0);

        /** @var Category $category */
        $category = Category::findOne($category_id);

        if (!$category) {
            throw new Exception('Категория не найдена.');
        }

        $child_ids = $category->getAllChildIds();

        // поиск компании
        $product_firm_filter = (new ProductFilter())
            ->select(['firm_id', new Expression('count(*) as product_count')])
            ->setFilterCategories(array_merge([$category->id], $child_ids))
            ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE)
            ->setFilterNotFirmId(0)
            ->setGroupBy(['firm_id'])
            ->setOrderBy('product_count desc');

        $current_category_firms = $product_firm_filter->findColumnList('firm_id');

        $firms = [];
        if($current_category_firms) {
            $firm_filter = (new FirmSearch())
                ->setFilterInId($current_category_firms);
            $firms = $firm_filter->get()->offset($offset)->limit(self::POPULAR_FIRM_LIMIT)->all();
        }

        return $this->render('parts/firms', [
            'firms'     => $firms,
            'category'  => $category,
        ]);
    }

    /**
     * @return Category
     * @throws Exception
     */
    protected function getCategoryModel() {
        $category_id = \Yii::$app->request->get('id', 0);
        if ($category_id) {
            $category = Category::findOne($category_id);
            if (!$category) {
                throw new Exception('Категория не найдена', 404);
            }
            return $category;
        }
        else {
            throw new Exception('Категория не найдена', 404);
        }
    }
}