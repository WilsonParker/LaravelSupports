<?php


namespace LaravelSupports\Libraries\Supports\Auth;


use FlyBookModels\Members\MemberModel;
use Illuminate\Support\Facades\Auth;

class AuthHelper
{

    /**
     * 로그인 된 AdminModel 을 return 합니다
     *
     * @return
     * @author  WilsonParker
     * @added   2019-08-23
     * @updated 2019-08-23
     * @updated 2020-06-11
     */
    public static function getAuthUser()
    {
        $member = MemberModel::find(151400);
        return $member;

        //        $member = Auth::user();
        $token = DB::table('oauth_access_tokens')->where('id', request()->token)->first();
        if (!$token) {
            abort(401);
        }

        $member = MemberModel::getModel($token->user_id);
        $member->tokenID = $token->id;
        return $member;

        /*if (self::isLogin()) {
            return Auth::guard("admin")->user();
        } else {
            return null;
        }
        $model = AdminModel::get()[0];
        return $model;*/
    }

    /**
     * 로그인 여부를 return 합니다
     * 로그인이 안된 상태에서 $needLogOut 을 true 또는 보내지 않으면 로그아웃 합니다
     *
     * @return  bool
     * @author  WilsonParker
     * @added   2019-09-05
     * @updated 2019-09-05
     */
    public static function isLogin()
    {
        $login = Auth::guard("admin")->check();
        /*if (!$login && $needLogOut) {
            self::logOut();
        }*/
        return $login;
    }

    /**
     * 로그아웃 합니다
     *
     * @author  WilsonParker
     * @added   2019-09-05
     * @updated 2019-09-05
     */
    public static function logOut()
    {
        Auth::guard("admin")->logout();
    }

}
