<?php


namespace LaravelSupports\Supports\Data;


use Illuminate\Support\Facades\Session;

class SessionHelper
{
    const KEY_SIDEBAR = "sideBar";
    const KEY_MENUS = "menus";
    const KEY_THREE_DEPTH_MENUS = "threeDepthMenu";
    const KEY_FAVORITES = "favorites";


    public static function get($key, $default = null)
    {
        $data = Session::get($key);
        // 세션에 데이터가 없을 경우 $default 적용
        if (is_null($data)) {
            $data = $default;
        }
        return $data;
    }

    public static function put($key, $value)
    {
        Session::put($key, $value);
    }

    public static function forget($key)
    {
        Session::forget($key);
    }
}
