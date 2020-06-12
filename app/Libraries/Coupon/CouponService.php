<?php


namespace LaravelSupports\Libraries\Coupon;


use LaravelSupports\Libraries\Coupon\Exceptions\AlreadyUsedException;
use LaravelSupports\Libraries\Coupon\Exceptions\NotMetConditionException;
use App\Models\Coupons\CouponModel;
use App\Models\Coupons\CouponUsedMemberModel;

class CouponService
{
    private CouponModel $coupon;
    private $member;

    /**
     * CouponService constructor.
     *
     * @param CouponModel $coupon
     * @param $member
     */
    public function __construct(CouponModel $coupon, $member)
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
        if (isset($this->coupon) && $this->isNotCouponUsed($throwException) && $this->coupon->isAvailable($throwException)) {
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
        return $this->getBenefitCallback($benefit->getCouponTypeCode())($benefit, $this->member, $data);
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

    public function isCouponUsed()
    {
        return CouponUsedMemberModel::isUsed($this->coupon->id, $this->member->id);
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
        $isUsed = $this->isCouponUsed();
        throw_if($throwException && $isUsed, new AlreadyUsedException());
        return !$isUsed;
    }

    protected function getCondition($condition)
    {
        return config('coupon.condition_types.' . $condition);
    }

    protected function getConditionCallback($condition)
    {
        return $this->getCondition($condition)['callback'];
    }

    protected function getBenefit($benefit)
    {
        return config('coupon.benefit_types.' . $benefit);
    }

    protected function getBenefitCallback($benefit)
    {
        return $this->getBenefit($benefit)['callback'];
    }

    /**
     * return coupon code
     *
     * @param
     * @return mixed|null
     * @throws \Throwable
     * @author  dew9163
     * @added   2020/06/11
     * @updated 2020/06/11
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
}
