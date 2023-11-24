<?php


namespace LaravelSupports\Push\Helpers;


use Exception;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use LaravelSupports\Http\RestCall;
use LaravelSupports\Push\Models\AbstractMobilePushModel;
use LaravelSupports\Push\Models\MobilePushTokenModel;

class PushHelper
{
    // FCM server key
    private static $serverKey = "AAAAtBZdUII:APA91bGY9g8Wa65ufEbHI_I8OKlihaEhsOsU44a5AldAehgAbLEQ8EfCht3iyg9O1KYINEMfC4M0TtnoSkmvSqoO1KqxzkkO0c4yUKBeoib28deBpun71gzvA1HMZtEnyJBx2xTv5Xm5";

    // FCM send url
    private static $url = "https://fcm.googleapis.com/fcm/send";

    /**
     * 푸시 정보를 가지고 있는 $model 을 이용해서 푸시를 한 후 array 결과를 return 합니다
     *
     * @param AbstractMobilePushModel $model
     * @return  array
     * @author  WilsonParker
     * @added   2019-04-12
     * @updated 2019-04-12
     */
    public static function pushAll(AbstractMobilePushModel $model)
    {
        /*
         * 실행 결과를 저장할 callback 함수 입니다
         */
        $callback = function ($resultModel, $carry) {
            $carry['success'] += $resultModel->success;
            $carry['failure'] += $resultModel->failure;
            return $carry;
        };

        $transactionCallback = function () use ($model, $callback) {
            $result = MobilePushTokenModel::pushAll($model, $callback);
            $model->setResult($result);
            $isSuccess = $model->execute();
            return self::pushResultArray(200, "success : {$result['success']}, failure : {$result['failure']}, isSuccess : $isSuccess");
        };

        $transactionFailure = function ($code, $message) {
            return self::pushResultArray(500, $message);
        };

        return self::transactionQuery($transactionCallback, $transactionFailure);
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
            {$e->getTraceAsString()}
            ";
            /*if (env('APP_DEBUG')) {
                echo $message;
                exit;
            }*/

            $logger = new Logger();
            $logger->Logging("logs/Kernel/Schedule/MobilePushBatch", "MobilePushBatch-" . date("Y-m-d"), "TraceAsString : {{$e->getTraceAsString()}} \nCode : $code, Message: {$e->getMessage()} ", true);
            return $failure($code, $message);
        }
        return $result;
    }

    /**
     * FirebaseCloudMessaging 을 REST 방식으로 전달 합니다
     *
     * @param AbstractMobilePushModel $model
     * @param array $tokens
     * @return  bool|string
     * @author  WilsonParker
     * @added   2019-04-12
     * @updated 2019-04-12
     */
    public static function androidPush(AbstractMobilePushModel $model, array $tokens)
    {
        $type = $model->contents_type;
        $title = $model->push_title;
        $content = $model->contents;
        // $link = UrlUtil::extractUrl($model->link);
        $link = $model->link;
        $imageUrl = $model->imageUrl;
        $fileResult = null;

        $curl = new RestCall();
        $headers = array(
            "Authorization:key=" . self::$serverKey,
            "Content-Type:application/json"
        );

        $token = implode("\",\"", $tokens);

        $data = "
          {
              \"registration_ids\" : [
                \"$token\"
                ],
              \"data\": {
                \"push_type\" : \"$type\",
                \"title\" :\"$title\",
                \"content\" : \"$content\",
                \"image_url\" : \"$imageUrl\",
                \"link\" : \"$link\"
              } ,
              \"android\":{
                  \"priority\":\"high\"
                }
           }
         ";
        $result = $curl->post(self::$url, $data, false, $headers);
        return $result;
    }

    public static function iosPush(AbstractMobilePushModel $model, array $tokens)
    {
        $type = $model->contents_type;
        $title = $model->push_title;
        $content = $model->contents;
        $link = $model->link;
        $imageUrl = $model->imageUrl;
        $fileResult = null;

        $curl = new RestCall();
        $headers = array(
            "Authorization:key=" . self::$serverKey,
            "Content-Type:application/json"
        );

        $token = implode("\",\"", $tokens);

        $data = "
            \"title\" :\"$title\",
            \"body\" : \"$content\",
            \"isImageInclude\" : \"true\"
            \"imageUrl\" : \"$imageUrl\",
            \"hyperlink\" : \"$link\"
        ";
        $iosBody = "
        {
            \"collpase_key\" : \"enter6admintest\",
            \"notification\" : {
                $data
            },
            \"data\" : {
                $data
            },
            \"apns\" : {
                \"headers\" : {
                    \"apns-priority\" : 10,
                    \"apns-collapse-id\" : \"enter6admintest\",
                },
            },
            \"registration_ids\" : [
                \"$token\"
            ],
        }
        ";
        $result = $curl->post(self::$url, $iosBody, false, $headers);
        return $result;
    }

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
        return json_decode("{ \"code\" : $code , \"message\" : $message }");
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
            return response()->json(self::pushResultJson(200, $result), 200);
        };
        $failure = function ($code, $message) {
            return response()->json(self::pushResultJson(500, $message), 500);
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

}
