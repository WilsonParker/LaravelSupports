<?php


namespace LaravelSupports\Libraries\Supports\String\Traits;

trait ConvertStringTrait
{
    /**
     * 말줄임
     *
     * @param
     * @param int $length
     * @return string
     * @author  seul
     * @added   2020-09-01
     * @updated 2020-09-01
     */
    public function shortenString($string, $length = 10)
    {
        return mb_strlen($string) > $length ? mb_substr($string, 0, $length) . "..." : $string;
    }
}
