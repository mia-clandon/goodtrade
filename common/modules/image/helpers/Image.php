<?php

namespace common\modules\image\helpers;

use common\libs\traits\Singleton;
use Imagine\Image\Box;
use Imagine\Imagick\Imagine;

/**
 * Class Image
 * @package common\modules\image\helper\Image
 * @author Артём Широких kowapssupport@gmail.com
 */
class Image {
    use Singleton;

    /** ключи массива параметров. */
    const RESIZE_KEY        = 'resize';
    const RESIZE_WIDTH      = 'width';
    const RESIZE_HEIGHT     = 'height';
    const RESIZE_MODE       = 'mode';
    const REQUEST_LINK      = 'request_link';
    const FILE_NAME         = 'file_name';
    const FILE_EXTENSION    = 'extension';

    /** режими ресайза. */
    //auto|height|width|none|crop
    const RESIZE_MODE_AUTO      = 1;
    const RESIZE_MODE_HEIGHT    = 2;
    const RESIZE_MODE_WIDTH     = 3;
    const RESIZE_MODE_NONE      = 4;
    const RESIZE_MODE_CROP      = 5;
    const RESIZE_MODE_PRECISE   = 6;
    const RESIZE_MODE_INVERSE   = 7;

    /** @var array */
    private $params;
    /** @var int|null */
    private $width_for_resize_by_mode;
    /** @var int|null */
    private $height_for_resize_by_mode;
    /** @var int|null */
    private $original_width;
    /** @var int|null */
    private $original_height;
    /** @var bool */
    private $processed_sizes_by_mode = false;
    /** @var \Imagine\Imagick\Image */
    private $imagine_image;

    /**
     * @return array
     */
    public static function getResizeModes(): array {
        return [
            self::RESIZE_MODE_AUTO      => 'auto',
            self::RESIZE_MODE_HEIGHT    => 'height',
            self::RESIZE_MODE_WIDTH     => 'width',
            self::RESIZE_MODE_NONE      => 'none',
            self::RESIZE_MODE_CROP      => 'crop',
            self::RESIZE_MODE_PRECISE   => 'precise',
            self::RESIZE_MODE_INVERSE   => 'inverse',
        ];
    }

    /**
     * Формирует и возвращает абсолютный путь до миниатюры.
     * @return string|null
     * @throws
     */
    public function get(): ?string {

        // ресайз не требуется.
        if (!$this->needResize()) {
            return $this->getAbsoluteRequestUrlOriginal();
        }

        // уже есть такая миниатюра.
        $thumbnail_path = $this->getAbsoluteThumbnailPath();
        if (file_exists($thumbnail_path)) {
            return $thumbnail_path;
        }

        $image = $this->getImageObject();
        $width = $this->getWidthForResizeByMode();
        $height = $this->getHeightForResizeByMode();

        // ресайз.
        $image->resize(new Box($width, $height));
        $image->save($thumbnail_path);
        return $thumbnail_path;
    }

    /**
     * @param array $params
     * @return $this
     * @throws \Exception
     */
    public function setParams(array $params) {
        $this->params = $params;
        $this->getImageObject();
        return $this;
    }

    /**
     * Возвращает URL запроса.
     * @return null|string
     * @throws
     */
    private function getRequestUrl(): ?string {
        $request_link = $this->getRequestLinkFromParams();
        if ($request_link === null) {
            return $request_link;
        }
        return urldecode($request_link) ?? null;
    }

    /**
     * Возвращает URL оригинального изображения.
     * @return null|string
     * @throws
     */
    private function getRequestUrlOriginal(): ?string {
        $request_url = $this->getRequestUrl();
        if (null === $request_url) {
            throw new \Exception('Request url not found in params.');
        }
        $file_name = $this->getParams()[self::FILE_NAME] ?? null;
        $extension = $this->getParams()[self::FILE_EXTENSION] ?? null;
        if (!$file_name || !$extension) {
            throw new \Exception('File name not found in params.');
        }
        $directory_name = pathinfo($request_url, PATHINFO_DIRNAME);
        return $directory_name.'/'.$file_name.'.'.$extension;
    }

