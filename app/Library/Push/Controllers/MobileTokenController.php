<?php


namespace App\Library\Push\Controllers;

use App\Http\Controllers\Controller;
use App\Library\Push\Helpers\MobileHelper;
use App\Library\Push\Models\MobilePushTokenModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileTokenController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('mobile-api');
    }

    public function insertToken(Request $request)
    {
        $token = $request->input("token");
        $device_id = $request->input("device_id", "");
        $os_type = $request->input("app_type", "a");
        $result = MobileHelper::insertToken($token, $os_type, $device_id);

        $success = true;
        if ($result->code == 200) {
            $message = "insertToken success";
        } else {
            $success = false;
            $message = "insertToken fail";
        }
        $data = ["success" => $success, "data" => ["sequence" => $result->message], "message" => $message];
        $res = array_merge(get_object_vars($result), $data);
        return response()->json($res, $result->code);
    }

    public function updateTokenWithUserCode(Request $request)
    {
        $sequence = $request->input("sequence");
        $user_code = $request->input("user_code", "");
        $result = MobileHelper::updateTokenWithUserCode($sequence, $user_code);

        $success = true;
        if ($result->code == 200) {
            $message = "updateTokenWithUserCode success";
        } else {
            $success = false;
            $message = "updateTokenWithUserCode fail";
        }
        $data = ["success" => $success, "data" => ["user_code" => $result->message], "message" => $message];
        $res = array_merge(get_object_vars($result), $data);
        return response()->json($res, $result->code);
    }

    public function updateTokenWithUser(Request $request)
    {
        $sequence = $request->input("sequence");
        $user = Auth::user();
        if (isset($user)) {
            $user_code = $user->user_code;
            $result = MobileHelper::updateTokenWithUserCode($sequence, $user_code);
            $data = ["success" => true, "data" => ["user_code" => $user_code, "sequence" => $sequence], "message" => "updateTokenWithUser success"];
            $res = array_merge(get_object_vars($result), $data);
            return response()->json($res, $result->code);
        } else {
            return response()->json(["success" => false, "message" => "user not login"], 500);
        }
    }

    public function updatePushWithAgreement(Request $request)
    {
        $token = $request->input("token", -1);
        if ($token != -1) {
            $osType = $request->input("os_type", "a");
            $deviceId = $request->input("device_id", "");
            $model = MobilePushTokenModel::where("token", "=", $token)->first();
            if ($model == null) {
                $model = new MobilePushTokenModel();
                $model->token = $token;
                $model->os_type = $osType;
                $model->device_id = $deviceId;
                $model->save();
            }
            $sequence = $model->sequence;
        } else {
            $sequence = $request->input("sequence");
        }
        $agreement = $request->input("agreement", "1");
        $agreement = $agreement == "true" || $agreement == "1" ? 1 : 0;
        $result = MobileHelper::updatePushWithAgreement($sequence, $agreement);

        $success = true;
        if ($result->code == 200) {
            $message = "updatePushWithAgreement success";
        } else {
            $success = false;
            $message = "updatePushWithAgreement fail";
        }
        $data = ["success" => $success, "data" => ["agreement" => $agreement == 1 ? true : false], "message" => $message];
        $res = array_merge(get_object_vars($result), $data);
        return response()->json($res, $result->code);
    }
}


