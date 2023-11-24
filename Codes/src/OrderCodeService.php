<?php


namespace LaravelSupports\Codes;


use LaravelSupports\Codes\Abstracts\AbstractCodeGenerator;

/**
 * 주문번호 생성 및 변경 관련 Service입니다
 *
 * @param
 * @return
 * @author  seul
 * @added   2020-08-26
 * @updated 2020-08-26
 */
class OrderCodeService extends AbstractCodeGenerator
{
    public function createCode(): string
    {
        $arr = explode(' ', microtime());
        $msec = round(1000 * $arr[0]);
        for ($i = strlen($msec); $i < 3; $i++) {
            $msec = '0' . $msec;
        }

        return date('ymdHis') . $msec;
    }

}
