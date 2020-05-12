<?php


namespace LaravelSupports\Libraries\Supports\Html;


class UrlUtil
{
    /**
     * https://enter6.co.kr/product/best 같은 도메인에서
     * https://enter6.co.kr 를 제거하여 /product/best 만 추출하도록 합니다
     * @param
     * @return
     * @author  WilsonParker
     * @added   2019-05-15
     * @updated 2019-05-15
     */
    public static function extractRoute($url)
    {
        $urlReg = "^https?://(\w*:\w*@)?[-\w.]+(:\d+)?^";
        return preg_replace($urlReg, "", $url);
    }
}
