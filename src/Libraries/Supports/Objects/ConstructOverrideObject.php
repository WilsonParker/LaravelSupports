<?php

namespace LaravelSupports\Libraries\Supports\Objects;

use LaravelSupports\Libraries\Supports\Objects\Contracts\Initializable;


/**
 * Construct Overloading 구현을 하기 위한 Class 입니다
 * default construct 를 Override 할 경우 기능이 실행되지 않습니다
 *
 * @author  WilsonParker
 * @class   ConstructOverrideObject.php
 * @added   2019.03.05
 * @updated 2019.03.05

 */
class ConstructOverrideObject
{

    /**
     * 객체를 생성하면서 전달한 Arguments 갯수에 따라
     * __construct1, __contruct2 .. 를 실행합니다
     *
     * @param   Arguments
     * @return  Object
     * @author  WilsonParker
     * @added   2019.03.05
     * @updated 2019.03.05
     * @bug
     * @see
     */
    public function __construct(){
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }
        $this->init();
    }

    /**
     * Construct 가 실행된 후 실행되는 초기화 함수 입니다
     *
     * @param
     * @return  Void
     * @author  WilsonParker
     * @added   2019.03.05
     * @updated 2019.03.05
     * @bug
     * @see
     */
    protected function init(){

    }

}
