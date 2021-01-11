<?php


namespace LaravelSupports\Libraries\OfflinePayment\EasyCard\Request;


use LaravelSupports\Libraries\Supports\Objects\ReflectionObject;

class EasyCardRequest extends ReflectionObject
{

    // 전문구분[2]
    public string $gubun = 'D1';
    // 현금영수증용도
    public string $cash_gubun = '';
    // 금액[9]
    public string $amount = '';
    // 할부[2]
    public string $install = '00';
    // (취소시) 원승인일자[yymmdd]
    public string $yymmdd = '';
    // (취소시) 원승인번호[12]
    public string $appr_num = '';
    // 상품코드[2]
    public string $code = '';
    // 임시판매번호[10]
    public string $sell_num = '';
    // 웹전송메세지[N]
    public string $message = '';
    // 이지카드옵션[N]
    public string $keyin = '';
    // 멀티사업자 단말기ID[N]
    public string $multi_tid = '';
    // 타임아웃[2]
    public string $timeout = '60';
    // 부가세
    public string $vat = 'F';
    // 추가필드
    public string $addfield = '';
    // 수신핸들값
    public string $handle = '';
    // 단말기구분[2]
    public string $catgubun = '';
    // 할인/적립구분[1]
    public string $discount = '';
    // 비밀번호
    public string $passwd = '';
    // 거래확장옵션
    public string $extend = '';
    // (취소시) 거래고유번호
    public string $serialno = '';
    // 동글Flag
    public string $dongflag = '';
    // EzGW바코드
    public string $barcode = '';
    // 봉사료
    public string $tip = '';
    // 문자셋
    public string $charType = 'EUC-KR';
    // BMP String
    public string $bmp = '';
    // VAN
    public string $van = '';
    // EzGW카드번호
    public string $cardnum = '';
    // EzGW유효기간
    public string $yymm = '';
    // 승인방법구분
    public string $regtype = '';
    // 화면표시
    public string $display = '';
    // 보너스승인번호
    public string $bonusAppNum = '';
    // 정유
    public string $oil = '';
    // 토큰
    public string $token = '';
    // DSPMSG
    public string $dspmsg = '';
    // 보너스WCC
    public string $bonuswcc = '';
    // 보너스번호
    public string $bonusno = '';

    protected array $dataSort = [
        'gubun',
        'cash_gubun',
        'amount',
        'install',
        'yymmdd',
        'appr_num',
        'code',
        'sell_num',
        'message',
        'keyin',
        'multi_tid',
        'timeout',
        'vat',
        'addfield',
        'handle',
        'catgubun',
        'discount',
        'passwd',
        'extend',
        'serialno',
        'dongflag',
        'barcode',
        'tip',
        'charType',
        'bmp',
        'van',
        'cardnum',
        'yymm',
        'regtype',
        'display',
        'bonusAppNum',
        'oil',
        'token',
        'dspmsg',
        'bonuswcc',
        'bonusno',
    ];

    public function buildRequest() : string
    {
        return collect($this->dataSort)->reduce(function ($carry, $item) {
            return $carry . $this->{$item} . '^';
        }, '');
    }
}
