<?php


namespace LaravelSupports\Libraries\Coupon;


use App\Renewal\Models\Coupons\CouponModel;
use App\Renewal\Models\Coupons\CouponUsedMemberModel;
use App\Renewal\Models\Membership\ConvertMarkMessageTrait;
use App\Renewal\Models\Membership\MembershipModel;
use App\Renewal\Models\Membership\MembershipPriceModel;
use LaravelSupports\Libraries\Coupon\Exceptions\AlreadyUsedException;
use LaravelSupports\Libraries\Coupon\Exceptions\DuplicatedException;
use LaravelSupports\Libraries\Coupon\Exceptions\NotMetConditionException;
use App\Supports\Date\DateHelper;

class CouponService
{
    use ConvertMarkMessageTrait;

    const DATE_FORMAT = 'Y.m.d';
    const TYPE_POINT = 'point';
    const TYPE_SERVICE = 'service';
    const TYPE_DISCOUNT = 'discount';
    const PROVIDE_MEMBERSHIP = 'provide_membership';
    const PROVIDE_REFERENCE_MEMBERSHIP = 'provide_reference_membership';

    protected $coupon;
    protected $member;

    protected $needMoreInformation = [
        'standard' => [
            'user'
        ],
        'premium' => [
            'user',
            'address',
        ],
        'subscribe' => [
            'card'
        ],
    ];

    /**
     * CouponService constructor.
     *
     * @param CouponModel $coupon
     * @param $member
     */
    public function __construct($coupon, $member)
    {
        $this->coupon = $coupon;
        $this->member = $member;
    }

    /**
     * use coupon
     *
     * @param $data
     * Price
     * MemberModel
     * @param bool $throwException
     * @return bool|null
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/06/09
     * @updated 2020/06/09
     */
    public function useCoupon($data = null, $throwException = false)
    {
        if ($this->isUsable($data, $throwException)) {
            $result = $this->provideBenefit($data);
            $this->userCouponUsed();
            return $result;
        }
        return false;
    }

    /**
     * coupon is usable
     *
     * @param $data
     * @param bool $throwException
     * @return bool
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/06/09
     * @updated 2020/06/09
     * @updated 2020/06/11
     */
    public function isUsable($data = null, $throwException = false)
    {
        if (isset($this->coupon)
            && $this->isNotCouponUsed($throwException)
            && $this->isNotDuplicateCoupon($throwException)
            && $this->coupon->isAvailable($throwException)
        ) {
            return $this->coupon->conditions->reduce(function ($carry, $item) use ($data) {
                $value = $this->getConditionCallback($item->getCouponTypeCode())($item, $this->member, $data);
                return $item->operator == 'and' ? $carry && $value : $carry || $value;
            }, true);
        }
        return false;
    }

    /**
     * provide benefit
     *
     * @param $data
     * @return
     * @author  dew9163
     * @added   2020/06/09
     * @updated 2020/06/09
     */
    public function provideBenefit($data)
    {
        $benefit = $this->coupon->benefit;
        return $this->getBenefitCallback($benefit->getCouponTypeCode())($this->coupon, $benefit, $this->member, $data);
    }

    /**
     * call when user coupon used
     *
     * @return void
     * @author  dew9163
     * @added   2020/06/09
     * @updated 2020/06/09
     */
    public function userCouponUsed()
    {
        $this->coupon->addUsedCount();
        CouponUsedMemberModel::createModel($this->coupon->id, $this->member->id);
    }

    /**
     * Check if you used a coupon
     *
     * @param bool $throwException
     * @return bool
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/06/11
     * @updated 2020/06/11
     */
    public function isNotCouponUsed($throwException = false)
    {
        $isUsed = CouponUsedMemberModel::isUsed($this->coupon->id, $this->member->id);
        throw_if($throwException && $isUsed, new AlreadyUsedException());
        return !$isUsed;
    }

    /**
     * Check if the coupon is duplicate with "duplicate_code"
     *
     * @param bool $throwException
     * @return bool
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/06/26
     * @updated 2020/06/26
     */
    public function isNotDuplicateCoupon($throwException = false)
    {
        $isDuplicated = $this->member->usedCoupons()->where('duplicate_code', $this->coupon->getDuplicateCode())->exists();
        throw_if($isDuplicated && $throwException, new DuplicatedException());
        return !$isDuplicated;
    }

    /**
     * Check if the coupon can be used immediately
     *
     * @return
     * @author  dew9163
     * @added   2020/06/22
     * @updated 2020/06/22
     */
    public function isNeedMoreData()
    {
        return $this->coupon->benefit->isNeedMoreData();
    }

    /**
     * get coupon code if exists
     *
     * @param
     * @return mixed|null
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/06/12
     * @updated 2020/06/12
     */
    public function getCode($data)
    {
        if (isset($this->coupon)) {
            $isUsable = $this->isUsable($data, true);
            throw_if(!$isUsable, new NotMetConditionException());
            return $this->coupon->getUniqueValue();
        } else {
            return null;
        }
    }

