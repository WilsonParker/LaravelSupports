<?php


namespace App\LaravelSupports\Library\SMS\Helpers;


use App\LaravelSupports\Library\SMS\Exceptions\InvalidPhoneNumberException;
use Illuminate\Support\Str;

/**
 * 전화번호 관련 Helper 클래스 입니다
 *
 * @author  dew9163
 * @added   2020/03/16
 * @updated 2020/03/16
 */
class TelNumberHelper
{
    /**
     * RegularExpression for phone number
     *
     * @type    RegularExpression
     * @author  dew9163
     * @added   2020/03/16
     * @updated 2020/03/16
     */
    private const REG_PHONE = '/(010[\d]{3,4}[\d]{4})/';

    /**
     * RegularExpression for phone number with hyphen
     *
     * @type    RegularExpression
     * @author  dew9163
     * @added   2020/03/16
     * @updated 2020/03/16
     */
    private const REG_PHONE_WITH_HYPHEN = '/(010-[\d]{3,4}-[\d]{4})/';

    /**
     * $phone 이 올바른 전화번호 format 인지 확인합니다
     *
     * @param   string $phone
     * @return  bool
     * @author  dew9163
     * @added   2020/03/16
     * @updated 2020/03/16
     */
    public static function isPhoneNumber(string $phone): bool
    {
        return preg_match(self::REG_PHONE, $phone) || preg_match(self::REG_PHONE_WITH_HYPHEN, $phone);
    }

    /**
     *
     * @param   string $phone
     * @param   bool $isNeedHyphen
     * @return  string
     * @author  dew9163
     * @added   2020/03/16
     * @updated 2020/03/16
     */
    public static function getPhoneNumber(string $phone, bool $isNeedHyphen = false): string
    {
        if (!self::isPhoneNumber($phone)) {
            throw new InvalidPhoneNumberException($phone);
        }

        if (!$isNeedHyphen && Str::contains($phone, "-")) {
            $phone = Str::replaceArray($phone, ["-"], "");
        } else if ($isNeedHyphen && !Str::contains($phone, "-")) {
            $phone = substr_replace($phone, '-', 3, 0);
            $phone = substr_replace($phone, '-', 8, 0);
        }
        return $phone;
    }
}
