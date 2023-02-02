<?php

namespace common\libs\parser;

use yii\base\Exception;

/**
 * Class Parser
 * @package common\libs\parser
 * @author Артём Широких kowapssupport@gmail.com
 */
abstract class Parser {

    /** @var null|string */
    protected $url = null;

    /** @var int  */
    protected $timeout = 30;

    /** @var array  */
    protected $post_data = [];

    /** @var bool  */
    private $post_request = false;

    /**
     * получение контента по url
     * @return string
     * @throws Exception
     */
    protected function getContent() {
        if (!is_null($this->getUrl())) {
            if (!extension_loaded('curl')) {
                return file_get_contents(
                    str_replace('https', 'http', $this->getUrl())
                );
            }
            else {
                // curl
                if ($curl = curl_init()) {
                    curl_setopt($curl, CURLOPT_URL, $this->getUrl());
                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
                    if ($this->post_request) {
                        curl_setopt($curl, CURLOPT_POST, true);
                        // post data
                        if (!empty($this->post_data)) {
                            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->post_data));
                        }
                    }
                    $content = curl_exec($curl);
                    curl_close($curl);
                    return $content;
                }
                else {
                    throw new Exception('curl init error.');
                }
            }
        }
        throw new Exception('parse url is null !');
    }

    /**
     * @return null|string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setPostRequest($flag = true) {
        $this->setPostRequest($flag);
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setPostData(array $data) {
        $this->post_request = true;
        $this->post_data = $data;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }
}