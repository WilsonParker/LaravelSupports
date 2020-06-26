<?php


namespace LaravelSupports\Libraries\Pay\Kakao\Response\Items;



use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseItem;

class KakaoAmountObject extends AbstractResponseItem
{
    /**
     * 전체 결제 금액
     *
     * @var    int
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $total;

    /**
     * 비과세 금액
     *
     * @var    int
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $tax_free;

    /**
     * 부가세 금액
     *
     * @var    int
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $vat;

    /**
     * 사용한 포인트 금액
     *
     * @var    int
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $point;

    /**
     * 할인 금액
     *
     * @var    int
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $discount;
}
