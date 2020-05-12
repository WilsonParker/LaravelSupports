<?php

namespace LaravelSupports\Library\Push\Services;

use LaravelSupports\Library\Push\Helpers\PushHelper;
use LaravelSupports\Library\Push\Models\AbstractMobilePushModel;
use LaravelSupports\Library\Push\Models\MobilePushTokenModel;
use LaravelSupports\Library\Push\Objects\MobileResultObject;
use Illuminate\Support\Arr;

class MobilePushService
{
    /**
     * Push 후 올바른 값이 아닌 token 을 제거할 지 여부 값 입니다
     *
     * @type    boolean
     * @author  WilsonParker
     * @added   2019-04-15
     * @updated 2019-04-15
     */
    public static $clearJunk = false;
    /**
     * 유효하지 않는 token array 입니다
     *
     * @type    array
     * @author  WilsonParker
     * @added   2019-04-15
     * @updated 2019-04-15
     */
    private static $junkTokens = array();
    /**
     * Push 결과를 저장합니다
     *
     * @author  WilsonParker
     * @added   2019-04-15
     * @updated 2019-04-15
     */
    private static $result;
    /**
     * token 을 chunk 해서 Push 를 보낼 갯수 입니다
     *
     * @type    int
     * @author  WilsonParker
     * @added   2019-04-15
     * @updated 2019-04-15
     */
    private const chunkSize = 1000;

    /**
     * 수신 동의한 모든 Android, IOS token 들에 푸시를 보내고 결과를 return 합니다
     *
     * @param AbstractMobilePushModel $model
     * 푸시 정보가 담겨있는 Model 객체 입니다
     *
     * @param   $callback : function (MobilePushResultModel $resultModel, $carry) : mixed
     * MobileResultModel, $carry 을 parameter 로 받고
     * $carry 를 return 합니다
     *
     * @param array $carry =
     * [
     *  'success' => 0,
     *  'failure' => 0
     * ]
     * 결과 값을 저장하면서 축척해나갈 초기 값 입니다
     *
     * @return  null | mixed
     * @author  WilsonParker
     * @added   2019-04-11
     * @updated 2019-04-11
     */
    public static function pushAll(AbstractMobilePushModel $model, $callback, $carry = ['success' => 0, 'failure' => 0])
    {
        self::$result = $carry;
        /**
         * android, ios push 를 하면서 공통으로 실행할 callback 입니다
         *
         * @param   $items
         * Database 결과 값 입니다
         * @param   $resultCallback
         * 결과 값을 생성할 callback 입니다
         * @author  WilsonParker
         * @added   2019-04-15
         * @updated 2019-04-15
         */
        $runCallback = function ($items, $resultCallback) use ($model, $callback, $carry) {
            $tokens = $items->map(function ($item) use ($items) {
                return $item->token;
            });
            $resultModel = new MobileResultObject();
            $resultModel->bind($resultCallback($model, $tokens));
            if (self::$clearJunk) {
                self::setJunkTokens($resultModel->results, $tokens, self::$junkTokens);
            }
            self::$result = $callback($resultModel, self::$result);
        };
        self::$result = self::androidPush($runCallback, self::$result);
        self::$result = self::iosPush($runCallback, self::$result);
        // [ 'success' => 1, 'failure' => 1 ] 와 같은 $result 가 생성됩니다
        if (self::$clearJunk) {
            $clearResult = MobilePushTokenModel::whereIn("token", self::$junkTokens)->delete();
        }
        return self::$result;

    }

    private static function androidPush($callback, $carry)
    {
        self::$result = $carry;
        $resultCallback = function ($model, $tokens) {
            return PushHelper::androidPush($model, $tokens->toArray());
        };
        self::
        where("os_type", "=", "a")->
        where("agreement", "=", 1)->
        chunk(self::chunkSize, function ($items) use ($callback, $resultCallback) {
            $callback($items, $resultCallback);
        });
        return self::$result;
    }

    private static function iosPush($callback, $carry)
    {
        self::$result = $carry;
        $resultCallback = function ($model, $tokens) {
            return PushHelper::iosPush($model, $tokens->toArray());
        };
        self::
        where("os_type", "=", "i")->
        where("agreement", "=", 1)->
        chunk(self::chunkSize, function ($items) use ($callback, $resultCallback) {
            $callback($items, $resultCallback);
        });
        return self::$result;
    }

    /**
     * FCM Push 결과에서 올바르지 않는 token 을 $junkTokens 에 채웁니다
     *
     * @param   $results
     * FCM 결과 array 입니다
     * @param   $tokens
     * junk token 으로 이루어진 array 입니다
     * @param   $arr
     * $arr 에 junk token 을 채웁니다
     * @return void 삭제된 갯수를 return 합니다
     * 삭제된 갯수를 return 합니다
     * @author  WilsonParker
     * @added   2019-04-15
     * @updated 2019-04-15
     */
    private static function setJunkTokens($results, $tokens, &$arr)
    {
        $filterResults = Arr::where($results, function ($value, $_) {
            return isset($value->error);
        });
        $filterResultKeys = array_keys($filterResults);
        foreach ($filterResultKeys as $idx) {
            array_push($arr, $tokens[$idx]);
        }
    }

}
