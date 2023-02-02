<?php

namespace backend\controllers;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Response;

use common\libs\StringBuilder;
use common\models\PriceQueue;
use common\libs\Env;
use common\assets\KnockoutAsset;
use common\assets\UploaderAsset;
use common\assets\JBoxAsset;

use backend\assets\ImportAsset;
use backend\forms\import\Add;
use backend\forms\import\Params;
use backend\helpers\ProductImport;
use backend\forms\product\Update;
use backend\components\form\controls\Selectize;
use backend\components\PriceProcessor;

/**
 * Class ImportController
 * @package backend\controllers
 * @author Артём Широких kowapssupport@gmail.com
 */
class ImportController extends BaseController {

    /**
     * Список прайс листов.
     * @return string
     */
    public function actionIndex() {
        $model = new PriceQueue();
        $data_provider = $model->search(\Yii::$app->request->get());
        return $this->render('index', [
            'data_provider' => $data_provider,
        ]);
    }

    /**
     * Страница для рендеринга прайс листа.
     * @return string
     * @throws Exception
     */
    public function actionImport() {
        $id = \Yii::$app->request->get('id', 0);
        if (!$id) {
            throw new Exception('Прайса не существует.');
        }
        $price = PriceQueue::findOne($id);
        if (!$price) {
            throw new Exception('Прайса не существует.');
        }

        $this->hideLeftMenu();
        $this->registerImportScripts();

        $form = (new Params())
            ->setId('import-params-form')
            ->setPriceId($id)
            ->setFirmId($price->firm_id)
            ->setGetMethod()
            ->setTitle('Настройки выгрузки прайс-листа.')
            ->setFormMode(Params::FORM_HORIZONTAL_MODE)
            ->setTemplateFileName('import')
            ->setAction(Url::to(['import/get-excel-table']))
        ;

        if (\Yii::$app->request->get()) {
            $form->setFormData(\Yii::$app->request->get());
        }

        // контрол для выбора категории (с формы редактирования товара).
        $category_control = $this->getCategoryControl();

        return $this->render('import', [
            'form' => $form->render(),
            'category_control' => $category_control->render(),
        ]);
    }

    /**
     * @return \yii\console\Response|Response
     * @throws Exception
     */
    public function actionDownload() {
        $file_not_found = 'Прайса не существует.';
        $id = \Yii::$app->request->get('id', 0);
        if (!$id) {
            throw new Exception($file_not_found);
        }
        $price = PriceQueue::findOne($id);
        if (!$price) {
            throw new Exception($file_not_found);
        }
        if (!$price->isFileExist()) {
            throw new Exception($file_not_found);
        }
        return \Yii::$app->getResponse()->sendFile($price->getRealFilePath(),
            $price->getFIleNameWithExtension()
        );
    }

    /**
     * @throws Exception
     */
    public function actionChangeStatus() {
        $file_not_found = 'Прайса не существует.';
        $id = \Yii::$app->request->get('id', 0);
        if (!$id) {
            throw new Exception($file_not_found);
        }
        $price = PriceQueue::findOne($id);
        if (!$price) {
            throw new Exception($file_not_found);
        }
        $new_status = (int)\Yii::$app->request->get('new_status');
        $price->status = $new_status;
        $price->save(false, ['status']);
        return $this->redirect(['/import']);
    }

    /**
     * Метод (action), сохраняет данные импортированных товаров с прайс листа.
     * @return string
     * @throws Exception
     */
    public function actionSaveProductList() {
        $this->layout = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (\Yii::$app->request->isAjax) {
            ini_set('memory_limit', '2048M');

            $product_list = Json::decode(\Yii::$app->request->post('product_list'));
            $firm_id = \Yii::$app->request->post('firm_id', 0);
            if (!$firm_id) {
                throw new Exception('Идентификатор организации не передан.');
            }

            foreach ($product_list as $product_array) {
                if (!$product_array) continue;

                /** @var string $title - название товара */
                $title = ArrayHelper::getValue($product_array, 'title');
                /** @var float $price - цена товара */
                $price = ArrayHelper::getValue($product_array, 'price');
                /** @var int $category - категория */
                $category = ArrayHelper::getValue($product_array, 'category');
                /** @var array $vocabularies - массив словарей */
                $vocabularies = ArrayHelper::getValue($product_array, 'vocabularies');
                /** @var array $images - массив фотографий */
                $images = ArrayHelper::getValue($product_array, 'images');

                (new PriceProcessor())
                    ->setFirmId($firm_id)
                    ->setTitle($title)
                    ->setPrice($price)
                    ->setCategory($category)
                    ->setVocabularies($vocabularies)
                    ->setImages($images)
                    ->process();
            }
        }
        return ['success' => true];
    }

