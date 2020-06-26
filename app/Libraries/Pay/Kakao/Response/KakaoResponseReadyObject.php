<?php


namespace LaravelSupports\Libraries\Pay\Kakao\Response;


use LaravelSupports\Libraries\Pay\Common\Abstracts\AbstractResponseReadyObject;

class KakaoResponseReadyObject extends AbstractResponseReadyObject
{

    /**
     * 결제 고유 번호, 20자
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/16
     * @updated 2020/06/16
     */
    public $tid;

    /**
     * 요청한 클라이언트(Client)가 모바일 앱일 경우
     * 카카오톡 결제 페이지 Redirect URL
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $next_redirect_app_url;

    /**
     * 요청한 클라이언트가 모바일 웹일 경우
     * 카카오톡 결제 페이지 Redirect URL
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $next_redirect_mobile_url;

    /**
     * 요청한 클라이언트가 PC 웹일 경우
     * 카카오톡으로 결제 요청 메시지(TMS)를 보내기 위한 사용자 정보 입력 화면 Redirect URL
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $next_redirect_pc_url;

    /**
     * 카카오페이 결제 화면으로 이동하는 Android 앱 스킴(Scheme)
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $android_app_scheme;

    /**
     * 카카오페이 결제 화면으로 이동하는 iOS 앱 스킴
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $ios_app_scheme;

    /**
     * 결제 준비 요청 시간
     *
     * @type    string
     * @author  dew9163
     * @added   2020/06/10
     * @updated 2020/06/10
     */
    public $created_at;

    public function getResult()
    {
        return [
            'next_redirect_app_url' => $this->next_redirect_pc_url,
            'next_redirect_mobile_url' => $this->next_redirect_mobile_url,
            'next_redirect_pc_url' => $this->next_redirect_pc_url,
            'android_app_scheme' => $this->android_app_scheme,
            'ios_app_scheme' => $this->ios_app_scheme,
        ];
    }

}
