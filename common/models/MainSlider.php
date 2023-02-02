<?php

namespace common\models;

use common\libs\Storage;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "main_slider".
 *
 * @property integer $id
 * @property integer $slide_id
 * @property string $image
 * @property string $tag
 * @property string $button
 * @property string $tip
 * @property string $type
 * @property string $title
 * @property string $link
 * @property integer $firm_id
 * @property string $description
 * @property integer $created_at
 * @property integer $update_at
 *
 * @author yerganat
 */
class MainSlider extends Base
{
    #region - теги для слайдера;
    //новости
    public const TAG_NEWS = 'news';
    //новый поставщик
    public const TAG_NEW_FIRM = 'firm';
    //видео
    public const TAG_VIDEO = 'video';
    //свободный блок
    public const TAG_BANNER = 'banner';
    #endregion;

    #region - размеры слайдеров;
    // 1*1 (маленький квадрат)
    public const SLIDER_SMALL_SQUARE = 'small_square';
    //1*2 (вытянутый прямоугольник)
    public const SLIDER_HORIZONTAL_SQUARE = 'horizontal_square';
    // 2*1 (вытянутый вертикальный прямоугольник)
    public const SLIDER_VERTICAL_SQUARE = 'vertical_square';
    // 2*2 (большой блок)
    public const SLIDER_BIG_SQUARE = 'big_square';
    #endregion;

    public const SLIDER_WEIGHT = [
        self::SLIDER_SMALL_SQUARE => 1,
        self::SLIDER_HORIZONTAL_SQUARE => 2,
        self::SLIDER_VERTICAL_SQUARE => 2,
        self::SLIDER_BIG_SQUARE => 4,
    ];

    public const SLIDER_MAX_WEIGH_OF_ELEMENTS = 4;

    /**
     * Присваивает номер для упрощения условия
     * self::SLIDER_SMALL_SQUARE = 1
     * self::SLIDER_HORIZONTAL_SQUARE = 2
     * self::SLIDER_VERTICAL_SQUARE = 3
     * self::SLIDER_BIG_SQUARE = 4
     * @return int
     */
    public function getTypeNum(): int
    {
        if ($this->type == self::SLIDER_VERTICAL_SQUARE) {
            return 3;
        }
        return self::SLIDER_WEIGHT[$this->type];
    }

