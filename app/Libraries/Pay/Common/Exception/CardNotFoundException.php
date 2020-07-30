<?php


namespace LaravelSupports\Libraries\Pay\Common\Exception;


class CardNotFoundException extends \Exception
{
    protected $code = 'MS_PY_CN_E6';
    protected $message = '카드 정보가 저장되지 않습니다.';
}
