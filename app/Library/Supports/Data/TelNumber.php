<?php


namespace App\Library\Supports\Data;


class TelNumber
{

    /**
     * @brief   국내전화번호를 자릿수에 맞춰 split 한다.
     * @params  $number 전화번호전체
     * @param $number
     * @return   array 0:ddd , 1:국번 , 2:번호 // 1588등의 특수번호는 4자리고 0으로 시작하지 않을때만 분리한다.
     * @author  freshsms
     * @date    2019-01-28
     * @bug     미확인
     */
    public static function splitKoreanTelNumber($number): array
    {
        if (strpos($number, "-")) {
            $number = str_replace("-", "", $number);
        }

        if (strlen($number) < 9) {
            return array($number, null, null);
        }

        if (preg_match('/^0/', $number)) {
            //국내용 전국 전화번호는 0 으로 시작한다 (국제규격은 국제기호뒤 ddd 는 0 탈락됨 국제전화번호 필요하면 국가코드 표 만들어서 앞글자 한자리~3자리까지 비교하는 로직 추가할것)
            if (preg_match('/^02/', $number)) {
                //서울국번
                if (strlen($number) == 10) {
                    return self::numberSplit(array(2, 4, 4), $number);
                } elseif (strlen($number) == 9) {
                    return self::numberSplit(array(2, 3, 4), $number);
                }
            } elseif (preg_match('/^0[3-6][1-5]/', $number)) {
                //지방국번
                if (strlen($number) == 11) {
                    return self::numberSplit(array(3, 4, 4), $number);
                } elseif (strlen($number) == 10) {
                    return self::numberSplit(array(3, 3, 4), $number);
                }
            } elseif (preg_match('/^01/', $number)) {
                //휴대폰
                if (strlen($number) == 11) {
                    return self::numberSplit(array(3, 4, 4), $number);
                } elseif (strlen($number) == 10) {
                    return self::numberSplit(array(3, 3, 4), $number);
                }
            } else {
                if (strlen($number) == 12) {
                    return self::numberSplit(array(4, 4, 4), $number);
                } elseif (strlen($number) == 11) {
                    return self::numberSplit(array(3, 4, 4), $number);
                }
            }
        } else {
            if (strlen($number) == 8) {
                return self::numberSplit(array(4, 4, null), $number);
            } else {
                return array($number, null, null);
            }
        }

        return [];
    }

    /**
     * @brief    전화번호 잘라주는 메소드
     * @params array $split_digit 몇글자씩 자를것인지 길이를지정한 배열 (서울 종로관할의 번호는 02-2xxx-xxxx 이다 이때는 array(2,4,4) 를 인자로 준다
     * @params $number 전화번호전체
     * @retun   array 0:ddd , 1:국번 , 2:번호 // 1588등의 특수번호는 4자리고 0으로 시작하지 않을때만 분리한다.
     * @param array $split_digit
     * @param $number
     * @return array
     * @author   freshsms
     * @date     2019-01-28
     * @bug     미확인
     */
    public static function numberSplit(array $split_digit, $number): array
    {
        $current_length = 0;
        $return_arr = array();
        foreach ($split_digit as $length) {
            $current_length += $length;
            $return_arr[] = substr($number, $current_length - $length, $length);
        }

        return $return_arr;
    }

    /**
     *  전화번호 빈값 체크해서 하나의 전화번호로 합쳐주기
     * @param $telFirst
     * @param $telMid
     * @param $telLast
     * @return string
     * @author  오세현
     * @added   2019-08-12
     * @updated 2019-08-12
     */
    public static function numberJoin($telFirst, $telMid, $telLast)
    {
        if(!is_null($telFirst) && !is_null($telMid) && !is_null($telLast)) {
            return $telFirst."-".$telMid."-".$telLast;
        } else {
            return "";
        }
    }

}
