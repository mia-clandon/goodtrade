<?php

namespace backend\controllers;

use backend\assets\AppAsset;
use backend\components\CatalogProcessor;
use backend\forms\category\OkedRelation as OkedRelationForm;
use backend\forms\category\Update;
use backend\forms\category\VocabularyRelation as VocabularyRelationForm;
use common\libs\StringHelper;
use common\models\Category;
use common\models\CategoryImage;
use common\models\CategoryOked;
use common\models\CategoryRelation;
use common\models\CategoryVocabulary;
use common\models\CategoryVocabularyPosition;
use common\models\goods\Product;
use common\models\goods\search\Product as ProductSearch;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Class CategoryController
 * Контроллер для работы с категориями.
 * TODO: управление разделами категорий CategoryController -> нужно вынести в отдельный модуль
 * TODO: который будет содержать в себе контроллеры для работы со связями / характеристиками / окэдами.
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class CategoryController extends BaseController
{

    /** включить ajax версию каталога ? */
    const ENABLE_AJAX_VERSION = false;

    /** @var null|VocabularyRelationForm */
    private $vocabulary_relation_form;

    /** @var null|OkedRelationForm */
    private $oked_relation_form;

    /**
     * Список сфер деятельности.
     * @return string
     */
    public function actionIndex()
    {

        // загрузка ресурсов AJAX формы (связи с характеристиками).
        $this->getVocabularyRelationForm()
            ->loadResources();

        \Yii::$app->getView()->registerJsFile('/js/category/dist/index.js', [
            'depends' => AppAsset::class,
        ]);
        \Yii::$app->getView()->registerJsFile('/js/category/dist/oked.js', [
            'depends' => AppAsset::class,
        ]);

        $current_activity_id = \Yii::$app->request->get('activity_id');
        $current_activity_id = $current_activity_id !== null ? (int)$current_activity_id : null;

        $activities = [];
        if ($current_activity_id === null) {
            /** @var Category[] $activities */
            $activities = Category::find()->where(['parent' => 0])->all();
        }

        $current_activity = null;
        /** @var Category $current_activity */
        if ($current_activity_id !== null) {
            $current_activity = Category::findOne($current_activity_id);
        }

        /** @var Category[] $category_list */
        $category_list = [];
        if (null !== $current_activity_id && self::ENABLE_AJAX_VERSION) {
            $category_list = (new Category())->getChildListByParent($current_activity_id);
        }

        $catalog_html = '';
        if (null !== $current_activity_id && !self::ENABLE_AJAX_VERSION) {
            $catalog_html = (new CatalogProcessor())
                ->setParentId($current_activity_id)
                ->enableCache()
                ->render();
        }

        $last_updated_date = \Yii::$app->formatter->asDatetime(
            Category::find()->max('created_at')
        );

        return $this->render('index', [
            'activities' => $activities, // все сферы деятельности.
            'current_activity' => $current_activity, // текущая сфера деятельности.
            'current_activity_id' => $current_activity_id, // текущая сфера деятельности - id.
            'catalog_is_ajax_version' => self::ENABLE_AJAX_VERSION,
            'category_list' => $category_list, // список категорий сферы деятельности. (AJAX вариант)
            'catalog_html' => $catalog_html,// список категорий сферы деятельности. (не AJAX вариант)
            'last_updated_date' => $last_updated_date,
        ]);
    }

    /**
     * Загрузка дочерних категорий.
     * @return string
     * @throws Exception
     */
    public function actionLoadChildCategories()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Page not found', 404);
        }
        $parent_id = (int)\Yii::$app->request->post('parent_id');
        $category_list = (new Category())->getChildListByParent($parent_id);
        return $this->render('parts/categories', [
            'category_list' => $category_list,
        ]);
    }

    /**
     * Обновление каталога (отдаёт хтмл код каталога).
     * @return string
     * @throws Exception
     */
    public function actionUpdateCatalog()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Page not found', 404);
        }
        $parent_id = \Yii::$app->request->post('parent_id');
        CatalogProcessor::clearCache();
        return (new CatalogProcessor())
            ->setParentId($parent_id)
            ->enableCache()
            ->render();
    }

    /**
     * Экземпляр формы связи характеристик с категорией.
     * @return VocabularyRelationForm
     */
    private function getVocabularyRelationForm()
    {
        if (null === $this->vocabulary_relation_form) {
            $this->vocabulary_relation_form = (new VocabularyRelationForm())
                ->setAjaxMode()
                ->setPostMethod()
                ->setAction(Url::to(['/category/vocabulary-relation']));
        }
        return $this->vocabulary_relation_form;
    }

    /**
     * Экземпляр формы связи ОКЭД с категорией.
     * @return OkedRelationForm
     */
    private function getOkedRelationForm()
    {
        if (null === $this->oked_relation_form) {
            $this->oked_relation_form = (new OkedRelationForm())
                ->setAjaxMode()
                ->setModel(new CategoryOked())
                ->setPostMethod()
                ->setAction(Url::to(['/category/oked-relation']));
        }
        return $this->oked_relation_form;
    }

    /**
     * Связь характеристик с категорией (форма, сохранение).
     * @return string|array
     * @throws Exception
     */
    public function actionVocabularyRelation()
    {
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Page not found', 404);
        }

        $this->layout = false;
        $form = $this->getVocabularyRelationForm();

        // отдаю форму.
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isGet) {
            // известна характеристика.
            $vocabulary_id = \Yii::$app->request->get('vocabulary_id');
            $category_id = \Yii::$app->request->get('category_id');
            $for_table_mode = \Yii::$app->request->get('for_table_mode');

            if (null !== $vocabulary_id) {
                $form->setVocabularyId($vocabulary_id);
            }
            if (null !== $category_id) {
                $form->setCategoryId($category_id);
            }
            if (null !== $for_table_mode && (bool)$for_table_mode) {
                $form->setForTableMode();
            }
            return $form->render();
        }

        // сохранение связи.
        if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $form->setFormData(\Yii::$app->request->post());
            return $form->ajaxValidateAndSave();
        }
        return '';
    }

    /**
     * Вкладка "Окэд'ы".
     * @return string
     * @throws \Exception
     */
    public function actionOked()
    {
        $model = $this->getCategoryModel();
        if ($model->isNewRecord) {
            throw new \Exception('Page not found', 404);
        }
        \Yii::$app->getView()->registerJsFile('/js/category/dist/oked.js', [
            'depends' => AppAsset::class,
        ]);
        $form = $this->getOkedRelationForm()
            ->setCategoryModel($model);
        return $this->render('oked', [
            'category' => $model,
            'form' => $form->render(),
        ]);
    }

    /**
     * Связь ОКЕД с категорией (форма, сохранение).
     * @return string|array
     * @throws Exception
     */
    public function actionOkedRelation()
    {

        $this->layout = false;
        $form = $this->getOkedRelationForm();

        if (!(\Yii::$app->request->isAjax && \Yii::$app->request->isGet)) {
            throw new Exception('Page not found', 404);
        }
        // отдаю форму.
        $category = $this->getCategoryModel('category_id');
        $form->setCategoryModel($category);
        $form->setTitle($category->title);
        return $form->render();
    }

    /**
     * @throws Exception
     */
    public function actionGetCategoryOkedList()
    {
        $this->layout = false;
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $category_id = \Yii::$app->request->post('category_id', 0);
            if (!$category_id) {
                throw new Exception('Category not found');
            }
            /** @var Category $category */
            $category = Category::findOne($category_id);
            if (!$category) {
                throw new Exception('Category not found');
            }
            $oked_list = $category->getOked()->select(['key', 'name'])->asArray()->all();
            return $oked_list;
        }
        throw new Exception(404, 'Страница не найдена.');
    }

    /**
     * Action привязывает ОКЕД'ы к выбранной категории.
     * @return array
     * @throws Exception
     */
    public function actionDoOkedRelation()
    {
        $this->layout = false;
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $category_id = \Yii::$app->request->post('category_id', 0);
            $items = \Yii::$app->request->post('items', []);
            if (!$category_id) {
                throw new Exception('Category not found');
            }
            $category = Category::findOne($category_id);
            if (!$category) {
                throw new Exception('Category not found');
            }
            $category->setCategoryOked($items, false);
            return ['message' => 'success'];
        }
        throw new Exception(404, 'Страница не найдена.');
    }

    /**
     * Удаление привязки ОКЕД от категории.
     * @return array
     * @throws Exception
     */
    public function actionRemoveOked()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $category_id = \Yii::$app->request->post('category_id', 0);
        if (!$category_id) {
            throw new Exception('Category not found');
        }
        $oked = \Yii::$app->request->post('oked', 0);
        if (!$oked) {
            throw new Exception('Oked not sets');
        }
        $category = Category::findOne($category_id);
        if (!$category) {
            throw new Exception('Category not found');
        }
        CategoryOked::deleteAll(['category_id' => $category_id, 'oked' => $oked]);
        return ['message' => 'success'];
    }

    /**
     * Action делает связь-дубликаты категорий.
     * @return array
     * @throws Exception
     */
    public function actionDoRelation()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $category_id = \Yii::$app->request->post('category_id', 0);
        $related_category_id = \Yii::$app->request->post('related_category_id', 0);

        if (!$category_id || !$related_category_id) {
            throw new Exception('Category not found');
        }

        /** @var Category|null $category */
        $category = Category::findOne($category_id);
        /** @var Category|null $related_category */
        $related_category = Category::findOne($related_category_id);

        if ($category === 0 || null === $related_category) {
            throw new Exception('Category not found');
        }

        $relation_one = new CategoryRelation();
        $relation_one->setAttributes([
            'category_id' => $category_id,
            'related_category_id' => $related_category_id,
        ]);

        $relationTwo = new CategoryRelation();
        $relationTwo->setAttributes([
            'category_id' => $related_category_id,
            'related_category_id' => $category_id,
        ]);
        return ['message' => ($relation_one->save() && $relationTwo->save()) ? 'success' : 'error'];
    }

    /**
     * Action делает дубликаты категорий.
     * @return array
     * @throws Exception
     */
    public function actionDoDuplicate()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $category_id = \Yii::$app->request->post('category_id', 0);
        $parent_category_id = \Yii::$app->request->post('parent_category_id', 0);

        if (!$category_id || !$parent_category_id) {
            throw new Exception('Category not found');
        }

        /** @var Category|null $category */
        $category = Category::findOne($category_id);
        /** @var Category|null $parent_category */
        $parent_category = Category::findOne($parent_category_id);

        if ($category === null || null === $parent_category) {
            throw new Exception('Category not found');
        }

        $new_category = new Category();
        $new_category->parent = $parent_category->id;
        $new_category->title = $category->title;

        if (!$new_category->save()) {
            return ['message' => 'error'];
        }

        $relation_one = new CategoryRelation();
        $relation_one->setAttributes([
            'category_id' => $new_category->id,
            'related_category_id' => $category->id,
        ]);

        $relation_two = new CategoryRelation();
        $relation_two->setAttributes([
            'category_id' => $category->id,
            'related_category_id' => $new_category->id,
        ]);
        return ['message' => ($relation_one->save() && $relation_two->save()) ? 'success' : 'error'];
    }

    /**
     * Action создает новую подкатегорию.
     * @return array
     * @throws Exception
     */
    public function actionDoNewCategory()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $category_title = \Yii::$app->request->post('category_title', 0);
        $parent_category_id = \Yii::$app->request->post('parent_category_id', 0);

        if (empty($category_title) || !$parent_category_id) {
            throw new Exception('Params not set');
        }
        /** @var Category|null $parent_category */
        $parent_category = Category::findOne($parent_category_id);
        if (null === $parent_category) {
            throw new Exception('Category not found');
        }

        $new_category = new Category();
        $new_category->parent = $parent_category->id;
        $new_category->title = $category_title;

        return ['message' => $new_category->save() ? 'success' : 'error'];
    }

    /**
     * Action удаляет дубликаты категорий.
     * @return array
     * @throws Exception
     */
    public function actionRemoveRelation()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $category_id = \Yii::$app->request->post('category_id', 0);
        $related_category_id = \Yii::$app->request->post('related_category_id', 0);

        if ($category_id === 0 || $related_category_id === 0) {
            throw new Exception('Category not found');
        }
        $resultOne = CategoryRelation::deleteAll([
            'category_id' => $category_id,
            'related_category_id' => $related_category_id,
        ]);

        $resultTwo = CategoryRelation::deleteAll([
            'category_id' => $related_category_id,
            'related_category_id' => $category_id,
        ]);
        return ['message' => ($resultOne && $resultTwo) ? 'success' : 'error'];
    }

    /**
     * Удаление связи характеристики с категорией.
     * @return array
     * @throws Exception
     */
    public function actionRemoveVocabularyCategory()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $category_id = \Yii::$app->request->post('category_id', 0);
        if ($category_id === 0) {
            throw new Exception('Category not found');
        }
        $vocabulary_id = \Yii::$app->request->post('vocabulary_id', 0);
        if ($vocabulary_id === 0) {
            throw new Exception('Vocabulary not found');
        }
        $result = CategoryVocabulary::removeRelation($category_id, $vocabulary_id);
        return ['success' => $result];
    }

    /**
     * Обновление свойств связи. (Использовать в названии товара, использовать значение характеристики.)
     * @return array
     * @throws Exception
     */
    public function actionUpdateCategoryVocabularyProperty()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $category_id = \Yii::$app->request->post('category_id', 0);
        if ($category_id === 0) {
            throw new Exception('Category not found');
        }
        $vocabulary_id = \Yii::$app->request->post('vocabulary_id', 0);
        if ($vocabulary_id === 0) {
            throw new Exception('Vocabulary not found');
        }
        $property = \Yii::$app->request->post('property');
        $flag = (bool)\Yii::$app->request->post('flag');
        /** @var bool $result */
        $result = CategoryVocabulary::updatePropertyFlags($category_id, $vocabulary_id, $property, $flag);
        $this->updateProductNames([$category_id]);
        return ['success' => $result];
    }

    /**
     * Редактирование / добавление категории.
     * @return string
     * @throws Exception
     */
    public function actionUpdate()
    {
        $model = $this->getCategoryModel();
        $parent_id = \Yii::$app->request->get('parent_id');

        $form = (new Update())
            ->setModel($model)
            ->setPostMethod()
            ->setParentId($parent_id);

        if ($data = \Yii::$app->request->post()) {
            $form->setFormData($data);
            $form->validate();

            if ($form->isValid() && $form->save()) {
                if ($model->isNewRecord) {
                    $this->refresh();
                } else {
                    $this->redirect(['category/update', 'id' => $model->id]);
                }
            } else {
                \Yii::$app->getSession()->setFlash(BaseController::FLASH_MESSAGE_ERROR, $model->stringifyErrors($form->getFormErrors()));
            }
        }
        return $this->render('update', [
            'model' => $model,
            'form' => $form->render(),
        ]);
    }

    /**
     * Характеристики категории.
     * @return string
     * @throws \Exception
     */
    public function actionVocabulary()
    {
        $model = $this->getCategoryModel();
        if ($model->isNewRecord) {
            throw new \Exception('Page not found', 404);
        }
        // загрузка ресурсов AJAX формы (связи с характеристиками).
        $this->getVocabularyRelationForm()
            ->loadResources();
        \Yii::$app->getView()->registerJsFile('/js/category/dist/index.js', [
            'depends' => AppAsset::class,
        ]);
        \Yii::$app->getView()->registerJsFile('/js/category/dist/VocabularyRelation.js', [
            'depends' => AppAsset::class,
        ]);
        /** @var array $vocabularies - список характеристик категории. */
        $vocabularies = CategoryVocabulary::getVocabularyDataByCategory($model->id, true);

        return $this->render('vocabulary', [
            'category' => $model,
            'vocabularies' => $vocabularies,
        ]);
    }

    /**
     * Обновление позиций характеристик категории.
     * @return array
     * @throws Exception
     */
    public function actionUpdateCategoryVocabularyPositions()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $position_data = \Yii::$app->request->post('positions', []);
        $category_id_column = ArrayHelper::getColumn($position_data, 'category_id');
        if (count(array_unique($category_id_column)) > 1) {
            return ['success' => false, 'error' => 'Пришли различные категории, сортировка не возможна.'];
        }
        $result = CategoryVocabularyPosition::updatePositions($position_data);
        if ($result === false) {
            return ['success' => false, 'error' => 'Обновление позиций произошло с ошибкой, сортировка не возможна.'];
        }
        $this->updateProductNames($category_id_column);
        return ['success' => true];
    }

    /**
     * Обновляет названия товаров настройки характеристик которые менялись.
     * @param array $category_id_list
     */
    private function updateProductNames(array $category_id_list)
    {
        $product_search = (new ProductSearch())
            ->setFilterCategories($category_id_list);
        /** @var Product[] $product_list */
        $product_list = $product_search->get()->all();
        foreach ($product_list as $product) {
            $product->getTitleGeneratorHelper()->updateProductTitle();
        }
    }

    /**
     * Дубликаты категории.
     * @return string
     */
    public function actionRelation()
    {
        $model = $this->getCategoryModel();

        \Yii::$app->getView()->registerJsFile('/js/category/relation.js', [
            'depends' => AppAsset::class,
        ]);

        $category_list = $model->getCategoryRelatedQuery()->select(['id', 'title'])->all();

        $category_breadcrumb = [];
        foreach ($category_list as $category) {
            /** @var Category $category_one */
            $category_one = Category::findOne($category['id']);
            $category_parents = [];
            $model->getAllParents($category_one, $category_parents);

            $breadcrumb = [];
            foreach ($category_parents as $parent) {
                $breadcrumb[] = $parent->title;

            }
            $category_breadcrumb[$category['id']] = implode('<i class="fa fa-long-arrow-right" aria-hidden="true"></i>',
                array_reverse($breadcrumb)
            );
        }

        return $this->render('relation', [
            'category_list' => $category_list,
            'category' => $model,
            'category_breadcrumb' => $category_breadcrumb,
        ]);
    }

    /**
     * Подгружает каталог для создания связей.
     * @return string
     * @throws Exception
     */
    public function actionGetRelationCategoryList()
    {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new Exception(404, 'Страница не найдена.');
        }

        $parent_id = (int)\Yii::$app->request->post('parent_id', 0);
        $model = $this->getCategoryModel();
        /** @var Category|null $parent_category */
        $parent_category = Category::findOne($parent_id);

        $parent_parent_id = 0;

        if ($parent_category !== null) {
            /** @var Category|null $parent_parent */
            $parent_parent = $parent_category->getParent()->one();
            if ($parent_category && $parent_parent !== null) {
                $parent_parent_id = $parent_parent->id;
            }
        }

        $category_list = $model->getFirstChildCategoryList($parent_id);

        return $this->render('parts/category_activity', [
            'category_list' => $category_list,
            'parent_id' => $parent_id,
            'parent' => $parent_category,
            'parent_parent_id' => $parent_parent_id,
        ]);
    }

    /**
     * @param string $request_key
     * @return Category
     * @throws Exception
     */
    private function getCategoryModel($request_key = 'id')
    {
        $id = (int)\Yii::$app->request->get($request_key, 0);
        /**
         * @var $model Category
         */
        if ($id) {
            $model = Category::findOne($id);
            if (!$model) {
                throw new Exception('Категория отсутствует.');
            }
        } else {
            $model = new Category();
        }
        return $model;
    }

    /**
     * @throws Exception
     */
    public function actionDelete()
    {
        $id = (int)\Yii::$app->request->get('id', 0);
        /**
         * @var $model Category
         */
        $model = Category::findOne($id);
        if (!$model) {
            throw new Exception('Category not found.');
        }
        if ($model->hasChild()) {
            $this->errorMessage('У данной категории есть вложенные, удалите их.');
            return $this->goBack();
        }
        if ($model->isUseInFirm()) {
            $this->errorMessage('К данной категории привязана организация, смените категорию у организации и попробуйте еще раз.');
            return $this->goBack();
        }
        if ($model->isUseInProduct()) {
            $this->errorMessage('К данной категории привязан(ы) товары, смените категорию у товара(ов) и попробуйте еще раз.');
            return $this->goBack();
        }
        if ($model->delete()) {
            $this->successMessage('Категория успешно удалена.');
        } else {
            $this->errorMessage('Произошла ошибка при удалении категории, попробуйте позже !');
        }
        return $this->goBack();
    }

    /**
     * Удаление фотографии категории.
     * @throws \yii\db\Exception
     */
    public function actionRemoveImage()
    {
        $this->layout = false;
        $id = \Yii::$app->request->post('image_id');
        if (!$id) {
            throw new \yii\db\Exception('Page not found');
        }
        /** @var CategoryImage $model */
        $model = CategoryImage::findOne($id);
        if (!$model) {
            throw new \yii\db\Exception('Page not found');
        }
        if ($model->clearImage()) {
            $model->delete();
        }
    }

    /**
     * Экспортирование каталога в EXCEL.
     * todo: в будущем сделать в отдельном классе.
     */
    public function actionExport()
    {
        $this->layout = false;
        $php_excel = new \PHPExcel();

        /** @var Category[] $activities */
        $activities = Category::findAll(['parent' => 0]);
        foreach ($activities as $sheet_index => $activity) {
            $categories = $activity->getChildCategoryList($activity->id, 'title ASC')[Category::CHILD_KEY_PROPERTY] ?? [];

            $sheet = $php_excel->createSheet($sheet_index);
            //Длина строки названия листа не должна привышать 31 символ.
            $sheet->setTitle(StringHelper::cutString($activity->title, 30));

            $row_index = 1;
            /** @var array $category */
            foreach ($categories as $category) {
                $this->writeChildRow($sheet, 0, $row_index, $category);
                $row_index = $sheet->getHighestRow() + 1;
            }
        }

        $php_excel->setActiveSheetIndex(0);
        $writer = \PHPExcel_IOFactory::createWriter($php_excel, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        $now = date('d.m.Y H:i:s', time());
        header('Content-Disposition: attachment;filename="catalog_' . $now . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    /**
     * @param \PHPExcel_Worksheet $sheet
     * @param int $col_index
     * @param int $row_index
     * @param array $category
     */
    private function writeChildRow(\PHPExcel_Worksheet $sheet, int $col_index, int $row_index, array $category)
    {

        $sheet->setCellValueByColumnAndRow($col_index, $row_index, $category[Category::PROPERTY_KEY_TITLE]);
        $sheet->getColumnDimensionByColumn($col_index)->setAutoSize(true);

        $child_category_list = $category[Category::CHILD_KEY_PROPERTY] ?? [];
        if (!empty($child_category_list)) {
            $col_index++;
            foreach ($child_category_list as $child_category) {
                $row_index = $sheet->getHighestRow() + 1;
                $this->writeChildRow($sheet, $col_index, $row_index, $child_category);
            }
        }
    }

    public function goBack($default_url = null)
    {
        $referrer = \Yii::$app->request->referrer;
        return $this->redirect($referrer ? $referrer : ['category/index']);
    }
}