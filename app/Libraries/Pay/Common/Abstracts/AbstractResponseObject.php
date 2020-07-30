<?php


namespace LaravelSupports\Libraries\Pay\Common\Abstracts;


use LaravelSupports\Libraries\Supports\Objects\ReflectionObject;

abstract class AbstractResponseObject extends ReflectionObject
{
    protected $aid = '';
    protected $tid = '';
    protected $cid = '';
    protected $uid = '';
    protected $sid = '';
    protected $payment_method_type = '';
    protected $pg_tid = '';
    protected $pg_provider = '';
    protected $token = '';
    protected $status = '';

    abstract public function getResult();

    /**
     * @return string
     */
    public function getAID()
    {
        return $this->aid;
    }

    /**
     * @param string $aid
     */
    public function setAID($aid)
    {
        $this->aid = $aid;
    }

    /**
     * @return string
     */
    public function getTID()
    {
        return $this->tid;
    }

    /**
     * @param string $tid
     */
    public function setTID($tid)
    {
        $this->tid = $tid;
    }

    /**
     * @return string
     */
    public function getCID()
    {
        return $this->cid;
    }

    /**
     * @param string $cid
     */
    public function setCID($cid)
    {
        $this->cid = $cid;
    }

    /**
     * @return string
     */
    public function getSID()
    {
        return $this->sid;
    }

    /**
     * @param string $sid
     */
    public function setSID($sid)
    {
        $this->sid = $sid;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->payment_method_type;
    }

    /**
     * @param string $payment_method_type
     */
    public function setPaymentMethodType($payment_method_type)
    {
        $this->payment_method_type = $payment_method_type;
    }

    /**
     * @return string
     */
    public function getPgTid()
    {
        return $this->pg_tid;
    }

    /**
     * @param string $pg_tid
     */
    public function setPgTid($pg_tid)
    {
        $this->pg_tid = $pg_tid;
    }

    /**
     * @return string
     */
    public function getPgProvider()
    {
        return $this->pg_provider;
    }

    /**
     * @param string $pg_provider
     */
    public function setPgProvider($pg_provider)
    {
        $this->pg_provider = $pg_provider;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function isSuccess() {
        return false;
    }

}
