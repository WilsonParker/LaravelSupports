<?php


namespace LaravelSupports\Libraries\Coupon;


use App\Models\Coupons\CouponModel;
use App\Models\Coupons\CouponUsedMemberModel;
use LaravelSupports\Libraries\Supports\Auth\AuthHelper;

class CouponService
{
    private CouponModel $coupon;
    private $member;

    /**
     * CouponService constructor.
     *
     * @param $coupon
     */
    public function __construct(CouponModel $coupon)
    {
        $this->coupon = $coupon;
        $this->member = AuthHelper::getAuthUser();
    }


    /**
     * use coupon
     *
     * @param $data
     * Price
     * MemberModel
     * @return bool|null
     * @author  dew9163
     * @added   2020/06/09
     * @updated 2020/06/09
     */
    public function useCoupon($data = null)
    {
        if ($this->isUsable($data)) {
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
     * @return bool
     * @author  dew9163
     * @added   2020/06/09
     * @updated 2020/06/09
     */
    public function isUsable($data = null)
    {
        if ($this->coupon->isAvailable() && $this->isNotCouponUsed()) {
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

    public function isNotCouponUsed()
    {
        return !$this->isCouponUsed();
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
}
