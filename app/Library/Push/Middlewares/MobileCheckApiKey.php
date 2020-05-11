<?php

namespace App\Library\Push\Middlewares;

use Closure;
use Auth;
use Illuminate\Http\Request;

class MobileCheckApiKey
{
    private $api_key = "YHq4yQzIlLeeIGQ87FIfGTyVEoX6y1dT7vbyfpQWHmc2X26wODzGSLNQONDLGxKNRoeOM1A1avryZ9J069uqIu1HdWLFUz3FoH53dcvn4SRY0vZJG3zPxVugmSa9ghzD1zHdiVGX0e66SKTXgzxGh8zpvKUi6zDAIck7XjoQUGpi7nfp5sg04rOSZaMCAAE1aypgCV1waDfJpnOyaa9LG0xemLoWMgFglumDsWP4V94QMvyw4bzwVR80JdCKWFH";

    /**
     * Mobile api 를 호출하기 전에 올바른 api key 를 전달하는지 확인합니다
     *
     * @param
     * @return
     * @author  WilsonParker
     * @added   2019-06-23
     * @updated 2019-06-23
     */
    public function handle($request, Closure $next)
    {
        if($this->isNotValidApiKey($request)){
            return response()->json("is Not Valid Api key", 500);
        }
        return $next($request);
    }

    private function isNotValidApiKey(Request $request)
    {
        return $request->input("api_key", "") != $this->api_key;
    }
}