    public static function tableName(): string
    {
        return 'main_slider';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['created_at', 'slide_id', 'firm_id'], 'integer'],
            [['type', 'tag', 'tip', 'button', 'title', 'image'], 'string', 'max' => 255],
            [['link', 'description'], 'string'],
            [['type'], 'checkAllowedType'],
            [['firm_id'], 'checkIfFirmTag'],
        ];
    }

    //Проверяет, можно лы выбрать тип.
    public function checkAllowedType(string $attribute): bool
    {
        if (empty($this->slide_id)) {
            return true;
        }

        $sum = $this->getTypeSum();
        $attribute_type = $this->getAttribute($attribute);

        if ($sum == self::SLIDER_MAX_WEIGH_OF_ELEMENTS) {
            $this->addError($attribute, 'Правая часть слайда заполнена!');
            return false;
        }

        if ($sum + self::SLIDER_WEIGHT[$attribute_type] > self::SLIDER_MAX_WEIGH_OF_ELEMENTS) {
            $this->addError($attribute, 'Выберите элементе размером меньше !');
            return false;
        }
        return true;
    }

    public function getTypeSum(): int
    {
        $types_count = $this->getSlideTypesCount($this->slide_id);

        if (!$this->isNewRecord) {
            $old_slider = MainSlider::findOne(['id' => $this->id]);
            if ($this->slide_id == $old_slider->slide_id && array_key_exists($old_slider->type, $types_count)) {
                $types_count[$old_slider->type] = $types_count[$old_slider->type] - 1;
            }
        }

        $sum = 0;
        foreach ($types_count as $type => $count) {
            if (isset(self::SLIDER_WEIGHT[$type])) {
                $sum += self::SLIDER_WEIGHT[$type] * $count;
            }
        }
        return $sum;
    }

    public function getNotAllowedType(): array
    {
        $sum = $this->getTypeSum();
        $not_allowed_types = [];

        foreach (self::SLIDER_WEIGHT as $type => $weight) {
            if ($weight > self::SLIDER_MAX_WEIGH_OF_ELEMENTS - $sum) {
                $not_allowed_types[] = $type;
            }
        }

        $types_count = $this->getSlideTypesCount($this->slide_id);

        if (array_key_exists(self::SLIDER_HORIZONTAL_SQUARE, $types_count)) {
            if ($this->isNewRecord || (!$this->isNewRecord) & MainSlider::findOne(['id' => $this->id])->type != self::SLIDER_HORIZONTAL_SQUARE) {
                $not_allowed_types[] = self::SLIDER_VERTICAL_SQUARE;
            }
        }

        if (array_key_exists(self::SLIDER_VERTICAL_SQUARE, $types_count)) {
            if ($this->isNewRecord || (!$this->isNewRecord) & MainSlider::findOne(['id' => $this->id])->type != self::SLIDER_VERTICAL_SQUARE) {
                $not_allowed_types[] = self::SLIDER_HORIZONTAL_SQUARE;
            }
        }
        return array_unique($not_allowed_types);
    }

    public function isFull(): bool
    {
        $types_count = $this->getSlideTypesCount($this->id);
        $sum = 0;
        foreach ($types_count as $type => $count) {
            if (isset(self::SLIDER_WEIGHT[$type])) {
                $sum += self::SLIDER_WEIGHT[$type] * $count;
            }
        }
        if ($sum >= self::SLIDER_MAX_WEIGH_OF_ELEMENTS) {
            return true;
        }
        return false;
    }

    //Валидирует поле организации в случае если выбран тип TAG_NEW_FIRM.
    public function checkIfFirmTag(string $attribute): bool
    {
        $firm_id = $this->getAttribute($attribute);
        if ($this->tag === self::TAG_NEW_FIRM && empty($firm_id)) {
            $this->addError($attribute, 'Поле "компания" обязательно для выбора.');
            return false;
        }
        return true;
    }

    //Метод возвращает количество слайдов по типу по номеру слайда.
    public function getSlideTypesCount(int $slide_id): array
    {
        $rows = (new Query())
            ->select(['COUNT(*) AS `count`', 'type'])
            ->from(self::tableName())
            ->where(['slide_id' => $slide_id])
            ->groupBy(['type'])
            ->all();
        $data = [];
        foreach ($rows as $row) {
            $data[$row['type']] = (int)$row['count'];
        }
        return $data;
    }

    public function attributeLabels(): array
    {
        return [
            'id' => '#',
            'tag' => 'Тег',
            'title' => 'Заголовок',
            'tip' => 'Комментарии к кнопке',
            'button' => 'Текст кнопки',
            'type' => 'Размер и положение элемента',
            'link' => 'Ссылка',
            'firm' => 'Компания',
            'slide_id' => 'ID слайда',
            'description' => 'Описание',
            'created_at' => 'Время создания',
            'updated_at' => 'Время обновления',
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = self::find();
        if (isset($params['type']) && !empty($params['type'])) {
            $query->where(['type' => $params['type']]);
        }
        if (isset($params['slide_id']) && !empty($params['slide_id'])) {
            $query->andWhere(['slide_id' => $params['slide_id']]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    //Устанавливает фото записи.
    public function setImage(array $images): self
    {
        $images = array_filter($images, function ($image) {
            return trim($image) !== '';
        });
        $uploaded_files = Storage::i()->moveFilesToStorage($images, self::tableName());
        if (!empty($uploaded_files)) {
            $this->image = $uploaded_files[0] ?? null;
        }
        return $this;
    }

    public function getTags(): array
    {
        return [
            self::TAG_BANNER => 'Баннер',
            self::TAG_NEW_FIRM => 'Новая компания',
            self::TAG_NEWS => 'Новость',
            self::TAG_VIDEO => 'Видео',
        ];
    }

    //Тег.
    public function getCurrentTagText(): string
    {
        $tags = $this->getTags();
        if (array_key_exists($this->tag, $tags)) {
            return $tags[$this->tag];
        }
        return '';
    }

    public static function getTypes(): array
    {
        return [
            self::SLIDER_SMALL_SQUARE => '1*1',
            self::SLIDER_HORIZONTAL_SQUARE => '1*2',
            self::SLIDER_VERTICAL_SQUARE => '2*1',
            self::SLIDER_BIG_SQUARE => '2*2',
        ];
    }

    //Тип записи.
    public function getCurrentTypeText(): string
    {
        if ($this->isNewRecord) {
            return '';
        }
        return static::getTypes()[$this->type] ?? '';
    }
}
