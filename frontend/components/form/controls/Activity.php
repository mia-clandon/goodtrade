<?php

namespace frontend\components\form\controls;

use yii\helpers\Json;

use common\models\firms\Firm;
use common\models\base\Category;
use common\models\Category as CategoryModel;
use common\libs\form\components\Input as BaseInput;

/**
 * Контрол для выбора сфер деятельности, или категорию товара.
 * Class Activity
 * @package frontend\components\form\controls
 * @author Артём Широких kowapssupport@gmail.com
 */
class Activity extends BaseInput {

    /** @var bool  */
    private $product_category_state = false;

    /** @var string  */
    private $label_tip = '';

    /**
     * Получение всех сфер деятельности организации.
     * @return CategoryModel[]
     */
    private function getAllCompanyActivities() {
        /** @var CategoryModel[] $category_list */
        $category_list = CategoryModel::find()->where(['parent' => 0])->all();
        return $category_list;
    }

    /**
     * Давать возможность выбора категорий товара ?
     * @param bool $flag
     * @return $this
     */
    public function setIsProductCategoryState($flag = true) {
        $this->product_category_state = (bool)$flag;
        return $this;
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->setIsMultiple();
    }

    public function getValue() {
        $value = parent::getValue();
        if (!is_array($value)) {
            return [];
        }
        $value = array_filter($value, function($category_id) {
            return trim($category_id) !== '';
        });
        $value = array_map('intval', $value);
        return $value;
    }

    /**
     * Возвращает массив Category[] из getValue();
     * @return Category[]
     */
    private function getSelectedCategories() {
        $categories = $this->getValue();
        return Category::find()->where(['id' => $categories])->all();
    }

    /**
     * @param string $label_tip
     * @return $this
     */
    public function setLabelTip($label_tip) {
        $this->label_tip = (string)$label_tip;
        return $this;
    }

    /**
     * Возвращает id сфер деятельности организации.
     * @return array
     */
    private function getFirmActivitiesIds() {
        return Firm::get()->getCategoryIds();
    }

    /**
     * Возвращает сферы деятельности по которым будут отображаться категории для товара.
     * @return Category[]
     */
    public function getActiveFirmActivities() {
        if (empty($this->getFirmActivitiesIds()) || $this->product_category_state == false) {
            return [];
        }
        return Category::find()->where(['id' => $this->getFirmActivitiesIds()])->all();
    }

    /**
     * Возвращает список категорий по сферам деятельности организации.
     * @return string
     */
    public function getFirmActiveActivitiesHtml() {
        $active_categories = $this->getActiveFirmActivities();
        if (empty($active_categories)) {
            return '';
        }
        return \Yii::$app->getView()->renderFile(\Yii::getAlias('@frontend/modules/api/views/activity/activity-category-list.php'), [
            'categories' => $active_categories,
        ]);
    }

    public function render(): string {
        return parent::renderTemplate([

            'activities' => $this->getAllCompanyActivities(),
            'name' => $this->getName(). '[]',
            'label_tip' => $this->label_tip,

            'classes' => $this->getClassString(),

            // состояние когда по нажатию на сферу деятельности - подгружаются категории сферы деятельности.
            'is_product_category_state' => $this->product_category_state ? 1 : 0,
            // выбранные категории.
            'selected_categories' => $this->getSelectedCategories(),
            // html выбранных сфер деятельностей и категорий (только для product_category_state)
            'selected_activities_html' => $this->getFirmActiveActivitiesHtml(),
            // выбранные id категорий.
            'selected_id' => $this->getValue(),
            'selected_id_json' => Json::encode($this->getValue()),
            // сферы деятельности организации.
            'firm_activities_ids_json' => Json::encode($this->getFirmActivitiesIds()),
        ]);
    }
}