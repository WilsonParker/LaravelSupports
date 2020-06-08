<?php


namespace LaravelSupports\Libraries\Codes;


use LaravelSupports\Libraries\Supports\Databases\Traits\TransactionTrait;
use LaravelSupports\Models\Members\PlusMemberModel;
use LaravelSupports\Models\Members\PlusMemberModel as PlusMemberModelAlias;
use LaravelSupports\Libraries\Codes\Abstracts\AbstractCodeGenerator;
use Illuminate\Database\Eloquent\Model;


/**
 * 추천인 코드 생성 및 변경 관련 Service 입니다
 *
 * @author  dew9163
 * @added   2020/04/20
 * @updated 2020/04/20
 */
class RecommendCodeService extends AbstractCodeGenerator
{
    use TransactionTrait;

    /**
     * 코드 길이 입니다
     *
     * @var int
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected int $codeLength = 6;

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

    /**
     * PlusMember 의 추천인 코드를 변경 합니다
     * 추천인 코드를 생성하여 중복이 되지 않으면 해당 코드로 설정하며
     * 중복이 된 코드가 생성 되었을 경우 코드를 다시 생성합니다
     * 최대 $replayCount 값 만큼 코드를 다시 생성합니다
     *
     * @param Model $model
     * @param string $code
     * @return Model
     * @author  dew9163
     * @added   2020/04/20
     * @updated 2020/04/20
     * @inheritDoc
     */
    protected function bindCode(Model $model, string $code): Model
    {
        $callback = function () use ($model, $code) {
            if (!$model->where("recom_code", $code)->exists()) {
                $model->recom_code = $code;
                return $model;
            } else {
                return false;
            }
        };
        $errorCallback = function ($e) {
            return null;
        };
        return $this->runTransaction($callback, $errorCallback);
    }

}
