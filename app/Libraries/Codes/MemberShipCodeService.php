<?php


namespace LaravelSupports\Libraries\Codes;


use LaravelSupports\Libraries\Codes\Contracts\CodeGeneratable;
use Illuminate\Database\Eloquent\Model;
use LaravelSupports\Libraries\Codes\Abstracts\AbstractCodeGenerator;
use LaravelSupports\Libraries\Supports\Databases\Traits\TransactionTrait;


/**
 * 멤버쉽 코드 생성 및 변경 관련 Service 입니다
 *
 * @author  dew9163
 * @added   2020/04/20
 * @updated 2020/04/20
 */
class MemberShipCodeService extends AbstractCodeGenerator
{
    /**
     * 코드 길이 입니다
     *
     * @var int
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected int $codeLength = 16;

    /**
     * 코드에 적용할 문자들 입니다
     * 숫자
     *
     * @var string
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected string $characters = '0123456789';

    /**
     * 멤버쉽 코드를 생성 합니다
     *
     * @param bool $withHyphen
     * @return string
     * @example
     * 5377022836235024
     * 5377-0228-3623-5024
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/21
     */
    public function createCode(bool $withHyphen = false): string
    {
        $code = "";
        for ($i = 0; $i < $this->codeLength; $i++) {
            if ($withHyphen && $i != 0 && $i % 4 == 0) {
                $code .= "-";
            }
            // 멤버쉽 번호의 첫번째 숫자는 0 이 오지 않도록 합니다
            $code .= $this->createChar($i == 0 ? "/(0)/" : "");
        }
        return $code;
    }

    /**
     * 생성된 바코드에 - (hyphen) 을 추가 합니다
     *
     * @param string $code
     * @return string
     * @author  dew9163
     * @added   2020/04/21
     * @updated 2020/04/21
     */
    public function memberShipWithHyphen(string $code): string
    {
        $result = "";
        foreach (str_split($code) as $i => $char) {
            if ($i != 0 && $i % 4 == 0) {
                $result .= "-";
            }
            $result .= $char;
        }
        return $result;
    }


}
