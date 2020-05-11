<?php

namespace App\LaravelSupports\Library\Push\Objects;

class MobileResultObject {
    var $multicast_id;
    var $success;
    var $failure;
    var $canonical_ids;
    /**
     * [
     *  { error : String } | { message_id : String }
     * ]
     * @author  : WilsonParker
     * @added   : 2019-04-11
     * @updated : 2019-04-11
     */
    var $results;

    function bind($data){
        $json = json_decode($data);
        if($json != null){
            $this->multicast_id = $json->multicast_id;
            $this->success = $json->success;
            $this->failure = $json->failure;
            $this->canonical_ids = $json->canonical_ids;
            $this->results = $json->results;
        }
    }

}
