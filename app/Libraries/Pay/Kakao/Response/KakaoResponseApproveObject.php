<?php


namespace LaravelSupports\Libraries\Pay\Kakao\Response;


use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseApproveObject;
use LaravelSupports\Libraries\Pay\Kakao\Response\Items\KakaoAmountObject;
use LaravelSupports\Libraries\Pay\Kakao\Response\Items\KakaoCardInfoObject;


/*
    {#686 ▼
      +"aid": "A2772998729560879439"
      +"tid": "T2772998617893363799"
      +"cid": "C009320091"
      +"sid": "S2772998763922404468"
      +"partner_order_id": "69"
      +"partner_user_id": "151400"
      +"payment_method_type": "CARD"
      +"item_name": "스텐다드 1개월 구독"
      +"quantity": 1
      +"amount": {#688 ▼
        +"total": 6900
        +"tax_free": 0
        +"vat": 0
        +"point": 0
        +"discount": 0
      }
      +"card_info": {#1648 ▼
        +"approved_id": "123123"
        +"bin": "123123"
        +"card_mid": "123132"
        +"card_type": "신용"
        +"install_month": "00"
        +"issuer_corp": "현대카드"
        +"issuer_corp_code": "08"
        +"purchase_corp": "현대카드"
        +"purchase_corp_code": "08"
        +"interest_free_install": "N"
        +"kakaopay_purchase_corp": "현대카드"
        +"kakaopay_purchase_corp_code": "706"
        +"kakaopay_issuer_corp": "현대카드"
        +"kakaopay_issuer_corp_code": "106"
      }
      +"created_at": "2020-06-16T16:10:54"
      +"approved_at": "2020-06-16T16:11:27"
    }
 * */
class KakaoResponseApproveObject extends AbstractResponseApproveObject
{

    /**
     * 요청 고유 번호
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $aid;

    /**
     * 결제 고유 번호, 20자
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $tid;

    /**
     * 가맹점 코드
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $cid;

    /**
     * 정기결제용 ID, 정기결제 CID로 단건결제 요청 시 발급
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $sid;

    /**
     * 가맹점 주문번호, 최대 100자
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $partner_order_id;

    /**
     * 가맹점 회원 id, 최대 100자
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $partner_user_id;

    /**
     * 결제 수단, CARD 또는 MONEY 중 하나
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $payment_method_type;

    /**
     * 결제 금액 정보
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $amount;

    /**
     * 결제 상세 정보, 결제수단이 카드일 경우만 포함
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $card_info;

    /**
     * 상품 이름, 최대 100자
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $item_name;

    /**
     * 상품 코드, 최대 100자
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $item_code;

    /**
     * 상품 수량
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $quantity;

    /**
     * 결제 준비 요청 시간
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $created_at;

    /**
     * 결제 승인 시각
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $approved_at;

    /**
     * 결제 승인 요청에 대해 저장한 값, 요청 시 전달된 내용
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $payload;

    public function bindAmount($json)
    {
        $obj = new KakaoAmountObject();
        $obj->bindJson($json);
        $this->amount = $obj;
    }

    public function bindCardInfo($json)
    {
        $obj = new KakaoCardInfoObject();
        $obj->bindJson($json);
        $this->amount = $obj;
    }

    public function bindStd($std)
    {
        parent::bindStd($std);
        $this->bindAmount(json_encode($this->amount, true));
        $this->bindCardInfo(json_encode($this->card_info, true));
    }

    public function bindJson($json)
    {
        parent::bindJson($json);
        $this->bindAmount(json_encode($this->amount, true));
        $this->bindCardInfo(json_encode($this->card_info, true));
    }

    public function getResult()
    {

    }


}