    /**
     * @return CouponModel
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @return mixed
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Membership type 을 제공 합니다
     *
     * @return
     * @author  dew9163
     * @added   2020/06/23
     * @updated 2020/06/23
     * @example
     * standard
     * premium
     *
     */
    public function getMembershipType()
    {
        return $this->getMembershipModel()->code;
    }

    /**
     * return Benefit Model
     *
     * @return mixed
     * @author  dew9163
     * @added   2020/06/23
     * @updated 2020/06/23
     */
    public function getBenefitModel()
    {
        return $value = $this->coupon->benefit;
    }

    /**
     * Membership Model 을 제공 합니다
     *
     * @return
     * @author  dew9163
     * @added   2020/06/23
     * @updated 2020/06/23
     */
    public function getMembershipModel()
    {
        $benefit = $this->getBenefitModel();
        $code = $benefit->ref_coupon_benefit_type_code;
        $value = $benefit->value;
        if ($code == self::PROVIDE_MEMBERSHIP) {
            return MembershipModel::getModel($value);
        } else if ($code == self::PROVIDE_REFERENCE_MEMBERSHIP) {
            return MembershipPriceModel::getModel($value)->membership;
        }
    }

    /**
     * get required data information to collection
     *
     * @return mixed
     * @author  dew9163
     * @added   2020/06/22
     * @updated 2020/06/22
     * @example
     * user
     * address
     * card
     */
    public function getRequiredDataInformation()
    {
        return collect(array_keys($this->needMoreInformation))->filter(function ($item) {
            return str_contains($this->coupon->benefit->value, $item);
        })->pipe(function ($collection) {
            return $collection->map(function ($item) {
                return $this->needMoreInformation[$item];
            })->collapse();
        });
    }

    /**
     * 혜택이 제공하는 Months 정보를 제공합니다
     *
     * @return |null
     * @author  dew9163
     * @added   2020/06/24
     * @updated 2020/06/24
     */
    public function getBenefitMonths()
    {
        $benefit = $this->getBenefitModel();
        $code = $benefit->ref_coupon_benefit_type_code;
        $dateHelper = new DateHelper();
        if ($code == self::PROVIDE_MEMBERSHIP) {
            return $dateHelper->convertDateInteger($benefit->getCouponSubValue(), $benefit->getCouponThirdValue(), DateHelper::MONTHS);
        } else if ($code == self::PROVIDE_REFERENCE_MEMBERSHIP) {
            return MembershipPriceModel::getModel($benefit->getCouponValue())->getNumber();
        }
    }

    /**
     * API
     * 결제 완료 시 결과 데이터를 제공 합니다
     *
     * @return mixed
     * @author  dew9163
     * @added   2020/06/17
     * @updated 2020/06/17
     * @updated 2020/06/24
     * 최초 결제, 연장 에 따른 메시지 변경
     */
    public function getCouponResult()
    {
        $data = null;
        switch ($this->coupon->getCouponTypeCode()) {
            case self::TYPE_SERVICE:
                $memberModel = $this->member;
                $membershipModel = $memberModel->getMembership();
                $plusMemberModel = $memberModel->bplus_member;
                $dateHelper = new DateHelper();
                $availableDate = $dateHelper->formatDate($plusMemberModel->available_date, self::DATE_FORMAT);
                $expirationDate = $dateHelper->formatDate($plusMemberModel->expiration_date, self::DATE_FORMAT);

                // 첫 결제 일 경우
                $isFirstPlus = $memberModel->isFirstPlus();
                if ($isFirstPlus) {
                    $data['message'] = $this->replaceMessageArray(config("api.membership.pay.messages.success_join_membership"), [$memberModel->realname, $memberModel->getMembership()->name]);
                } else {
                    $data['message'] = $this->replaceMessageArray(config("api.membership.pay.messages.success_extend_membership"), [$memberModel->realname, $memberModel->getMembership()->name]);
                }
                $data['membership'] = [
                    'is_first' => $isFirstPlus,
                    'name' => $membershipModel->name,
                    'description' => $membershipModel->description,
                    'available_date' => $availableDate,
                    'expiration_date' => $expirationDate,
                    'use_date' => $availableDate . " ~ " . $expirationDate,
                ];
                if ($memberModel->isSubscribe()) {
                    $data['membership']['next_payment_date'] = $dateHelper->getDateTheMonthAdded(1, self::DATE_FORMAT);
                }
                if ($membershipModel == 'premium' && $isFirstPlus) {
                    $data['membership']['first_delivery_date'] = MembershipModel::getFirstDeliveryDate(self::DATE_FORMAT);
                }
                break;
            case self::TYPE_POINT:
                break;
        }

        return $data;
    }

    protected function getConditionConfig($condition)
    {
        return config('coupon.condition_types.' . $condition);
    }

    protected function getConditionCallback($condition)
    {
        return $this->getConditionConfig($condition)['callback'];
    }

    protected function getBenefitConfig($benefit)
    {
        return config('coupon.benefit_types.' . $benefit);
    }

    protected function getBenefitCallback($benefit)
    {
        return $this->getBenefitConfig($benefit)['callback'];
    }

}
