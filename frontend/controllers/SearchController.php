<?php

namespace frontend\controllers;

use common\libs\SearchHelper;
use common\models\Category;
use common\models\goods\Categories;
use common\models\goods\Product;
use common\models\goods\search\Product as ProductSearch;
use frontend\components\form\controls\b2b\{CapacityRange, PriceRange};
use frontend\forms\Search;
use frontend\forms\search\Filter;
use Yii;
use yii\data\Pagination;
use yii\helpers\{ArrayHelper, Url};
use yii\web\Response;

/**
 * Class SearchController
 * todo: разнести index action по методам.
 * todo: вывод уточнения категории.
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class SearchController extends BaseController
{

    /** @var null|Filter */
    private $filter_form;

    /** @var SearchHelper|null */
    private $search_helper;

    /** Количество товаров на 1 страницу. */
    const PER_PAGE_PRODUCT_LIMIT = 20;

    /**
     * Страница поиска по каталогу.
     * @return string|array
     */
    public function actionIndex()
    {
        $this->layout = 'b2b';
        $this->registerScriptBundleB2B();

        $data = Yii::$app->request->get();
        // форма поиска.
        $search_form = $this->getSearchForm();
        $search_form->setFormData($data);
        // форма фильтра.
        $filter_form = $this->getFilterForm();
        /** @var SearchHelper $search_helper */
        $search_helper = $this->getSearchHelper();
        $product_search = (new ProductSearch())
            ->setFilterStatus(Product::STATUS_PRODUCT_ACTIVE);

        $product_list = [];
        $product_found_count = 0;
        // категории для уточнения.
        $category_to_be_sure = [];
        // категория найденного товара.
        $category = null;
        $query = $this->getQueryString();

        // обработка get запроса query.
        if (!empty($query)) {

            $get_request_data = Yii::$app->request->get();
            $search_form->setFormData($get_request_data);
            $filter_form->setFormData($get_request_data);

            $product_search->setFilterTitle($query);
            $this->setFilterDataToSearch($filter_form, $product_search);

            // найденные товары.
            $product_found_count = $product_search->count();
            $found = $product_search->get();

            // сортировка
            $sort_data = $search_helper->getSortingData();
            foreach ($sort_data as $property => $direction) {
                $found->orderBy($property . ' ' . $direction);
            }

            $pagination = $this->getPagination($product_found_count);
            $product_list = $found
                ->limit($pagination->getLimit())
                ->offset($pagination->getOffset())
                ->all();

            // ответ на ajax запрос.
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $product_list_rendered = Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/product/parts/new_product_list.php'), [
                    'product_list' => $product_list ?? [],
                ]);
                return [
                    'response' => $product_list_rendered,
                    'need_show_load_button' => $this->needShowLoadMore($product_found_count),
                ];
            }

            # поиск категорий + уточнение конкретной категории.

            // на случай если товары были найдены в разных категориях.
            if ($product_found_count > 0) {
                $found_categories = Categories::find()
                    ->where(['product_id' => $product_search->getFoundIdList()])
                    ->select(['category_id'])
                    ->groupBy('category_id')
                    ->asArray()
                    ->all();
                $found_categories_id = ArrayHelper::getColumn($found_categories, 'category_id', []);
                // если необходимо уточнение категории.
                if (count($found_categories_id) > 1) {
                    $category_to_be_sure = Category::find()
                        ->where(['id' => $found_categories_id])
                        ->asArray()
                        ->all();
                    $category_to_be_sure = $this->prepareCategoriesToBeSure($category_to_be_sure);
                } else if (count($found_categories_id) === 1) {
                    // нет необходимости уточнять категорию.
                    $category = Category::findOne(current($found_categories_id));
                }
            }
        }

        // параметры сортировок и фильтрации в форму фильтра.
        $filter_form->addTemplateVars([
            'additional_filter_params' => $search_helper->renderSearchInputs(true),
        ]);

        $render_params = array_merge([

            'filter_form' => $filter_form->render(),
            'product_list' => $product_list,
            'search_helper' => $search_helper,
            'category_to_be_sure' => $category_to_be_sure,
            'found_count' => $product_found_count,
            'category' => $category,
            'need_show_load_button' => $this->needShowLoadMore($product_found_count),

        ], $search_helper->getTemplateVars());
        return $this->render('index', $render_params);
    }

    /**
     * @param int $total_count
     * @return Pagination
     */
    private function getPagination(int $total_count): Pagination
    {
        $pages = new Pagination(['totalCount' => $total_count]);
        $pages->setPageSize(self::PER_PAGE_PRODUCT_LIMIT);
        $pages->setPage((int)Yii::$app->request->get('page', 0));
        return $pages;
    }

    /**
     * @param int $total_count
     * @return bool
     */
    private function needShowLoadMore(int $total_count): bool
    {
        $current_page = (int)Yii::$app->request->get('page', 0);
        $current_loaded = ($current_page + 1) * self::PER_PAGE_PRODUCT_LIMIT;
        return $current_loaded < $total_count;
    }

    /**
     * @return string
     */
    private function getQueryString(): string
    {
        $query = strip_tags(Yii::$app->request->get('query'));
        return trim(htmlspecialchars($query));
    }

    /**
     * Устанавливает данные фильтра поиска товаров в поисковик товаров.
     * @param Filter $filter_form
     * @param ProductSearch $product_search
     */
    private function setFilterDataToSearch(Filter $filter_form, ProductSearch $product_search): void
    {
        $filter_data = $filter_form->getControlsData();
        $price_data = $filter_data['price'] ?? [];
        $from_price = $price_data[PriceRange::FROM_INPUT] ?? 0;
        $to_price = $price_data[PriceRange::TO_INPUT] ?? 0;
        if ($from_price > 0) {
            $product_search->setFilterPriceRangeFrom($from_price);
        }
        if ($to_price > 0) {
            $product_search->setFilterPriceRangeTo($to_price);
        }
        $price_with_vat = $price_data[PriceRange::WITH_VAT];
        if ($price_with_vat) {
            $product_search->setFilterPriceVat(true);
        }
        // мощности.
        $capacities_from = $filter_data['capacities'][CapacityRange::FROM_INPUT] ?? 0;
        $capacities_to = $filter_data['capacities'][CapacityRange::TO_INPUT] ?? 0;
        if ($capacities_from > 0) {
            $product_search->setFilterCapacitiesFrom($capacities_from);
        }
        if ($capacities_to > 0) {
            $product_search->setFilterCapacitiesTo($capacities_to);
        }
        // условия доставки.
        $delivery_terms = $filter_data['terms'] ?? [];
        if (!empty($delivery_terms)) {
            $product_search->setFilterDeliveryTerms($delivery_terms);
        }
    }

    /**
     * @param Category[] $category_list
     * @return array
     */
    private function prepareCategoriesToBeSure(array $category_list): array
    {
        //todo;
        return [];
    }

    /**
     * @return SearchHelper
     */
    private function getSearchHelper(): SearchHelper
    {
        if (null === $this->search_helper) {
            $this->search_helper = (new SearchHelper())
                ->setPossibleSortProperties([
                    'title' => 'по названию',
                    'price' => 'по цене',
                    'firm_id' => 'по продавцам',
                ])
                ->setRouteUrl('/search/index');
        }
        return $this->search_helper;
    }

    /**
     * @return Filter
     */
    private function getFilterForm(): Filter
    {
        if ($this->filter_form === null) {
            $filter_form = (new Filter())
                ->setTitle('Фильтр по товарам')
                ->setTemplateFileName('filter')
                ->setGetMethod()
                ->setAction(Url::to('/search/index'));
            return $this->filter_form = $filter_form;
        }
        return $this->filter_form;
    }

    /**
     * Поисковая форма.
     * @return Search
     */
    public function getSearchForm(): Search
    {
        $search_form = parent::getSearchForm();
        $search_form->setTemplatePath(Yii::getAlias('@frontend/views/search'));
        $search_form->getProductQueryControl()->setPlaceholder('Поиск...');
        return $search_form;
    }
}