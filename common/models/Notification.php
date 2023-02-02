<?php

namespace common\models;

use common\models\firms\Firm;
use common\modules\image\helpers\Image as ImageHelper;
use common\modules\image\helpers\Image;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property integer $from_firm_id
 * @property integer $to_firm_id
 * @property integer $send_time
 * @property integer $type
 * @property string $title
 * @property string $text
 * @property integer $deleted
 * @property integer $deleted_time
 * @property string $extra_data
 * @author Артём Широких kowapssupport@gmail.com
 */
class Notification extends Base {

    const TABLE_NAME = 'notification';

    const NOT_DELETED_STATE = 0;
    const DELETED_STATE = 1;

    public static function tableName() {
        return self::TABLE_NAME;
    }

    public function rules() {
        return [
            [['from_firm_id', 'to_firm_id', 'send_time', 'type', 'deleted', 'deleted_time'], 'integer'],
            [['extra_data'], 'string'],
            [['title', 'text'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'id'            => '#',
            'from_firm_id'  => 'Владелец',
            'to_firm_id'    => 'Получатель',
            'send_time'     => 'Дата отправки',
            'type'          => 'Тип уведомления',
            'title'         => 'Название',
            'text'          => 'Текст',
            'deleted'       => 'Удалено ?',
            'deleted_time'  => 'Дата удаления',
            'extra_data'    => 'Данные по типу уведомления',
        ];
    }

    /**
     * Устанавливает состояние удалённого уведомления.
     * @return bool
     */
    public function setDeletedState() {
        if ($this->isNewRecord) {
            return false;
        }
        $this->deleted = self::DELETED_STATE;
        $this->deleted_time = time();
        return $this->save();
    }

    /**
     * Востанавливает уведомление из удалённых.
     * @return bool
     */
    public function setNotDeletedState() {
        if ($this->isNewRecord) {
            return false;
        }
        $this->deleted = self::NOT_DELETED_STATE;
        $this->deleted_time = 0;
        return $this->save();
    }

    /**
     * @return Firm
     */
    public function getFromFirm() {
        return Firm::findOne($this->from_firm_id);
    }

    /**
     * Метод возвращает ссылку на миниатюру логотипа компании.
     * @param int $width
     * @param int $height
     * @param int $mode
     * @return string
     */
    public function getFromFirmLogo($width = 40, $height = 40, $mode = Image::RESIZE_MODE_AUTO) {
        $firm = $this->getFromFirm();
        if (!empty($firm->image)) {
            return ImageHelper::i()->generateRelativeImageLink($firm->image, $width, $height, $mode);
        }
        return '/img/placeholders/'. $width.'x'.$height.'.png';
    }
}