    /**
     * Метод возвращает HTML код контрола категорий.
     * @return string
     */
    public function actionGetCategoryControl() {
        $this->layout = false;
        if (\Yii::$app->request->isAjax) {
            $category_control = $this->getCategoryControl();
            return $category_control->render();
        }
        return '';
    }

    /**
     * Метод возвращает контрол категорий с формы обновления/добавления товара.
     * т.к нужна логика именно этого контрола.
     * @return Selectize
     */
    private function getCategoryControl() {
        $category_control = (new Update())->getCategoryControl()
            ->setName('category')
            ->setTitle('Выберите категорию для выбранных товаров.');
        return $category_control;
    }

    /**
     * Регистрирует скрипты для страницы обработки прайс листов.
     */
    private function registerImportScripts() {

        JBoxAsset::register($this->getView());

        if (Env::i()->isProd()) {
            // собранные скрипты.
            ImportAsset::register($this->getView());
        }
        else {
            KnockoutAsset::register($this->getView());
            UploaderAsset::register($this->getView());
            $depends_scripts = [
                'backend\assets\AppAsset',
                'common\assets\UploaderAsset',
                'common\assets\KnockoutAsset',
            ];
            \Yii::$app->getView()->registerJsFile('/js/import/Vocabulary.js',   ['depends' => $depends_scripts]);
            \Yii::$app->getView()->registerJsFile('/js/import/Term.js',         ['depends' => $depends_scripts]);
            \Yii::$app->getView()->registerJsFile('/js/import/Image.js',        ['depends' => $depends_scripts]);
            \Yii::$app->getView()->registerJsFile('/js/import/PriceList.js',    ['depends' => $depends_scripts]);
        }
    }

    /**
     * Получение прайс листа JSON.
     * @throws Exception
     */
    public function actionGetExcelTable() {
        if (!\Yii::$app->request->isAjax) {
            throw new Exception('Страница не найдена.');
        }
        $price_id = \Yii::$app->request->post('id', 0);
        if (!$price_id) {
            throw new Exception('Не передан идентификатор прайс-листа.');
        }

        $this->layout = false;
        $data = \Yii::$app->request->post();

        /** @var PriceQueue $price */
        $price = PriceQueue::findOne($price_id);
        if (!$price) {
            throw new Exception('Прайс листа не существует.');
        }

        // Настройки для парсера.
        $col_count_in_row = (int)ArrayHelper::getValue($data, 'col_count_in_row', 0);
        $header_col_index = (int)ArrayHelper::getValue($data, 'header_col_index', 0);
        $title_col_index = (int)ArrayHelper::getValue($data, 'title_col_index', 0);
        $price_col_index = (int)ArrayHelper::getValue($data, 'price_col_index', 0);

        $file_path = (new StringBuilder())
            ->add(\Yii::getAlias('@common/web/'))
            ->add($price->file)
            ->get();

        $import_helper = (new ProductImport())
            ->setFilePath($file_path)
            ->setColsInRowCount($col_count_in_row)
            ->setHeaderRowIndex($header_col_index)
            ->setTitleColIndex($title_col_index)
            ->setPriceColIndex($price_col_index)
            ->parse()
        ;

        if (!Env::i()->isProd()) {
            if (!$header = \Yii::$app->cache->get('import_helper_header')) {
                $header = $import_helper->getHeader();
                \Yii::$app->cache->set('import_helper_header', $header);
            }
            if (!$body = \Yii::$app->cache->get('import_helper_body')) {
                $body = $import_helper->getBody();
                \Yii::$app->cache->set('import_helper_body', $body);
            }
        }
        else {
            $header = $import_helper->getHeader();
            $body = $import_helper->getBody();
        }

        return $this->render('excel-table', [
            'header_row' => $header,
            'body_rows' => $body,
        ]);
    }

    /**
     * Добавление нового прайс листа.
     * @return string|\yii\web\Response
     */
    public function actionAdd() {

        $price_model = new PriceQueue();

        $form = (new Add())
            ->setTitle('Добавление нового прайс листа')
            ->setPostMethod()
            ->setTemplateFileName('add')
            ->setEnctype(Add::ENCTYPE_MULTIPART_FORM_DATA)
            ->setModel($price_model)
        ;

        if ($data = \Yii::$app->request->post()) {
            $form->setFormData($data);
            $form->validate();
            if ($form->isValid() && $form->save()) {
                return $this->redirect(Url::to(['import/index']));
            }
        }

        return $this->render('add', [
            'form' => $form->render(),
        ]);
    }
}