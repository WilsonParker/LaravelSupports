<?php


namespace LaravelSupports\Push\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;
use LaravelSupports\Push\Models\MobileDownloadHistoryModel;
use LaravelSupports\Push\Models\MobilePushTokenModel;

class MobileHelper
{
    /**
     * mobile_push_token 을 저장합니다
     *
     * @param   $token
     * @param   $os_type
     * @param   $device_id
     * @return  Json
     * token 의 sequence 를 return 합니다
     * @author  WilsonParker
     * @added   2019-04-12
     * @updated 2019-04-12
     */
    public static function insertToken($token, $os_type, $device_id)
    {
        $model = new MobilePushTokenModel();
        $model->token = $token;
        $model->os_type = $os_type;
        $model->device_id = $device_id;
        $callback = function () use ($model) {
            $model->save();
            $result = $model->sequence;
            return self::pushResultJson(200, $result);
        };
        $failure = function ($code, $message) use ($token, $callback) {
            try {
                // Duplicate token
                if ($code == "23000") {
                    $model = MobilePushTokenModel::where('token', '=', $token)->first();
                    if ($model == null) {
                        return $callback();
                    } else {
                        $result = $model->sequence;
                        return self::pushResultJson(200, $result);
                    }
                    $result = $model->sequence;
                    return self::pushResultJson(200, $result);
                }
                return self::pushResultJson(500, $message);
            } catch (Exception $exception) {
                return self::pushResultJson(500, $message);
            }
        };
        return self::transactionQuery($callback, $failure);
    }

    /**
     * Push 결과를 json 형태로 반환합니다
     *
     * @param   $code
     * @param   $message
     * @return  String
     * @author  WilsonParker
     * @added   2019-04-12
     * @updated 2019-04-12
     */
    public static function pushResultJson($code, $message)
    {
        $re_message = preg_replace('/[\n\r\t]/', '', $message);
        return json_decode("{ \"code\" : $code , \"message\" : \"$re_message\" }");
    }

    /**
     * Transaction 을 이용하여 database 작업을 합니다
     *
     * @param   $callback
     * 실행할 작업 function 입니다
     * @param   $failure
     * 문제가 발생할 경우 실행할 function 입니다
     * @return  null | $callback 의 return 값
     * @author  WilsonParker
     * @added   2019-04-12
     * @updated 2019-04-12
     */
    public static function transactionQuery($callback, $failure)
    {
        $model = null;
        $result = null;
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $code = $e->getCode();
            $message = "
            Caught exception:
            code : $code
            message : {$e->getMessage()}
            ";
            /*if (env('APP_DEBUG')) {
                echo $message;
                exit;
            }*/
            return $failure($code, $message);
        }
        return $result;
    }

    /**
     * mobile_push_token 의 고유키를 이용하여 user_code 를 설정 합니다
     *
     * @param   $sequence
     * @param   $user_code
     * @return  String
     * @author  WilsonParker
     * @added   2019-04-12
     * @updated 2019-04-12
     */
    public static function updateTokenWithUserCode($sequence, $user_code)
    {
        $callback = function ($model) use ($user_code) {
            $model->user_code = $user_code;
        };
        return self::updateTokenModel($sequence, $callback);
    }

    private static function updateTokenModel($sequence, $setCallback)
    {
        $callback = function () use ($sequence, $setCallback) {
            $model = MobilePushTokenModel::find($sequence);
            $setCallback($model);
            $result = $model->update() == 1 ? "true" : "false";
            return self::pushResultJson(200, $result);
        };
        $failure = function ($code, $message) {
            return self::pushResultJson(500, $message);
        };
        return self::transactionQuery($callback, $failure);
    }

    /**
     * mobile_push_token 의 고유키를 이용하여 수신 동의 여부 $agreement 를 설정 합니다
     *
     * @param   $sequence
     * @param   $agreement
     * @return  String
     * @author  WilsonParker
     * @added   2019-04-12
     * @updated 2019-04-12
     */
    public static function updatePushWithAgreement($sequence, $agreement)
    {
        $callback = function ($model) use ($agreement) {
            $model->agreement = $agreement;
        };
        return self::updateTokenModel($sequence, $callback);
    }

    public static function insertDownloadHistory($os_type)
    {
        $model = new MobileDownloadHistoryModel();
        $callback = function () use ($model, $os_type) {
            $result = $model->addHistory($os_type);
            return self::pushResultJson(200, $result);
        };
        $failure = function ($code, $message) {
            return self::pushResultJson(500, $message);
        };

        return self::transactionQuery($callback, $failure);
    }

    /**
     * Push 결과를 array 로 반환합니다
     *
     * @param   $code
     * @param   $message
     * @return  array
     * @author  WilsonParker
     * @added   2019-04-12
     * @updated 2019-04-12
     */
    public static function pushResultArray($code, $message): array
    {
        return ["code" => $code, "message" => $message];
    }

}
