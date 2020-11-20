<?php

namespace LaravelSupports\Libraries\Push\Objects;

class MobileResultObject {
    var $multicast_id;
    var $success;
    var $failure;
    var $canonical_ids;
    var $successList;
    var $failureList;
    /**
     * [
     *  { error : String } | { message_id : String }
     * ]
     * @author  : WilsonParker
     * @added   : 2019-04-11
     * @updated : 2019-04-11
     */
    var $results;
    var $additionalResults;

    function bind($data, $list){
        $json = json_decode($data);
        if($json != null){
            $this->multicast_id = $json->multicast_id;
            $this->success = $json->success;
            $this->failure = $json->failure;
            $this->canonical_ids = $json->canonical_ids;
            $this->results = $json->results;

            $this->additionalResults = collect($this->results)->transform(function ($item, $key) use($list) {
                $item->id = $list[$key];
                return collect((array) $item);
            });

            $filteredList = $this->additionalResults->divide(function ($item) {
                return !isset($item['error']);
            });
            $this->successList = $filteredList['true'];
            $this->failureList = $filteredList['false'];

        }
    }

}
