<?php


namespace LaravelSupports\Libraries\RecommendUser\Exceptions;


class NotUsableRecommendedException extends RecommendedException
{
    protected $code = "MB_RC_NU_E3";
    protected $message = "추천인 코드를 사용할 수 없는 상품 입니다.";
}
