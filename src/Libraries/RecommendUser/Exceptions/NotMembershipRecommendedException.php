<?php


namespace LaravelSupports\Libraries\RecommendUser\Exceptions;


class NotMembershipRecommendedException extends RecommendedException
{
    protected $code = "MB_RC_NP_E6";
    protected $message = "멤버십이 아닌 회원을 추천할 수 없습니다.";
}
