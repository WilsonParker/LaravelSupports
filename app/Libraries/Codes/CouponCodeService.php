<?php


namespace LaravelSupports\Libraries\Codes;


use LaravelSupports\Libraries\Codes\Contracts\CodeGeneratable;
use Illuminate\Database\Eloquent\Model;
use LaravelSupports\Libraries\Codes\Abstracts\AbstractCodeGenerator;
use LaravelSupports\Libraries\Supports\Databases\Traits\TransactionTrait;


/**
 * 쿠폰 코드 생성 및 변경 관련 Service 입니다
 *
 * @author  dew9163
 * @added   2020/06/08
 * @updated 2020/06/08
 */
class CouponCodeService extends AbstractCodeGenerator
{
    /**
     * 코드 길이 입니다
     *
     * @var int
     * @author  dew9163
     * @added   2020/06/08
     * @updated 2020/06/08
     */
    protected int $codeLength = 8;

    /**
     * 코드에 적용할 문자들 입니다
     * 숫자
     *
     * @var string
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected string $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

}
