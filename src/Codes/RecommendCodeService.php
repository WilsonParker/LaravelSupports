<?php


namespace LaravelSupports\Codes;


use Illuminate\Database\Eloquent\Model;
use LaravelSupports\Codes\Abstracts\AbstractCodeGenerator;
use LaravelSupports\Codes\Contracts\GenerateCode;
use LaravelSupports\Database\Traits\TransactionTrait;


/**
 * 추천인 코드 생성 및 변경 관련 Service 입니다
 *
 * @author  WilsonParker
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
     * @author  WilsonParker
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected int $codeLength = 6;

    /**
     * 코드에 적용할 문자들 입니다
     * 숫자
     *
     * @var string
     * @author  WilsonParker
     * @added   2020/04/20
     * @updated 2020/04/20
     */
    protected string $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    protected function isExistCode(GenerateCode $model, string $code): bool
    {
        return $model->where("recom_code", $code)->exists();
    }

    /**
     * PlusMember 의 추천인 코드를 변경 합니다
     * 추천인 코드를 생성하여 중복이 되지 않으면 해당 코드로 설정하며
     * 중복이 된 코드가 생성 되었을 경우 코드를 다시 생성합니다
     * 최대 $replayCount 값 만큼 코드를 다시 생성합니다
     *
     * @param Model $model
     * @param string $code
     * @return Model
     * @author  WilsonParker
     * @added   2020/04/20
     * @updated 2020/04/20
     * @inheritDoc
     */
    protected function bindCode(GenerateCode $model, string $code): Model
    {
        $callback = function () use ($model, $code) {
            $model->recom_code = $code;
            return $model;
        };
        $errorCallback = function ($e) {
            return null;
        };
        return $this->runTransaction($callback, $errorCallback);
    }

}
