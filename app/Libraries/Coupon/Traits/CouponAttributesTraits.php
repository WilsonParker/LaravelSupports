<?php


namespace App\Library\LaravelSupports\app\Libraries\Coupon\Traits;


trait CouponAttributesTraits
{

    public function getUniqueName()
    {
        return 'id';
    }

    public function getValueKey()
    {
        return 'value';
    }

    public function getSubValueKey()
    {
        return 'sub_value';
    }

    public function getUniqueValue()
    {
        return $this->{$this->getUniqueName()};
    }

    public function getCouponValue()
    {
        return $this->{$this->getValueKey()};
    }

    public function getCouponSubValue()
    {
        return $this->{$this->getSubValueKey()};
    }
}