    /**
     * Возвращает полный путь до оригинального изображения.
     * @return null|string
     * @throws \Exception
     */
    public function getAbsoluteRequestUrlOriginal(): ?string {
        $request_url_original = $this->getRequestUrlOriginal();
        $request_url_original = \Yii::getAlias('@common/web'). $request_url_original;
        if (!file_exists($request_url_original)) {
            throw new \Exception('File not found.');
        }
        return $request_url_original;
    }

    /**
     * Возвращает полный путь до миниатюры.
     * @return null|string
     * @throws \Exception
     */
    public function getAbsoluteThumbnailPath(): ?string {
        $width = $this->getWidthFromParams();
        $height = $this->getHeightFromParams();
        $mode = $this->getModeFromParams();
        $absolute_request_url = $this->getAbsoluteRequestUrlOriginal();

        return str_replace('.'. $this->getFileExtensionFromParams(),
            $this->getSizesParamsString($width, $height, $mode). '.'. $this->getFileExtensionFromParams()
        , $absolute_request_url);
    }

    /**
     * Строка параметров ресайза (постфикс).
     * @param int|null $width
     * @param int|null $height
     * @param string|null $mode
     * @return string
     * @throws
     */
    private function getSizesParamsString(int $width = null, int $height = null, string $mode = null): string {
        $sizes = '';
        if ($width) {
            $sizes .= '_'. $width;
            if ($height) {
                $sizes .= '_'. $height;
            }
            if (!empty($mode)) {
                $sizes .= $mode;
            }
        }
        return $sizes;
    }

    /**
     * Возвращает режим ресайза (строковое представление) по константе.
     * @param int|null $mode
     * @return string
     */
    public function getModeStringByCode(int $mode = null): string {
        if ($mode === null) {
            return '';
        }
        return self::getResizeModes()[$mode] ?? '';
    }

    /**
     * Возвращает режим ресайза (константу) по строковому представлению.
     * @param string $mode
     * @return int
     */
    public function getModeConstByString(string $mode = null): int {
        if ($mode === null) {
            return self::RESIZE_MODE_AUTO;
        }
        $resize_modes = self::getResizeModes();
        $resize_modes = array_flip($resize_modes);
        return $resize_modes[$mode] ?? self::RESIZE_MODE_AUTO;
    }

    /**
     * @return string|null
     * @throws \Exception
     */
    public function getRequestLinkFromParams(): ?string {
        return $this->getParams()[self::REQUEST_LINK] ?? null;
    }

