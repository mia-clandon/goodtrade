<?php

namespace backend\components\qq;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class UploadedFileXhr
 * @package backend\components\qq
 * @author Артём Широких kowapssupport@gmail.com
 */
class UploadedFileXhr {

    /**
     * Save the file to the specified path
     * @param string $path
     * @return bool
     */
    public function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()){
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }

    /**
     * @return string|null
     */
    public function getName() {
        return ArrayHelper::getValue($_GET, 'file');
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }
}