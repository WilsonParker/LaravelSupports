<?php


namespace App\Library\LaravelSupports\app\Libraries\OfflinePayment\EasyCard\Response\Traits;


use Carbon\Carbon;

trait GetPaymentAttributes
{
    public function getTid(): string
    {
        return $this->RS08;
    }

    public function getCid(): string
    {
        return $this->RS09;
    }

    // 사업자 번호
    public function getBusinessNumber(): string
    {
        return '220-88-65167';
    }

    // 대표자
    public function getExponent(): string
    {
        return '김준현';
    }

    // 전화번호
    public function getContact(): string
    {
        return '070-5029-1422';
    }

    // 주소
    public function getAddress(): string
    {
        return '서울특별시 서초구 매헌로 16 5층';
    }

    // 결제 금액
    public function getPaymentPrice(): string
    {
        return $this->RQ07;
    }

    // 부가세
    public function getVAT(): string
    {
        return $this->RQ13;
    }

    // 할인 금액
    public function getSalePrice(): string
    {
        return $this->RQ12;
    }

    // 카드 발급사 명
    public function getCardCompany(): string
    {
        return $this->RS12;
    }

    // 카드 번호
    public function getCardNumber(): string
    {
        return $this->RQ04;
    }

    // 승인 일시
    public function getApprovalDate(): string
    {
        return Carbon::now()->format('y-m-d H:i:s');
    }

    // 승인 번호
    public function getApprovalNumber(): string
    {
        return $this->RS09;
    }

    public function isSuccess(): bool
    {
        return $this->RS04 == '0000';
    }
}