    /**
     * @param int|null $width
     * @param int|null $height
     * @param int|null $master
     * @throws \Exception
     * @return $this
     */
    private function processResizeMode($width = null, $height = null, $master = null) {
        if ($master === NULL) {
            // Choose the master dimension automatically
            $master = Image::RESIZE_MODE_AUTO;
        }
        // Image::WIDTH and Image::HEIGHT deprecated. You can use it in old projects,
        // but in new you must pass empty value for non-master dimension
        elseif ($master == Image::RESIZE_MODE_WIDTH AND ! empty($width)) {
            $master = Image::RESIZE_MODE_AUTO;
            // Set empty height for backward compatibility
            $height = NULL;
        }
        elseif ($master == Image::RESIZE_MODE_HEIGHT AND ! empty($height)) {
            $master = Image::RESIZE_MODE_AUTO;
            // Set empty width for backward compatibility
            $width = NULL;
        }
        if (empty($width)) {
            if ($master === Image::RESIZE_MODE_NONE) {
                // Use the current width
                $width = $this->original_width;
            }
            else {
                // If width not set, master will be height
                $master = Image::RESIZE_MODE_HEIGHT;
            }
        }
        if (empty($height)) {
            if ($master === Image::RESIZE_MODE_NONE) {
                // Use the current height
                $height = $this->original_height;
            }
            else {
                // If height not set, master will be width
                $master = Image::RESIZE_MODE_WIDTH;
            }
        }
        switch ($master) {
            case Image::RESIZE_MODE_AUTO:
                // Choose direction with the greatest reduction ratio
                $master = ($this->original_width / $width) > ($this->original_height / $height)
                    ? Image::RESIZE_MODE_WIDTH
                    : Image::RESIZE_MODE_HEIGHT;
                break;
            case Image::RESIZE_MODE_INVERSE:
                // Choose direction with the minimum reduction ratio
                $master = ($this->original_width / $width) > ($this->original_height / $height)
                    ? Image::RESIZE_MODE_HEIGHT
                    : Image::RESIZE_MODE_WIDTH;
                break;
            case Image::RESIZE_MODE_WIDTH:
                // Recalculate the height based on the width proportions
                $height = $this->original_height * $width / $this->original_width;
                break;
            case Image::RESIZE_MODE_HEIGHT:
                // Recalculate the width based on the height proportions
                $width = $this->original_width * $height / $this->original_height;
                break;
            case Image::RESIZE_MODE_PRECISE:
                // Resize to precise size
                $ratio = $this->original_width / $this->original_height;
                if ($width / $height > $ratio) {
                    $height = $this->original_height * $width / $this->original_width;
                }
                else {
                    $width = $this->original_width * $height / $this->original_height;
                }
                break;
        }
        // Convert the width and height to integers, minimum value is 1px
        $this->width_for_resize_by_mode = max(round($width), 1);
        $this->height_for_resize_by_mode = max(round($height), 1);
        return $this;
    }

    /**
     * Возвращает просчитанные размеры ширины в зависимости от режима ресайза.
     * @return int|null
     * @throws \Exception
     */
    public function getWidthForResizeByMode(): ?int {
        $this->initProcessSizesByMode();
        return $this->width_for_resize_by_mode;
    }

    /**
     * Возвращает просчитанные размеры высоты в зависимости от режима ресайза.
     * @return int|null
     * @throws \Exception
     */
    public function getHeightForResizeByMode(): ?int {
        $this->initProcessSizesByMode();
        return $this->height_for_resize_by_mode;
    }

    /**
     * @return int|null
     * @throws \Exception
     */
    public function getWidthFromParams(): ?int {
        return $this->getParams()[self::RESIZE_KEY][self::RESIZE_WIDTH] ?? null;
    }

    /**
     * @return int|null
     * @throws \Exception
     */
    public function getHeightFromParams(): ?int {
        return $this->getParams()[self::RESIZE_KEY][self::RESIZE_HEIGHT] ?? null;
    }

    /**
     * @return string|null
     * @throws
     */
    public function getModeFromParams(): ?string {
        return $this->getParams()[self::RESIZE_KEY][self::RESIZE_MODE] ?? null;
    }

    /**
     * @return null|string
     * @throws
     */
    public function getFileNameFromParams(): ?string {
        return $this->getParams()[self::FILE_NAME] ?? null;
    }

