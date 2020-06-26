<?php


namespace LaravelSupports\Libraries\RecommendUser\Exceptions;


class RecommendedException extends \Exception
{
    protected $code = "MB_RC_E1";
    protected $message = "추천인 등록 중 오류가 발생했습니다.";
}
