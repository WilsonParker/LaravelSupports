<?php


namespace LaravelSupports\Libraries\SMS\Helpers;


use LaravelSupports\Libraries\SMS\Models\SMSModel;
use LaravelSupports\Libraries\Supports\Date\DateHelper;
use LaravelSupports\Models\Members\PlusMemberModel;
use GuzzleHttp\Client;
use SoapBox\Formatter\Formatter;

class SMSHelper
{
    private $key = "NDU2Mi0xNDY0NjYzNjE3OTI1LWJlZjkxNjUyLTMwNzctNDNjZC1iOTE2LTUyMzA3N2YzY2Q3MQ==";
    private $callback = "07050291422";

    public const KEY_TEMPLATE = "template";
    public const KEY_MESSAGE = "message";
    public const KEY_TEMPLATE_CODE = "template_code";
    public const KEY_PHONE = "phone";
    public const KEY_NAME = "name";
    public const KEY_CONTACT = "contact";
    public const KEY_COUPON = "coupon";
    public const KEY_PAYMENT_DATE = "payment_date";
    public const KEY_MEMBER_ID = "member_id";
    public const KEY_MEMBER_PAYMENT_DATE = "member_payment_date";

    public const TEMPLATE_BASIC = "basic";
    public const TEMPLATE_PLUS_MEMBER = "plus_member";
    public const TEMPLATE_SEND_MEMBER = "send_member";
    public const TEMPLATE_RECOMMEND_MEMBER = "recom_member";
    public const TEMPLATE_LAST_MEMBER = "last_member";
    public const TEMPLATE_SEND_PLUS = "send_plus";
    public const TEMPLATE_SEND_SECRET_BOOK = "send_secret_book";
    // 플러스 단체 회원 쿠폰 발송 template code
    public const TEMPLATE_PLUS_COUPON = "plus_coupon";
    // KakaoPay 결과 template code
    public const TEMPLATE_KAKAO_PAY_SUCCESS = "kakaopay_success";
    public const TEMPLATE_KAKAO_PAY_FAIL = "kakaopay_fail";

