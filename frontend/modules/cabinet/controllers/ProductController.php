<?php

namespace frontend\modules\cabinet\controllers;

use common\libs\Declension;
use common\libs\SearchHelper;
use common\models\Category;
use common\models\firms\Firm;
use common\models\goods\Product;
use common\models\goods\search\Product as ProductSearch;
use common\models\PriceQueue;
use frontend\components\helper\Alert;
use frontend\modules\cabinet\forms\product\Add;
use frontend\modules\cabinet\forms\product\AddPrice;
use frontend\modules\cabinet\forms\product\Search as SearchForm;
use yii\base\Exception;

/**
 * Class ProductController
 * @package frontend\modules\cabinet\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ProductController extends DefaultController
{

    const COUNT_FOUND_IN_ROW = 2;

    /** @var  SearchHelper */
    private $search_helper;

    /** @var  SearchForm */
    private $search_form;

    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        $this->getBreadcrumbs()
            ->addBreadcrumbsLink(\Yii::$app->urlManager->createUrl(['/cabinet/product']), 'Товары');

        return $result;
    }

    /**
     * @return SearchHelper
     */
    public function getSearchHelper(): SearchHelper
    {
        if (null === $this->search_helper) {
            $this->search_helper = (new SearchHelper())
                ->setPossibleSortProperties([
                    'title' => 'По названию',
                ])
                ->setPossibleFilterProperties([
                    'category_id'
                ])
                ->setRouteUrl('cabinet/product');
        }
        return $this->search_helper;
    }

    /**
     * Мои товары.
     * @return string
     */
    public function actionIndex()
    {

        $this->seo->title = 'Мои товары';
        $this->registerScriptBundle();

        $product_search = (new ProductSearch())
            ->setFilterFirmId(Firm::get()->id);

        /** @var SearchHelper $search_helper */
        $search_helper = $this->getSearchHelper();

        // всего моих товаров.
        $product_count = $product_search->count();
        $search_form = $this->getSearchFormTest();

        // обработка get запроса query.
        if ($query = \Yii::$app->request->get('query')) {
            $search_form->setFormData(\Yii::$app->request->get());
            $product_search->setFilterTitle($query);
        }

        // фильтр по категории.
        $filter_category_id = $search_helper->getFilterValue('category_id');
        $filter_category_name = $filter_category_id !== null
            ? Category::findOne($filter_category_id)
            : '';
        $filter_category_name = $filter_category_name->title ?? '';

        if (null !== $filter_category_id) {
            $product_search->setFilterCategories([(int)$filter_category_id]);
        }

        // найденные товары.
        $product_found_count = $product_search->count();
        $found = $product_search->get();

        // сортировка
        $sort_data = $search_helper->getSortingData();
        foreach ($sort_data as $property => $direction) {
            $found->orderBy($property . ' ' . $direction);
        }
        $found = $this->prepareFoundArray($found->all());

        /** @var int $price_list_count */
        $price_list_count = PriceQueue::getMine()->count();
        /** @var PriceQueue[] $price_list */
        $price_list = PriceQueue::getMine()->all();

        /** @var array $all_my_product_categories - список категорий моих товаров. */
        $all_my_product_categories = $this->getMyProductCategories();

        $search_form->addTemplateVars([
            // товары.
            'product_found_count' => $product_found_count,
            'product_found_count_stringify' => Declension::number($product_found_count, 'товар', 'товара', 'товаров', true),

            // прайс - листы.
            'price_list_found_count' => $price_list_count,
            'price_list_found_count_stringify' => Declension::number($price_list_count, 'прайс-лист', 'прайс-листа', 'прайс-листов', true),

            // параметры сортировок.
            'sort_links' => $search_helper->getSortLinks(),
            // список категорий моих товаров.
            'all_my_product_categories' => $all_my_product_categories,
            // фильтра.
            'filter_category_id' => $filter_category_id,
            'filter_category_name' => $filter_category_name,
            'filter_category_drop_url' => $search_helper->getLinkWithoutFilter('category_id'),
            'search_params' => $search_helper->getSearchParams(),
            'has_active_filters' => $search_helper->hasActiveFilters(true),
            'clear_filter_sort_form' => \Yii::$app->urlManager->createUrl(['/cabinet/product/']),
        ]);

        return $this->render('index', [
            'product_count' => $product_count,
            'search_form' => $search_form->render(),
            'found_product' => $found,
            'found_count' => $product_found_count,
            'price_list' => $price_list,
        ]);
    }

    /**
     * @return SearchForm
     */
    protected function getSearchFormTest()
    {
        if (null === $this->search_form) {
            $this->search_form = (new SearchForm())
                ->setGetMethod()
                ->setTemplateFileName('search');
            $this->search_form->getQueryControl()
                ->removeAttribute('required');
        }
        return $this->search_form;
    }

    /**
     * Добавление товара.
     * @return string
     */
    public function actionAdd()
    {
        $this->seo->title = 'Добавление товара';
        $this->registerScriptBundle();
        $this->getBreadcrumbs()
            ->addBreadcrumbsLink(\Yii::$app->urlManager->createUrl(['/cabinet/product/add']), 'Добавление товара');

        $form = (new Add())
            ->setPostMethod()
            ->setTemplateFileName('add')
            ->setModel(new Product())
            ->setAjaxMode();

        if (\Yii::$app->request->isAjax) {
            $this->layout = false;
            return $form->ajaxValidateAndSave();
        }
        return $this->render('add', [
            'form' => $form->render(),
            'action' => 'add',
        ]);
    }

    /**
     * Обновление товара.
     */
    public function actionUpdate()
    {
        $this->seo->title = 'Редактирование товара';
        $this->registerScriptBundle('cabinet_product_add');

        $this->getBreadcrumbs()
            ->addBreadcrumbsLink(\Yii::$app->urlManager->createUrl(['/cabinet/product/index']), 'Список товаров');

        $form = (new Add())
            ->setPostMethod()
            ->setTemplateFileName('add')
            ->setModel($this->getModel())
            ->setAjaxMode();
        if (\Yii::$app->request->isAjax) {
            $this->layout = false;
            return $form->ajaxValidateAndSave();
        }
        return $this->render('add', [
            'form' => $form->render(),
            'action' => 'update',
        ]);
    }

    /**
     * Добавление товара. (прайс лист).
     * @return string
     */
    public function actionAddPrice()
    {
        $this->seo->title = 'Добавление товаров из прайс листа';
        $this->registerScriptBundle();

        $form = (new AddPrice())
            ->setPostMethod()
            ->setTemplateFileName('add_price')
            ->setModel(new PriceQueue());

        if (\Yii::$app->request->isPost) {
            $form->validate();
            if ($form->isValid() && $form->save()) {
                Alert::setMessage('Прайс лист успешно отправлен на проверку.');
                return $this->refresh();
            } else {
                $first_error = $form->getFirstError();
                if (!empty($first_error)) {
                    Alert::setMessage($first_error, Alert::MESSAGE_ERROR);
                }
            }
        }

        return $this->render('add_price', [
            'form' => $form->render(),
        ]);
    }

    /**
     * @param array $found
     * @return array
     */
    private function prepareFoundArray(array $found)
    {
        // последний элемент массива "добавить товар"
        $found[] = [];
        return array_chunk($found, self::COUNT_FOUND_IN_ROW);
    }

    /**
     * @return Category[]
     */
    private function getMyProductCategories(): array
    {
        $data = [];

        /** @var Category[] $all_my_product_categories */
        $all_my_product_categories = Product::getProductCategoryQueryByFirm()->all();
        /** @var SearchHelper $search_helper */
        $search_helper = $this->getSearchHelper();
        /** @var null|Category $category_id */
        $category_id = $search_helper->getFilterValue('category_id');

        foreach ($all_my_product_categories as $category) {
            if (null !== $category_id
                && (int)$category_id === (int)$category->id
            ) {
                continue;
            }
            $filter_url = \Yii::$app->urlManager->createUrl($search_helper->getRequestParamsWithSort([
                'category_id' => $category->id
            ]));
            $data[$filter_url] = $category->title;
        }
        if ($search_helper->isActiveFilter('category_id')) {
            $url = $search_helper->getLinkWithoutFilter('category_id');
            $data[$url] = 'Все категории';
        }
        return $data;
    }

    /**
     * @return Product
     * @throws Exception
     */
    private function getModel()
    {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $model Product
         */
        if ($id) {
            $model = Product::findOne($id);
            if (!$model) {
                throw new Exception('Записи не существует.');
            }
        } else {
            $model = new Product();
        }
        return $model;
    }
}