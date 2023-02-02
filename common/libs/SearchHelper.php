<?php

namespace common\libs;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class SearchHelper
 * @package common\libs
 * @author Артём Широких kowapssupport@gmail.com
 */
class SearchHelper
{

    // параметр сортировки.
    const SORT_PARAM = 'sort';

    // направления сортировок.
    const SORT_ASC_DIRECTION = 'asc';
    const SORT_DESC_DIRECTION = 'desc';

    /** @var string|null */
    private $route_url;

    /** @var array - возможные свойства. */
    private $possible_sort_properties = [];

    /** @var array - возможные фильтра. */
    private $possible_filter_properties = [];

    /**
     * Возвращает возможные ссылки сортировок.
     * @return array
     */
    public function getSortLinks(): array
    {
        $sort_links = [];
        foreach ($this->possible_sort_properties as $property => $name) {
            $sort_links[$name] = [
                'url' => \Yii::$app->urlManager->createUrl($this->getRequestParamsWithSort([
                    self::SORT_PARAM => [
                        $property => $this->getNextSortDirection($property),
                    ],
                ])),
                'current_direction' => $this->getCurrentSortDirection($property),
                'is_active' => $this->isActiveSorting($property),
            ];
        }
        return $sort_links;
    }

    /**
     * Возвожные свойства сортировок.
     * @return array
     */
    public function getPossibleSortProperties(): array
    {
        return $this->possible_sort_properties;
    }

    /**
     * @param array $properties
     * @return $this
     */
    public function setPossibleSortProperties(array $properties)
    {
        $this->possible_sort_properties = $properties;
        return $this;
    }

    /**
     * @param array $properties
     * @return $this
     */
    public function setPossibleFilterProperties(array $properties)
    {
        $this->possible_filter_properties = $properties;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortParamName(): string
    {
        return self::SORT_PARAM;
    }

    /**
     * @param string $route_url
     * @return $this
     */
    public function setRouteUrl(string $route_url)
    {
        $this->route_url = $route_url;
        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getRouteUrl(): string
    {
        if ($this->route_url === null) {
            throw new Exception('Route url not init.');
        }
        return $this->route_url;
    }

    /**
     * @param array $sort_params
     * @return array
     */
    public function getRequestParamsWithSort(array $sort_params = []): array
    {
        $get_request = \Yii::$app->request->get();
        foreach ($sort_params as $param => $value) {
            if (null === $value) {
                // удаляю параметр из get.
                unset($sort_params[$param]);
                if (isset($get_request[$param])) {
                    unset($get_request[$param]);
                }
            }
        }
        return array_merge([$this->getRouteUrl()], $get_request, $sort_params);
    }

    /**
     * Возможные свойства фильтров.
     * @param bool $with_query
     * @return array
     */
    private function getPossibleFilterProperties(bool $with_query = false): array
    {
        $filters = $this->possible_filter_properties;
        if ($with_query) {
            return array_merge($filters, ['query']);
        }
        return $filters;
    }

    /**
     * Все возможные направления сортировок.
     * @return array
     */
    private function getPossibleSortDirections(): array
    {
        return [self::SORT_ASC_DIRECTION, self::SORT_DESC_DIRECTION,];
    }

    /**
     * Возвращает текущее направление сортировки.
     * @param string $sort_property
     * @return string
     */
    private function getCurrentSortDirection(string $sort_property): string
    {
        $default_sorting_direction = self::SORT_DESC_DIRECTION;
        $sort = \Yii::$app->request->get(self::SORT_PARAM);
        if (empty($sort) || !is_array($sort)) {
            return $default_sorting_direction;
        }
        $sort_property = ArrayHelper::getValue($sort, $sort_property, null);
        return $sort_property ?? $default_sorting_direction;
    }

    /**
     * @param string $property
     * @return bool
     */
    public function isActiveFilter(string $property): bool
    {
        $filter = \Yii::$app->request->get($property);
        return !empty($filter);
    }

    /**
     * @param string $filter_name
     * @return string
     */
    public function getLinkWithoutFilter(string $filter_name): string
    {
        return \Yii::$app->urlManager->createUrl(
            $this->getRequestParamsWithSort([
                $filter_name => null,
            ])
        );
    }

    /**
     * @param string $property
     * @return mixed|null
     */
    public function getFilterValue(string $property)
    {
        if ($this->isActiveFilter($property)) {
            return \Yii::$app->request->get($property);
        }
        return null;
    }

    /**
     * Возвращает bool, применена ли сортировка.
     * @param string $sort_property
     * @return bool
     */
    public function isActiveSorting(string $sort_property): bool
    {
        $sort = \Yii::$app->request->get(self::SORT_PARAM);
        if (empty($sort) || !is_array($sort)) {
            return false;
        }
        return isset($sort[$sort_property]);
    }

    /**
     * Возвращает следующее направление для сортировки.
     * @param string $sort_property
     * @return string
     */
    private function getNextSortDirection(string $sort_property): string
    {
        return $this->getCurrentSortDirection($sort_property) === self::SORT_DESC_DIRECTION
            ? self::SORT_ASC_DIRECTION
            : self::SORT_DESC_DIRECTION;
    }

    /**
     * Возвращает массив сортировок.
     * @return array
     */
    public function getSortingData(): array
    {
        // сортировка
        $directions = [];
        $sort_data = \Yii::$app->request->get(self::SORT_PARAM);
        if ($sort_data) {
            $possible_sort_properties = $this->getPossibleSortProperties();
            $possible_sort_directions = $this->getPossibleSortDirections();
            foreach ($sort_data as $property => $direction) {
                if (!array_key_exists($property, $possible_sort_properties)) {
                    continue;
                }
                if (!in_array($direction, $possible_sort_directions)) {
                    continue;
                }
                $directions[$property] = $direction;
            }
        }
        return $directions;
    }

    /**
     * @param bool $with_query
     * @return bool
     */
    public function hasActiveFilters(bool $with_query = false): bool
    {
        foreach ($this->getPossibleFilterProperties($with_query) as $property) {
            if ($this->isActiveFilter($property)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Параметры поиска.
     * @param bool $with_query
     * @return array
     */
    public function getSearchParams(bool $with_query = false): array
    {
        $params = [];
        foreach ($this->getPossibleFilterProperties($with_query) as $property) {
            if ($this->isActiveFilter($property)) {
                $params[$property] = htmlspecialchars($this->getFilterValue($property));
            }
        }
        $sorting = $this->getSortingData();
        foreach ($sorting as $param => $direction) {
            $params[self::SORT_PARAM . '[' . $param . ']'] = $direction;
        }
        return $params;
    }

    /**
     * @param bool $with_query
     * @return string
     */
    public function renderSearchInputs(bool $with_query = false): string
    {
        $inputs = [];
        foreach ($this->getSearchParams($with_query) as $name => $value) {
            $inputs[] = Html::hiddenInput($name, $value);
        }
        return implode('', $inputs);
    }

    public function getTemplateVars(): array
    {
        return [
            // параметры сортировок.
            'sort_links' => $this->getSortLinks(),
            'search_params' => $this->getSearchParams(),
            'has_active_filters' => $this->hasActiveFilters(true),
            'clear_filter_sort_form' => \Yii::$app->urlManager->createUrl([$this->getRouteUrl()]),
        ];
    }
}