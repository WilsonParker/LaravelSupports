<?php


namespace LaravelSupports\Libraries\RecommendUser\Exceptions;


class SubscriberRecommendedException extends RecommendedException
{
    protected $code = "MB_RC_SB_E4";
    protected $message = "해당 추천인은 자동 결제 회원 입니다.";
}
