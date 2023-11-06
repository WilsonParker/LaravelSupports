<?php


namespace LaravelSupports\Coupon\Traits;


trait CouponAttributesTraits
{

    public function getUniqueValue()
    {
        return $this->{$this->getUniqueName()};
    }

    public function getUniqueName()
    {
        return 'id';
    }

    public function getCouponValue()
    {
        return $this->{$this->getValueKey()};
    }

    public function getValueKey()
    {
        return 'value';
    }

    public function getCouponSubValue()
    {
        return $this->{$this->getSubValueKey()};
    }

    public function getSubValueKey()
    {
        return 'sub_value';
    }

    public function getCouponThirdValue()
    {
        return $this->{$this->getThirdValueKey()};
    }

    public function getThirdValueKey()
    {
        return 'third_value';
    }

    public function getDescription()
    {
        return $this->{$this->getDescriptionKey()};
    }

    public function getDescriptionKey()
    {
        return 'description';
    }
}
