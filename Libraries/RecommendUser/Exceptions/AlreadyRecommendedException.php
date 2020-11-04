<?php


namespace LaravelSupports\Libraries\RecommendUser\Exceptions;


class AlreadyRecommendedException extends RecommendedException
{
    protected $code = "MB_RC_AR_E2";
    protected $message = "이미 추천인 등록을 하셨습니다.";
}