    /**
     * @return null|string
     * @throws
     */
    public function getFileExtensionFromParams(): ?string {
        return $this->getParams()[self::FILE_EXTENSION] ?? null;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getParams(): array {
        if (null === $this->params) {
            throw new \Exception('Params not setted;');
        }
        return $this->params;
    }

    /**
     * Нужен ли ресайз.
     * @throws
     * @return bool
     */
    private function needResize(): bool {
        // в параметрах не передана ширина картинки для ресайзинга.
        if ((bool)$this->getWidthFromParams() === false) {
            return false;
        }
        $width = $this->getWidthForResizeByMode();
        $height = $this->getHeightForResizeByMode();

        if ($width >= $this->original_width) {
            $width = $this->original_width;
        }
        if ($height >= $this->original_height) {
            $height = $this->original_height;
        }
        if ($width === $this->original_width && $height === $this->original_height) {
            return false;
        }
        if (null === $width && null === $height) {
            return false;
        }
        return true;
    }

    /**
     * Метод генерирует абсолютную ссылку для получения миниатюры.
     * @param string $image
     * @param int|null $width
     * @param int|null $height
     * @param int|null $resize_mode
     * @return string
     * @throws \Exception
     */
    public function generateAbsoluteImageLink(string $image, int $width = null, int $height = null, int $resize_mode = null): string {
        $this->setParams([
            Image::FILE_NAME        => pathinfo($image, PATHINFO_FILENAME),
            Image::FILE_EXTENSION   => pathinfo($image, PATHINFO_EXTENSION),
            Image::REQUEST_LINK     => $image,
            // параметры ресайза.
            Image::RESIZE_KEY => [
                Image::RESIZE_MODE      => $this->getModeStringByCode($resize_mode),
                Image::RESIZE_WIDTH     => $width,
                Image::RESIZE_HEIGHT    => $height,
            ],
        ]);
        return $this->getAbsoluteThumbnailPath();
    }

    /**
     * Метод генерирует относительную ссылку для получения миниатюры.
     * @param string $image - путь к оригиналу (ex. /storage/...)
     * @param int|null $width - ширина.
     * @param int|null $height - высота.
     * @param int|null $resize_mode - режим ресайзинга фото (auto|height|width|none|crop)
     * @return string|bool
     */
    public function generateRelativeImageLink(string $image, int $width = null, int $height = null, int $resize_mode = null) {
        try {
            $absolute_url = $this->generateAbsoluteImageLink($image, $width, $height, $resize_mode);
            return str_replace(\Yii::getAlias('@common/web'), '', $absolute_url);
        }
        catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Метод удаляет все созданные миниатюры.
     * @param string $image - relative image path.
     * @param bool $with_original
     * @return bool
     */
    public function removeAllThumbnails(string $image, bool $with_original = false): bool {
        $absolute_path = \Yii::getAlias('@common/web'). $image;
        if (!file_exists($absolute_path)) {
            return false;
        }
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        $path = str_replace('.'.$extension, '', $absolute_path);
        $thumbnails = [];
        foreach (glob($path. "*.".$extension) as $thumbnail) {
            $thumbnails[] = $thumbnail;
        }
        if (!$with_original) {
            $original_index = array_search($absolute_path, $thumbnails);
            if ($original_index !== false) {
                unset($thumbnails[$original_index]);
            }
        }
        foreach ($thumbnails as $thumbnail) {
            @unlink($thumbnail);
        }
        return true;
    }

    /**
     * @return \Imagine\Imagick\Image
     * @throws \Exception
     */
    private function getImageObject(): \Imagine\Imagick\Image {
        if (null === $this->imagine_image) {
            if (!file_exists($this->getAbsoluteRequestUrlOriginal())) {
                throw new \Exception('File not found.');
            }
            /** @var Imagine $imagine - ImageMagic */
            $imagine = new Imagine();
            /** @var \Imagine\Imagick\Image $image */
            $image = $imagine->open($this->getAbsoluteRequestUrlOriginal());
            $this->original_width = $image->getSize()->getWidth();
            $this->original_height = $image->getSize()->getHeight();
            $this->imagine_image = $image;
        }
        return $this->imagine_image;
    }

    /**
     * @throws \Exception
     */
    private function initProcessSizesByMode(): void {
        if ($this->processed_sizes_by_mode === false) {
            // просчитывает значения ширины и высоты в зависимости от выбранного режима ресайза.
            $this->processResizeMode(
                $this->getWidthFromParams(),
                $this->getHeightFromParams(),
                $this->getModeConstByString($this->getModeFromParams())
            );
            $this->processed_sizes_by_mode = true;
        }
    }
}