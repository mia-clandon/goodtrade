<?php

namespace backend\components\form\controls;

use yii\base\Exception;
use yii\helpers\Json;

use common\libs\form\Form;
use common\libs\traits\RegisterJsScript;

use common\assets\SelectizeAsset;

/**
 * Class Selectize
 * @package backend\components\bootstrap\form\components
 * @author Артём Широких kowapssupport@gmail.com
 */
class SearchSelect extends Select {
    use RegisterJsScript;

    /** @var  string */
    protected $url;
    /** @var string - value option, поле в Response */
    protected $value_field = 'id';
    /** @var string - text option, поле в Response */
    protected $label_field = null;
    /** @var string - поле для поиска в option's */
    protected $search_field = null;
    /** @var string  */
    protected $request_method = Form::METHOD_GET;
    /** @var string Отсылаемое поле на сервер. */
    protected $query_field = 'q';
    /** @var array Отсылаемые данные на сервер. */
    protected $query_data = [];

    protected $template_name = 'select';

    /**
     * Необходимые скрипты для работы selectize.
     */
    protected function registerFormAssets() {
        SelectizeAsset::register(\Yii::$app->getView());
    }

    /**
     * Регистрация скриптов
     * @return string
     * @throws Exception
     */
    public function render(): string {
        if (is_null($this->url)) {
            throw new Exception('Url must be set.');
        }
        $this->prepareAttributes();
        $this->addClass('search-select-'.$this->getName());
        $this->registerOnlyThisClassScript();
        return parent::render();
    }

    private function prepareAttributes() {

        $this->addDataAttribute('url', $this->url);
        $this->addDataAttribute('request-method', $this->request_method);
        $this->addDataAttribute('query-field', $this->query_field);
        $this->addDataAttribute('query-data', Json::encode($this->query_data));

        if ($this->value_field) {
            $this->addDataAttribute('value-field', $this->value_field);
        }
        if ($this->label_field) {
            $this->addDataAttribute('label-field', $this->label_field);
        }
        if ($this->search_field) {
            $this->addDataAttribute('search-field', $this->search_field);
        }
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $value_field
     * @return $this
     */
    public function setValueField($value_field) {
        $this->value_field = (string)$value_field;
        return $this;
    }

    /**
     * @param string $label_field
     * @return $this
     */
    public function setLabelField($label_field) {
        $this->label_field = (string)$label_field;
        return $this;
    }

    /**
     * @param string $search_field
     * @return $this
     */
    public function setSearchField($search_field) {
        $this->search_field = (string)$search_field;
        return $this;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setQueryField($query) {
        $this->query_field = (string)$query;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setQueryData(array $data) {
        $this->query_data = (array)$data;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setRequestMethod($method) {
        if (in_array($method, [Form::METHOD_GET, Form::METHOD_POST])) {
            $this->request_method = $method;
        }
        return $this;
    }
}