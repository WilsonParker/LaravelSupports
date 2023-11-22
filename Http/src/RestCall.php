<?php

namespace LaravelSupports\Http;

class RestCall
{
    public $RestObj;

    public function __construct($timeout = 30)
    {
        $this->RestObj = curl_init(); // curl 초기화
        curl_setopt($this->RestObj, CURLOPT_CONNECTTIMEOUT, $timeout); // connection timeout 설정
        curl_setopt($this->RestObj, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($this->RestObj, CURLOPT_RETURNTRANSFER, TRUE); // 요청결과 문자열로 반환
        curl_setopt($this->RestObj, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($this->RestObj, CURLOPT_MAXREDIRS, 5);
    }

    public function getInfo()
    {
        return curl_getinfo($this->RestObj);
    }

    public function GET($url, $ssl = false, $header = array())
    {
        curl_setopt($this->RestObj, CURLOPT_SSL_VERIFYPEER, $ssl);

        if (count($header) > 0) {
            curl_setopt($this->RestObj, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($this->RestObj, CURLOPT_URL, $url);
        curl_setopt($this->RestObj, CURLOPT_RETURNTRANSFER, true);

        $returnVal = curl_exec($this->RestObj);
        return $returnVal;
    }

    public function POST($url, $data = array(), $ssl = false, $header = array())
    {
        curl_setopt($this->RestObj, CURLOPT_SSL_VERIFYPEER, $ssl);

        if (!empty($header)) {
            curl_setopt($this->RestObj, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($this->RestObj, CURLOPT_URL, $url);
        curl_setopt($this->RestObj, CURLOPT_POST, true);
        curl_setopt($this->RestObj, CURLOPT_POSTFIELDS, $data);
        $returnVal = curl_exec($this->RestObj);
        return $returnVal;
    }

    public function close()
    {
        $this->__destruct();
    }

    public function __destruct()
    {
        curl_close($this->RestObj);
    }
}
