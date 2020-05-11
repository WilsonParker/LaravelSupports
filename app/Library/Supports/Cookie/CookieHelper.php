<?php


namespace App\Library\Supports\Cookie;


use Illuminate\Support\Facades\Cookie;

class CookieHelper
{

    const KEY_REMEMBER_ID = "remember_id";
    const KEY_REMEMBERED_ID = "remembered_id";
    const COOKIE_EXPIRED = 14 * 24 * 60;

    public static function getCookie($key)
    {
        return str_replace("\"", "", urldecode(Cookie::get($key)));
    }

    public static function setCookie($key, $value, $time = self::COOKIE_EXPIRED)
    {
        // Cookie::queue($key, urlencode(json_encode($value)), $time);
        Cookie::queue($key, urlencode(json_encode($value)), $time);
    }

    public static function setRememberId($id, $time = self::COOKIE_EXPIRED)
    {
        self::setCookie(self::KEY_REMEMBER_ID, "on", $time);
        self::setCookie(self::KEY_REMEMBERED_ID, $id, $time);
    }

    public static function getRememberedId()
    {
        return self::getCookie(self::KEY_REMEMBERED_ID);
    }

    public static function isRememberId()
    {
        return Cookie::has(self::KEY_REMEMBER_ID) && self::getCookie(self::KEY_REMEMBER_ID) == "on";
    }

    public static function removeRememberId()
    {
        self::setCookie(self::KEY_REMEMBER_ID, "off", 0);
        self::setCookie(self::KEY_REMEMBERED_ID, "", 0);
    }

    static function utf8_urldecode($str)
    {
        $str = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str));
        return html_entity_decode($str, null, 'UTF-8');;
    }
}
