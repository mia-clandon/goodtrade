<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\web\UploadedFile;

use common\models\firms\Firm;
use common\libs\Storage;

/**
 * This is the model class for table "price_queue".
 *
 * @property integer $id
 * @property UploadedFile $price_file
 * @property string $file
 * @property string $file_name
 * @property integer $status
 * @property integer $firm_id
 * @property integer $created_at
 * @property integer $updated_at
 * @author Артём Широких kowapssupport@gmail.com
 */
class PriceQueue extends Base {

    const STATUS_NEW = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_PROCESSED = 2;

    public $price_file;

    public static function tableName() {
        return 'price_queue';
    }

    public function getStatusNames() {
        return [
            self::STATUS_NEW        => 'Только загружен',
            self::STATUS_PROCESSING => 'В обработке',
            self::STATUS_PROCESSED  => 'Обработан',
        ];
    }

    /**
     * @param string $status
     * @return null|string
     */
    public function getStatusName($status = null): ?string {
        $status = is_null($status) ? $this->status : $status;
        if (array_key_exists($status, $this->getStatusNames())) {
            $statuses = $this->getStatusNames();
            return $statuses[$status];
        }
        return null;
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public static function getMine(): ActiveQuery {
        return static::find()->where(['firm_id' => Firm::get()->id]);
    }

    /**
     * @return string
     */
    public function getFileName(): string {
        return $this->file_name;
    }

    /**
     * @return string
     */
    public function getFIleNameWithExtension(): string {
        return $this->getFileName(). '.'. $this->getExtension();
    }

    /**
     * @return string
     */
    public function getExtension(): string {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

    /**
     * @return bool
     */
    public function isNewStatus(): bool {
        return (int)$this->status === self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isProcessingStatus(): bool {
        return (int)$this->status === self::STATUS_PROCESSING;
    }

    /**
     * @return bool
     */
    public function isProcessedStatus(): bool {
        return (int)$this->status === self::STATUS_PROCESSED;
    }

    public function rules() {
        return [
            [['firm_id'], 'required'],
            [['status', 'firm_id', 'created_at', 'updated_at'], 'integer'],
            [['price_file'], 'file', 'skipOnEmpty' => false, 'extensions' => ['xls', 'xlsx', 'csv'], 'checkExtensionByMimeType' => false,],
            [['status', 'firm_id'], 'unique', 'targetAttribute' => ['firm_id'], 'message' => 'Прайс лист уже в обработке.'],
        ];
    }

    public function attributeLabels() {
        return [
            'id'            => '#',
            'file'          => 'Путь к прайс листу',
            'file_name'     => 'Название файла',
            'status'        => 'Статус',
            'firm_id'       => 'Id организации',
            'created_at'    => 'Created At',
            'updated_at'    => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirm(): ActiveQuery {
        return $this->hasOne(Firm::class, ['id' => 'firm_id']);
    }

    /**
     * @return bool
     */
    public function isFileExist(): bool {
        return file_exists(Storage::getRealFilePath($this->file));
    }

    /**
     * @return string
     */
    public function getRealFilePath(): string {
        return Storage::getRealFilePath($this->file);
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = self::find();
        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $data_provider;
        }
        return $data_provider;
    }

    public function beforeSave($insert) {
        if ($insert) {
            $this->status = self::STATUS_NEW;
        }
        return parent::beforeSave($insert);
    }

    /**
     * Метод загружает файл прайс листа в хранилище.
     * @return bool
     */
    public function upload(): bool {
        if (!$this->validate()) {
            return false;
        }
        $path = Storage::i()->generatePath('product-price', $this->price_file->extension);
        if (Storage::i()->move($this->price_file->tempName, $path)) {
            $this->file = Storage::i()->getRelativePath($path);
            $this->file_name = $this->price_file->baseName;
        }
        return true;
    }
}