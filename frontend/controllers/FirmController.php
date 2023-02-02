<?php

namespace frontend\controllers;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

use common\models\goods\Product;
use common\models\firms\Firm;
use common\models\goods\search\Product as ProductSearch;
use common\models\firms\search\Firm as FirmSearch;

use frontend\forms\Search;

/**
 * Class FirmController
 * @todo та же херня что и CategoryController, после запуска порефакторить.
 * @package frontend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class FirmController extends BaseController {

    /** Количество организаций на 1 страницу. */
    const PER_PAGE_FIRMS_LIMIT = 13;

    /** Количество организаций в строке (вывод похожих/моих) */
    const COUNT_IN_ROW = 2;

    public function beforeAction($action) {
        $this->getBreadcrumbs()
            ->addBreadcrumbsLink(
                \Yii::$app->urlManager->createUrl(['/firm']),
                'Организации'
            );
        return parent::beforeAction($action);
    }

    /**
     * Поиск товаров у организаций.
     * @return string
     */
    public function actionIndex() {

        $this->seo->title = 'Поиск товаров у организаций';
        $this->registerCommonBundle();

        $data = \Yii::$app->request->get();
        $search_form = $this->getSearchForm();
        $search_form->setFormData($data);

        $query = ArrayHelper::getValue($data, 'query', '');

        $first_firm = null;
        $firm_list = [];
        $pagination = null;
        $firms_count = 0;
        $product_count = 0;

        // поиск организаций (с товарами из запроса.)
        if (!empty($query)) {

            $firm_filter = (new FirmSearch())
                ->setQueryString($query)
                ->setChunkPartSize(self::COUNT_IN_ROW)
                ->setPerPageCount(self::PER_PAGE_FIRMS_LIMIT)
                ->setWithFirstFirm()
            ;

            $firm_list = $firm_filter->findFirmsByProduct();
            $first_firm = $firm_filter->getFirstFirm();
            $firms_count = $firm_filter->getCount();
            $pagination = $firm_filter->getPagination();

            // количество найденных товаров.
            $product_count = (new ProductSearch())
                ->setFilterTitle($query)
                ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE)
                ->count();
        }

        $this->setSearchFormInfo($product_count, $firms_count);

        return $this->render('index', [
            'search_form'   => $search_form->render(),
            'first_firm'    => $first_firm,
            'firm_list'     => $firm_list,
            'pagination'    => $pagination,
        ]);
    }

    /**
     * Страница организации.
     * @return string
     * @throws Exception
     */
    public function actionShow() {

        $product_id = intval(\Yii::$app->request->get('product_id', 0));
        $firm_id = intval(\Yii::$app->request->get('id', 0));
        /** @var Firm $firm */
        $firm = Firm::findOne($firm_id);

        if (!$firm) {
            throw new Exception('Страница не найдена');
        }

        $this->seo->title = 'Моя страница';

        $this->getBreadcrumbs()
            ->addBreadcrumbsLink(
                \Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm_id]),
                $firm->getTitle()
            );
        $this->registerScriptBundle();

        // количество моих товаров.
        $product_search = new ProductSearch();
        $product_search->setFilterFirmId($firm->id);
        $product_search->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE);
        $product_count = (int)$product_search->count();

        $referrer = null;
        if($product_id) {
            $referrer = \Yii::$app->urlManager->createUrl(['product/show', 'id' => $product_id]);
        }

        $firm_product_categories = [];

        if($product_count > 0) {
            $firm_product_categories[] = [
                Firm::FIELD_PRODUCT_COUNT => $product_count,
                Firm::FIELD_CATEGORY_ID => null,
                Firm::FIELD_CATEGORY_TITLE => 'Все',
            ];
        }

        $firm_product_categories =  array_merge($firm_product_categories, $firm->getProductCategoriesData());

        return $this->render('show', [
            'firm'              => $firm,
            // сферы деятельности.
            'firm_categories'   => $firm->getCategories()->all(),
            // категории товаров.
            'firm_product_categories' => $firm_product_categories,
            'referrer'          => $referrer,
            'product_count'     => $product_count,
        ]);
    }

    /**
     * @return Search
     */
    public function getSearchForm(): Search {
        $search_form = parent::getSearchForm();
        $search_form->setTemplatePath(\Yii::getAlias('@frontend/views/product'));
        $search_form->getSubmitControl()->addClass('btn-outline');
        return $search_form;
    }
}