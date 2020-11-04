<?php


namespace LaravelSupports\Libraries\RecommendUser\Exceptions;


class NotFoundRecommendedException extends RecommendedException
{
    protected $code = "MB_RC_NF_E5";
    protected $message = "입력하신 추천인 코드가 정확하지 않습니다. 다시 한 번 확인해주세요.";
}
