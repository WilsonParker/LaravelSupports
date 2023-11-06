<?php

namespace LaravelSupports\Crypt;

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use Illuminate\Support\Facades\Crypt;

/**
 * @class   CryptHelper.php
 * @author  WilsonParker
 * @brief   ValidationModel 과 사용해야 합니다
 * property 를 get 할 경우 복호화를
 * set 할 경우 암호화를 합니다
 * @create  20190104
 * @update  20190104
 **/
trait CryptHelper
{
    protected $get_state = "eloquent";
    protected $set_state = "default";
    protected $def_get = false
    , $def_set = false;
    protected $enc = [];
    private $data = array();
    /**
     * @author  WilsonParker
     * @brief
     * default
     * custom
     * eloquent
     * @var
     **/
    private $DEFAULT = "default"
    , $CUSTOM = "custom"
    , $ELOQUENT = "eloquent";

    /**
     * @param String $property
     * @return  String
     * @create  20190104
     * @update  20190104
     **@author  WilsonParker
     * @brief
     * Property 를 가져갈 경우 암호화가 되어있는 컬럼인지(array $enc) 확인하여 복호화를 해줍니다
     */
    public function __get($property)
    {
        switch ($this->get_state) {
            case $this->ELOQUENT:
                if (isset($property)) {
                    return $this->isEnc($property) ? Crypt::decrypt($this[$property]) : $this[$property];
                } else {
                    throw new UnexpectedValueException('Undefined property via __get(): ' . $property);
                }
                break;
            case $this->CUSTOM :
                if (array_key_exists($property, $this->data)) {
                    return $this->isEnc($property) ? Crypt::decrypt($this->data[$property]) : $this->data[$property];
                } else {
                    throw new UnexpectedValueException('Undefined property via __get(): ' . $property);
                }
                break;
            case $this->DEFAULT :
            default :
                return parent::__get($property);
        }

    }

    /**
     * @param String $property
     * @param Mixed $value
     * @create  20190104
     * @update  20190104
     **@author  WilsonParker
     * @brief
     */
    public function __set($property, $value)
    {
        switch ($this->set_state) {
            case $this->CUSTOM:
                $this->data[$property] = $this->isEnc($property) ? Crypt::encrypt($value) : $value;
                break;
            case $this->DEFAULT:
            default:
                parent::__set($property, $value);
                break;
        }
    }

    /**
     * @param String $property
     * @return  Boolean
     * @create  20190104
     * @update  20190104
     **@author  WilsonParker
     * @brief   암호화가 필요한 property 인지 확인합니다
     * $property 가 $enc 에 포함되어있는지 확인하여 Booelan 값을 return 합니다
     */
    private function isEnc($property)
    {
        return in_array($property, $this->enc);
    }

    /**
     * @param String $key
     * @param Mixed $val
     * @return  String
     * @create  20190104
     * @update  20190104
     **@author  WilsonParker
     * @brief   이 CryptHelp 를 use 하고 있는 곳이 RequestValueCastImlpl 을 implements 하였기 때문에 구현해줍니다
     * isEnc($key) function 을 이용하여 암호화할 필요가 있는지 확인 후 암호화된 값을 return 합니다
     */
    public function castValue(string $key, $val)
    {
        return $this->isEnc($key) ? Crypt::encrypt($val) : $val;
    }
}
