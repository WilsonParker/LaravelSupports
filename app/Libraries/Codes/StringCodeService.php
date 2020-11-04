<?php


namespace LaravelSupports\Libraries\Codes;


use Illuminate\Support\Str;
use LaravelSupports\Libraries\Codes\Abstracts\AbstractCodeGenerator;

/**
 * String 코드 생성 및 변경 관련 Service 입니다
 *
 * @author  dew9163
 * @added   2020/06/11
 * @updated 2020/06/11
 */
class StringCodeService extends AbstractCodeGenerator
{
    /**
     * 코드 길이 입니다
     *
     * @var int
     * @author  dew9163
     * @added   2020/06/11
     * @updated 2020/06/11
     */
    protected int $codeLength = 64;

    /**
     * StringCodeService constructor.
     * @param int $codeLength
     */
    public function __construct(int $codeLength = 64)
    {
        $this->codeLength = $codeLength;
    }

    public function createCode(): string
    {
        return Str::random($this->codeLength);
    }

}