    /**
     * template 에 따라 sms 메시지를 발송합니다
     *
     * @param array $template
     * @param null $data
     * @return bool
     * @author  dew9163
     * @added   2020/03/16
     *
     * 카카오페이 결제 결과에 따른 SMS 발송 기능 추가
     * @updated 2020/04/23
     */
    public function templateSend(array $template, $data = null)
    {
        try {

            switch ($template[self::KEY_TEMPLATE]) {
                case self::TEMPLATE_BASIC :
                    break;
                case self::TEMPLATE_PLUS_MEMBER :
                    break;
                case self::TEMPLATE_SEND_MEMBER :
                    break;
                case self::TEMPLATE_SEND_PLUS :
                    break;
                case self::TEMPLATE_RECOMMEND_MEMBER :
                    break;
                case self::TEMPLATE_LAST_MEMBER :
                    $plusMemberModel = new PlusMemberModel();
                    $plusExpireMembers = $plusMemberModel->plusExpireInAMonth();

                    collect($plusExpireMembers)->each(function ($plusMember) use ($template) {
                        $member = Member::
                        select("phone", "realname")
                            ->where('id', $plusMember->member_id)
                            ->first();

                        $phone = $member->phone;
                        if (isset($phone)) {
                            $phone = str_replace("-", "", $phone);
                            if (!TelNumberHelper::isPhoneNumber($phone)) {
                                return;
                            }
                        }

                        $dateHelper = new DateHelper();
                        $message = str_replace("#{회원이름}", $member->realname, $template[self::KEY_MESSAGE]);
                        $message = str_replace("#{이용기간}", $plusMember->sendnum, $message);
                        // $message = str_replace("#{해당월쿠폰코드}", $coupon, $message);
                        $message = str_replace("#{유효기간}", $dateHelper->getCurrentMonth(), $message);
                        $message = str_replace("#{유효기간요일}", $dateHelper->getCurrentMonth(), $message);

                        $this->send($template[self::KEY_TEMPLATE_CODE], $phone, $message);
                    });
                    break;
                case self::TEMPLATE_SEND_SECRET_BOOK :
                    break;
                case self::TEMPLATE_PLUS_COUPON :
                    /**
                     * @example
                     * [
                     *  "name"=>"김준수",
                     *  "contact"=>"010-1234-5678",
                     *  "coupon"=>"AAA123"
                     * ]
                     * @author  dew9163
                     * @added   2020/04/16
                     * @updated 2020/04/16
                     */
                    collect($data)->each(function ($item) use ($template) {
                        $phone = $item[self::KEY_CONTACT];
                        if (isset($phone)) {
                            $phone = str_replace("-", "", $phone);
                            if (!TelNumberHelper::isPhoneNumber($phone)) {
                                return;
                            }
                        }

                        $message = str_replace("#{회원이름}", $item[self::KEY_NAME], $template[self::KEY_MESSAGE]);
                        $message = str_replace("#{쿠폰코드}", $item[self::KEY_COUPON], $message);
                        $this->send($template[self::KEY_TEMPLATE_CODE], $phone, $message);
                    });
                    break;
                case self::TEMPLATE_KAKAO_PAY_SUCCESS:
                    $header = "
[플라이북] #{결제일} 플러스 자동 결제
카카오 페이 결제 완료 회원 정보 목록 입니다
            ";
                    $messageTemplate = "
회원 번호 : #{회원번호}
회원 이름 : #{회원이름}
연락처 : #{회원연락처}
자동결제일 : #{회원결제일}
메시지 : #{메시지}

            ";
                    $dateHelper = new DateHelper();
                    $message = str_replace("#{결제일}", $dateHelper->getNowMonthAndDayOfMonth(), $header);

                    foreach ($data as $item) {
                        $instanceTemplate = str_replace("#{회원번호}", $item[self::KEY_MEMBER_ID], $messageTemplate);
                        $instanceTemplate = str_replace("#{회원이름}", $item[self::KEY_NAME], $instanceTemplate);
                        $instanceTemplate = str_replace("#{회원연락처}", $item[self::KEY_PHONE], $instanceTemplate);
                        $instanceTemplate = str_replace("#{회원결제일}", $item[self::KEY_MEMBER_PAYMENT_DATE], $instanceTemplate);
                        $instanceTemplate = str_replace("#{메시지}", $item[self::KEY_MESSAGE], $instanceTemplate);
                        $message .= $instanceTemplate;
                    }
                    $this->send($template[self::KEY_TEMPLATE_CODE], "01051318537", $message);
                    break;
                case self::TEMPLATE_KAKAO_PAY_FAIL:
                    $header = "
[플라이북] #{결제일} 플러스 자동 결제
카카오 페이 결제 실패 회원 정보 목록 입니다
            ";
                    $messageTemplate = "
회원 번호 : #{회원번호}
회원 이름 : #{회원이름}
연락처 : #{회원연락처}
자동결제일 : #{회원결제일}
메시지 : #{메시지}

            ";
                    $dateHelper = new DateHelper();
                    $message = str_replace("#{결제일}", $dateHelper->getNowMonthAndDayOfMonth(), $header);

                    foreach ($data as $item) {
                        $instanceTemplate = str_replace("#{회원번호}", $item[self::KEY_MEMBER_ID], $messageTemplate);
                        $instanceTemplate = str_replace("#{회원이름}", $item[self::KEY_NAME], $instanceTemplate);
                        $instanceTemplate = str_replace("#{회원연락처}", $item[self::KEY_PHONE], $instanceTemplate);
                        $instanceTemplate = str_replace("#{회원결제일}", $item[self::KEY_MEMBER_PAYMENT_DATE], $instanceTemplate);
                        $instanceTemplate = str_replace("#{메시지}", $item[self::KEY_MESSAGE], $instanceTemplate);
                        $message .= $instanceTemplate;
                    }
                    $this->send($template[self::KEY_TEMPLATE_CODE], "01051318537", $message);
                    break;
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * SMS 를 발송합니다
     *
     * @param $template_code
     * @param $phone
     * @param $message
     * @return SMSModel
     * @author  dew9163
     * @added   2020/04/16
     * @updated 2020/04/16
     */
    public function send($template_code, $phone, $message)
    {
        $client = new Client();
        $res = $client->request('POST', 'http://api.apistore.co.kr/kko/1/msg/flybook', [
            'headers' => [
                'x-waple-authorization' => $this->key
            ],
            'form_params' => [
                self::KEY_PHONE => $phone,
                self::KEY_TEMPLATE_CODE => $template_code,
                'callback' => $this->callback,
                'msg' => $message,
                'failed_type' => 'LMS',
                'failed_subject' => '플라이북 알림',
                'failed_msg' => $message,
                'apiVersion' => '1',
                'client_id' => 'flybook'
            ]
        ]);

        $formatter = Formatter::make($res->getBody(), Formatter::JSON);
        $return_data = $formatter->toArray();
        $return_data[self::KEY_MESSAGE] = $message;
        $return_data[self::KEY_PHONE] = $phone;
        $return_data[self::KEY_TEMPLATE_CODE] = $template_code;

        $smsObj = new SMSModel();
        $smsObj->bindData($return_data);
        $smsObj->save();

        return $smsObj;
    }
}
