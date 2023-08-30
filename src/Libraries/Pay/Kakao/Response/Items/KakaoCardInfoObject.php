<?php


namespace LaravelSupports\Libraries\Pay\Kakao\Response\Items;


use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseItem;

class KakaoCardInfoObject extends AbstractResponseItem
{

    /**
     * 할인 금액
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $purchase_corp;

    /**
     * 매입 카드사 코드
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $purchase_corp_code;

    /**
     * 카드 발급사 한글명
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $issuer_corp;

    /**
     * 카드 발급사 코드
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $issuer_corp_code;

    /**
     * 카카오페이 매입사명
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $kakaopay_purchase_corp;

    /**
     * 카카오페이 매입사 코드
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $kakaopay_purchase_corp_code;

    /**
     * 카카오페이 발급사명
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $kakaopay_issuer_corp;

    /**
     * 카카오페이 발급사 코드
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $kakaopay_issuer_corp_code;

    /**
     * 카드 BIN
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $bin;

    /**
     * 카드 타입
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $card_type;

    /**
     * 할부 개월 수
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $install_month;

    /**
     * 카드사 승인번호
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $approved_id;

    /**
     * 카드사 가맹점 번호
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $card_mid;

    /**
     * 무이자할부 여부(Y/N)
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $interest_free_install;

    /**
     * 카드 상품 코드
     *
     * @var    string
     * @author  WilsonParker
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $card_item_code;

}
