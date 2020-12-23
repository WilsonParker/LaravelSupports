<?php


namespace LaravelSupports\Libraries\OfflinePayment\EasyCard\Response;


use LaravelSupports\Libraries\Supports\Objects\ReflectionObject;

class EasyCardResponse extends ReflectionObject
{

    // 전문 구분
    public string $RQ01 = '';
    // 단말기 번호
    public string $RQ02 = '';
    // 카드 입력 구분
    public string $RQ03 = '';
    // 카드 번호
    public string $RQ04 = '';
    // 유효 기간
    public string $RQ05 = '';
    // 할부 개월
    public string $RQ06 = '';
    // 금액
    public string $RQ07 = '';
    // 현금 영수증 거래용도
    public string $RQ08 = '';
    // 상품 코드
    public string $RQ09 = '';
    // 원 승인 번호
    public string $RQ10 = '';
    // 원 승인 일자
    public string $RQ11 = '';
    // 봉사료
    public string $RQ12 = '';
    // 부가세
    public string $RQ13 = '';
    // 임시 판매 번호
    public string $RQ14 = '';
    // 웹 전송 메시지
    public string $RQ15 = '';
    // 단말 구분 코드
    public string $RQ16 = '';
    // VAN
    public string $RQ17 = '';
    // 거래 제어 코드
    public string $RS01 = '';
    // 정산 INDEX
    public string $RS02 = '';
    // 거래 일련 번호
    public string $RS03 = '';
    // 응답코드
    public string $RS04 = '';
    // 매입사 코드
    public string $RS05 = '';
    // 매입 일련 번호
    public string $RS06 = '';
    // 승인 일시
    public string $RS07 = '';
    // 거래 고유 번호
    public string $RS08 = '';
    // 승인 번호
    public string $RS09 = '';
    // 체크 카드 유무 | VAN 코드
    public string $RS10 = '';
    // 발급사 코드
    public string $RS11 = '';
    // 발급사명
    public string $RS12 = '';
    // 가맹점 번호
    public string $RS13 = '';
    // 매입사명
    public string $RS14 = '';
    // 화면 제어 코드
    public string $RS15 = '';
    // 화면 제어 코드
    public string $RS16 = '';
    // Notice
    public string $RS17 = '';
    // 전자 서명 유무
    public string $RS18 = '';
    // 사업자 번호
    public string $RS19 = '';
    // 서명 BMP 키
    public string $RS20 = '';
    // DCC 카드 처리 구분
    public string $RS21 = '';
    // 알림 1-1
    public string $RS22 = '';
    // 알림 1-2
    public string $RS23 = '';
    // 알림 1-3
    public string $RS24 = '';
    // 알림 2-1
    public string $RS25 = '';
    // 알림 2-2
    public string $RS26 = '';
    // 알림 2-3
    public string $RS27 = '';
    // 알림 2-4
    public string $RS28 = '';
    // 현금 지급 금액
    public string $RS29 = '';
    // ARS 경품 번호
    public string $RS30 = '';
    // 동굴 FLAG
    public string $RS31 = '';
    // 거래 구분 FLAG
    public string $RS32 = '';
    // 동굴 SKT 제휴사 정보
    public string $RS33 = '';
    // 보너스 승인 번호
    public string $RS34 = '';

    public function getTid(): string
    {
        return $this->RS08;
    }

    public function getCid(): string
    {
        return $this->RS09;
    }

    public function isSuccess(): bool
    {
        return $this->RS04 == '0000';
    }
}
