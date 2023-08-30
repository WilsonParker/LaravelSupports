<?php


namespace LaravelSupports\Libraries\Pay\Kakao\Response;


use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseApproveObject;
use LaravelSupports\Libraries\Pay\Kakao\Response\Items\KakaoAmountObject;
use LaravelSupports\Libraries\Pay\Kakao\Response\Items\KakaoCardInfoObject;

class KakaoResponseOrderObject extends AbstractResponseApproveObject
{
    /**
     * 결제 고유 번호, 20자
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $tid;

    /**
     * 가맹점 코드
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $cid;

    /**
     * 결제상태
     *
     * @type    string
     * @author  seul
     * @added   2021-01-13
     * @updated 2021-01-13
     */
    public $status;

    /**
     * 가맹점 주문번호, 최대 100자
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $partner_order_id;

    /**
     * 가맹점 회원 id, 최대 100자
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $partner_user_id;

    /**
     * 결제 수단, CARD 또는 MONEY 중 하나
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $payment_method_type;

    /**
     * 결제 금액 정보
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $amount;

    /**
     * 취소된 금액
     *
     * @type    string
     * @author  seul
     * @added   2021-01-13
     * @updated 2021-01-13
     */
    public $canceled_amount;

    /**
     * 취소 가능 금액
     *
     * @type    string
     * @author  seul
     * @added   2021-01-13
     * @updated 2021-01-13
     */
    public $cancel_available_amount;

    /**
     * 상품 이름, 최대 100자
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $item_name;

    /**
     * 상품 코드, 최대 100자
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $item_code;

    /**
     * 상품 수량
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $quantity;

    /**
     * 결제 준비 요청 시간
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $created_at;

    /**
     * 결제 승인 시각
     *
     * @type    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $approved_at;

    /**
     * 결제 취소 시각
     *
     * @type    string
     * @author  seul
     * @added   2021-01-13
     * @updated 2021-01-13
     */
    public $canceled_at;

    /**
     * 결제 카드 정보
     *
     * @type    string
     * @author  seul
     * @added   2021-01-13
     * @updated 2021-01-13
     */
    public $selected_card_info;

    /**
     * 결제 카드 정보
     *
     * @type    string
     * @author  seul
     * @added   2021-01-13
     * @updated 2021-01-13
     */
    public $payment_action_details;

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
        $this->bindCardInfo(json_encode($this->selected_card_info, true));
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

    public function isPaymentSuccess()
    {
        return $this->status == 'SUCCESS_PAYMENT';
    }

}
