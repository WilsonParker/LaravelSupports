<?php


namespace LaravelSupports\Libraries\RecommendUser\Exceptions;


class DoNotSelfRecommendedException extends RecommendedException
{
    protected $code = "MB_RC_NS_E7";
    protected $message = "본인의 추천인 코드는 사용할 수 없어요.";
}
