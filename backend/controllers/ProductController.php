<?php

namespace backend\controllers;

use backend\components\ProductVocabularyProcessor;
use backend\forms\product\{Indexer, Place as ProductPlaceForm, ProductVocabulary, Search, Update};
use common\models\goods\{Images, Product};
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Class ProductController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ProductController extends BaseController
{
    public function actionIndex(): string
    {
        $model = new Product();
        $data_provider = $model->search(\Yii::$app->request->get());
        $search_form = (new Search())
            ->addDataAttribute('url', Url::to(['product/index']))
            ->setTitle('Поиск по товарам')
            ->setGetMethod()
            ->setFormMode(Search::FORM_HORIZONTAL_MODE)
            ->setTemplateFileName('index'); //backend/views/product/form/index

        if (\Yii::$app->request->get()) {
            $search_form->setFormData(\Yii::$app->request->get());
        }

        $indexer_form = (new Indexer())
            ->setId('product-indexer-form')
            ->setAction(\Yii::$app->urlManager->createUrl('product/calculate-part-count'))
            ->setTemplateFileName('indexer');
        \Yii::$app->getView()->registerJsFile('/js/product/index.js', ['depends' => ['backend\assets\AppAsset']]);
        return $this->render('index', [
            'model' => $model,
            'data_provider' => $data_provider,
            'search_form' => $search_form->render(),
            'indexer_form' => $indexer_form->render(),
        ]);
    }

    /**
     * Индексация порции товаров.
     * @return array
     * @throws Exception
     */
    public function actionIndexer()
    {
        if (\Yii::$app->request->isAjax) {
            $offset = \Yii::$app->request->post('offset', 0);
            $limit = \Yii::$app->request->post('limit', 0);
            // индексация партии товаров.
            (new Product())->indexPart($offset, $limit);
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => 1];
        } else {
            throw new Exception(404);
        }
    }

    /**
     * Действия над выбранными товарами.
     * @throws Exception
     */
    public function actionActions()
    {
        if (\Yii::$app->request->post()) {
            $this->layout = false;
            $action = \Yii::$app->request->post('action', false);
            $product_ids = \Yii::$app->request->post('selection', []);
            $possible_actions = ['remove'];
            if (in_array($action, $possible_actions)) {
                foreach ($product_ids as $product_id) {
                    /** @var Product $product */
                    $product = Product::findOne($product_id);
                    $product->delete();
                }
                return $this->redirect(Url::to(['product/index']));
            } else {
                return $this->redirect(Url::to(['product/index']));
            }
        }
        throw new Exception('Страница не найдена !', 404);
    }

    /**
     * Обновление товара.
     * @return string
     * @throws Exception
     */
    public function actionUpdate()
    {
        $model = $this->getModel();

        $form = (new Update())
            ->setModel($model)
            ->setPostMethod()
            ->setTemplateFileName('update');

        // ресурсы формы характеристик.
        (new ProductVocabulary())->loadResources();

        // сохранение товара.
        if ($data = \Yii::$app->request->post()) {
            $form->setFormData($data);
            $form->validate();
            if ($form->isValid() && $form->save()) {
                if ($model->isNewRecord) {
                    $this->refresh();
                } else {
                    $this->redirect(['product/update', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'form' => $form->render(),
        ]);
    }

    /**
     * Удаление товара.
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionDelete()
    {
        $model = $this->getModel();
        if ($model->isNewRecord) {
            throw new Exception(404, 'Страница не найдена.');
        }
        $model->delete();
        return $this->redirect(['product/index']);
    }

    /**
     * Метод очищает индекс. (truncate).
     * @return array
     * @throws Exception
     */
    public function actionClearIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $this->layout = false;
            \Yii::$app->response->format = Response::FORMAT_JSON;
            Product::clearIndex();
            return ['success' => 1];
        } else {
            throw new Exception(404);
        }
    }

    /**
     * Таблица с порциями для индексации.
     * @return string
     * @throws Exception
     */
    public function actionCalculatePartCount()
    {
        if (\Yii::$app->request->isAjax) {
            $this->layout = false;
            $part_count = \Yii::$app->request->post('part_count', 10000);
            $data = Product::calculateParts($part_count);
            return $this->renderFile(\Yii::getAlias('@app/views/common/parts-table.php'), [
                'parts' => $data,
                'limit' => $part_count,
            ]);
        } else {
            throw new Exception(404);
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function actionRemoveImage()
    {
        $this->layout = false;
        $id = \Yii::$app->request->post('image_id');
        if (!$id) {
            throw new \yii\db\Exception('Page not found');
        }
        /** @var Images $model */
        $model = Images::findOne($id);
        if (!$model) {
            throw new \yii\db\Exception('Page not found');
        }
        if ($model->clearImage()) {
            $model->delete();
        }
    }

    /**
     * Подгрузка характеристик и их значений.
     * @return string
     * @throws
     */
    public function actionGetVocabularyTermsForm()
    {
        $category_id = \Yii::$app->request->post('category_id');
        if (!$category_id) {
            throw new Exception('Не передана категория.');
        }
        $this->layout = false;
        $product_id = (int)\Yii::$app->request->post('product_id');
        $processor = ProductVocabularyProcessor::i()
            ->setCategoryId($category_id)
            ->setEnvelope(ProductVocabularyProcessor::ENVELOPE_BACKEND);
        if ($product_id > 0) {
            $processor->setProductId($product_id);
        }
        return $processor->renderVocabularyTerms();
    }

    /**
     * Подгрузка мест реализации.
     * @return string
     * @throws
     */
    public function actionGetPlacesForm()
    {
        $this->layout = false;
        $product_id = (int)\Yii::$app->request->post('product_id');
        $product = Product::getByIdCached((int)$product_id);

        if (!$product) {
            throw new Exception('Нет товара!');
        }

        $places = $product->getPlaces()->all();

        return (new ProductPlaceForm())
            ->setPlacesData($places)
            ->render();
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