<?php


namespace LaravelSupports\Libraries\SMS\Helpers;


use Carbon\Carbon;
use FlyBookModels\Members\MemberModel;
use FlyBookModels\Members\PlusMemberModel;
use GuzzleHttp\Client;
use LaravelSupports\Libraries\SMS\Models\SMSModel;
use LaravelSupports\Libraries\Supports\Date\DateHelper;
use SoapBox\Formatter\Formatter;

class SMSHelper
{
    const KEY_TEMPLATE = "template";
    const KEY_MESSAGE = "message";
    const KEY_TEMPLATE_CODE = "template_code";
    const KEY_PHONE = "phone";
    const KEY_NAME = "name";
    const KEY_CONTACT = "contact";
    const KEY_COUPON = "coupon";
    const KEY_PAYMENT_MODULE = "payment_module";
    const KEY_PAYMENT_NAME = "payment_name";
    const KEY_PAYMENT_DATE = "payment_date";
    const KEY_MEMBER_ID = "member_id";
    const KEY_MEMBER_PAYMENT_DATE = "member_payment_date";
    const KEY_CONTENT = "content";

    const TEMPLATE_BASIC = "basic";
    const TEMPLATE_REPLACE = "replace";
    // 엑셀 파일의 이름, 연락처로 발송
    const TEMPLATE_SEND_EXCEL = "send_excel";

    /*
     * Plus
     * */
    // 마지막 회원 재전송
    const TEMPLATE_LAST_MEMBER_RE_SEND = "re_send_last_member";
    const TEMPLATE_PLUS_MEMBER = "plus_member";
    const TEMPLATE_PLUS_SEND_MEMBER = "plus_send_member";
    const TEMPLATE_RECOMMEND_MEMBER = "recom_member";
    const TEMPLATE_LAST_MEMBER = "last_member";
    // 플러스 피드백
    const TEMPLATE_PLUS_FEEDBACK = "plus_feedback";
    const TEMPLATE_PLUS_FEEDBACK2 = "plus_feedback2";

    const TEMPLATE_SEND_PLUS = "send_plus";
    const TEMPLATE_SEND_SECRET_BOOK = "send_secret_book";

    /*
     * Coupon
     * */
    // 플러스 단체 회원 쿠폰 발송 template code
    const TEMPLATE_PLUS_COUPON = "plus_coupon";

    /*
     * Payment
     * */
    // 결제 실패 template code
    const TEMPLATE_PAYMENT_FAIL = "payment_fail";
    const TEMPLATE_SUBSCRIBE_PAYMENT_FAIL_REASON = "subscribe_payment_fail_reason";

    private string $key = "NDU2Mi0xNDY0NjYzNjE3OTI1LWJlZjkxNjUyLTMwNzctNDNjZC1iOTE2LTUyMzA3N2YzY2Q3MQ==";
    private string $callback = "07050291422";

    private int $successCount = 0;
    private array $success = [];
    private int $failCount = 0;
    private array $fail = [];

    /**
     * template 에 따라 sms 메시지를 발송합니다
     *
     * @param array $template
     * @param null $data
     * @return int[]
     * @author  WilsonParker
     * @added   2020/03/16
     *
     * 카카오페이 결제 결과에 따른 SMS 발송 기능 추가
     * @updated 2020/04/23
     */
    public function templateSend(array $template, $data = null)
    {
        $this->initializeResult();
        try {
            switch ($template[self::KEY_TEMPLATE]) {
                case self::TEMPLATE_BASIC :
                    $this->send($template[self::KEY_TEMPLATE_CODE], $data['contact'], $template[self::KEY_MESSAGE]);
                    break;
                case self::TEMPLATE_REPLACE :
                    collect($data)->each(function ($item) use ($template) {
                        $message = str_replace("#{회원이름}", $item[self::KEY_NAME], $template[self::KEY_MESSAGE]);
                        $message = str_replace("#{내용}", $item[self::KEY_CONTENT], $message);
                        $this->send($template[self::KEY_TEMPLATE_CODE], $item['phone'], $message);
                    });
                    break;
                case self::TEMPLATE_PLUS_MEMBER :
                    break;
                case self::TEMPLATE_PLUS_SEND_MEMBER :
                    $plusMemberModels = PlusMemberModel::getPlusSentMembersQuery()->get();
//                    $plusMemberModels = PlusMemberModel::where('member_id', 146973)->get();
                    collect($plusMemberModels)->each(function ($item) use ($template) {
                        $message = str_replace("#{회원이름}", $item->member->realname, $template[self::KEY_MESSAGE]);

                        $phone = $item->member->phone;
                        if (isset($phone)) {
                            $phone = str_replace("-", "", $phone);
                            if (!TelNumberHelper::isPhoneNumber($phone)) {
                                return;
                            }
                        }
                        $this->send($template[self::KEY_TEMPLATE_CODE], $phone, $message);
                    });
                    break;
                case self::TEMPLATE_LAST_MEMBER :
                    $plusExpireMembers = PlusMemberModel::getPlusExpireInAMonthQuery()->whereDoesntHave('dusanCouponUsedMember')->count();
//                    $plusExpireMembers = PlusMemberModel::where('member_id', 146973)->get();
//                     dd($plusExpireMembers);

                    collect($plusExpireMembers)->each(function ($plusMember) use ($template) {
                        $member = MemberModel::select("id", "phone", "realname")
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
                case self::TEMPLATE_LAST_MEMBER_RE_SEND :
                    collect($data)->each(function ($plusMember) use ($template) {
                        $member = MemberModel::select("id", "phone", "realname")
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
                case self::TEMPLATE_PLUS_FEEDBACK :
                    collect($data)->each(function ($send) use ($template) {
                        $member = $send->member;
                        $phone = $this->convertContact($member->phone);

                        if (!isset($phone)) {
                            return;
                        }

                        $config = config('sms.plus.send.feedback');

                        $message = $config['message'];
                        $dateHelper = new DateHelper();
                        $message = str_replace("#{이름}", $member->realname, $message);
                        $message = str_replace("#{일}", 13, $message);
                        $message = str_replace("#{prevMonth}", $dateHelper->getPrevMonth() . '월', $message);
                        $message = str_replace("#{currentMonth}", $dateHelper->getCurrentMonth() . '월', $message);
                        $url = $config['urls'] . $send->id;
                        $this->send($config['code'], $phone, $message, $config['button_types'], $config['buttons'], $url);
                    });
                    break;
                case self::TEMPLATE_PLUS_COUPON :
                    /**
                     * @author  WilsonParker
                     * @added   2020/04/16
                     * @updated 2020/04/16
                     * @example
                     * [
                     *  "name"=>"김준수",
                     *  "contact"=>"010-1234-5678",
                     *  "coupon"=>"AAA123"
                     * ]
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
                case self::TEMPLATE_SEND_EXCEL :
                    collect($data)->each(function ($item) use ($template) {
                        $phone = $item[self::KEY_CONTACT];
                        if (isset($phone)) {
                            $phone = str_replace("-", "", $phone);
                            if (!TelNumberHelper::isPhoneNumber($phone)) {
                                return;
                            }
                        }

                        $message = str_replace("#{회원이름}", $item[self::KEY_NAME], $template[self::KEY_MESSAGE]);
                        $this->send($template[self::KEY_TEMPLATE_CODE], $phone, $message);
                    });
                    break;
                case self::TEMPLATE_PAYMENT_FAIL:
                    $message = "
[플라이북] 멤버십 미결제 안내
안녕하세요. 책과 더 가까워지는 곳 플라이북입니다.
회원님이 이용중인 멤버십이 카드 오류로 결제가 완료되지 않았습니다.
플라이북 앱에서 재결제하시면 정상적으로 멤버십을 이용할 수 있습니다.
궁금하신 점 있으시면 카카오톡(ID:플라이북)으로 연락주세요!
감사합니다.
            ";
                    $members = MemberModel::whereIn('id', $data)->get();
                    collect($members)->each(function ($item) use ($template, $message) {
                        $phone = $item->phone;
                        if (isset($phone)) {
                            $phone = str_replace("-", "", $phone);
                            if (!TelNumberHelper::isPhoneNumber($phone)) {
                                return;
                            }
                        }

                        $this->send($template[self::KEY_TEMPLATE_CODE], $phone, $message);
                    });
                    break;
                case self::TEMPLATE_SUBSCRIBE_PAYMENT_FAIL_REASON:
                    collect($data)->each(function ($item) use ($template) {
                        $message = str_replace("#{회원이름}", $item[self::KEY_NAME], $template[self::KEY_MESSAGE]);
                        $message = str_replace("#{내용}", $item[self::KEY_CONTENT], $message);
                        $this->send($template[self::KEY_TEMPLATE_CODE], $item['phone'], $message);
                    });
                    break;
            }
        } catch (\Throwable $throwable) {
            dd($throwable);
        }
        return [
            'successCount' => $this->successCount,
            'success' => $this->success,
            'failCount' => $this->failCount,
            'fail' => $this->fail,
        ];
    }

    /**
     * SMS 를 발송합니다
     * Kakao 알림톡을 우선하여 보내며 그렇지 않을 경우
     * SMS 로 발송합니다
     *
     * @param string $templateCode
     * @param string $phone
     * @param string $message
     * @param string $btnTypes
     * @param string $btnText
     * @param string $btnUrls
     * @return SMSModel
     * @author  WilsonParker
     * @added   2020/04/16
     * @updated 2020/04/16
     * @updated 2020/11/25
     */
    public function send(string $templateCode, string $phone, string $message, string $btnTypes = '', string $btnText = '', string $btnUrls = '')
    {
        try {
            $client = new Client();
            $res = $client->request('POST', 'http://api.apistore.co.kr/kko/1/msg/flybook', [
                'headers' => [
                    'x-waple-authorization' => $this->key
                ],
                'form_params' => [
                    self::KEY_PHONE => $phone,
                    self::KEY_TEMPLATE_CODE => $templateCode,
                    'callback' => $this->callback,
                    'msg' => $message,
                    'failed_type' => 'LMS',
                    'failed_subject' => '플라이북 알림',
                    'failed_msg' => $message,
                    'apiVersion' => '1',
                    'client_id' => 'flybook',
                    'btn_types' => $btnTypes,
                    'url_button_txt' => $btnText,
                    'url' => $btnUrls,
                ]
            ]);

            $formatter = Formatter::make($res->getBody(), Formatter::JSON);
            $return_data = $formatter->toArray();
            $return_data[self::KEY_MESSAGE] = $message;
            $return_data[self::KEY_PHONE] = $phone;
            $return_data[self::KEY_TEMPLATE_CODE] = $templateCode;

            $smsObj = new SMSModel();
            $smsObj->bindData($return_data);
            $smsObj->save();

            $this->successCount++;
            array_push($this->success, $phone);
        } catch (\Throwable $throwable) {
            $this->failCount++;
            array_push($this->fail, [
                'phone' => $phone,
                'message' => $throwable->getMessage(),
            ]);
        }

        return $smsObj;
    }

    /**
     * templateSend 에 사용될 $template 정보를 생성 합니다
     *
     * @param string $template
     * @param string $code
     * @param string $message
     * @return array
     * @author  WilsonParker
     * @added   2020/11/25
     * @updated 2020/11/25
     */
    public function buildTemplate(string $template, string $code = '', string $message = ''): array
    {
        return [
            SMSHelper::KEY_TEMPLATE => $template,
            SMSHelper::KEY_TEMPLATE_CODE => $code,
            SMSHelper::KEY_MESSAGE => $message
        ];
    }

    /**
     * 연락처를 발송가능한 번호 인지 확인 하고 return 합니다
     *
     * @param string $contacts
     * @return string|string[]|null
     * @author  WilsonParker
     * @added   2020/11/25
     * @updated 2020/11/25
     */
    private function convertContact(string $contacts): ?string
    {
        if (isset($contacts)) {
            $contacts = str_replace("-", "", $contacts);
            if (!TelNumberHelper::isPhoneNumber($contacts)) {
                return null;
            } else {
                return $contacts;
            }
        } else {
            return null;
        }
    }

    private function initializeResult()
    {
        $this->successCount = 0;
        $this->success = [];
        $this->failCount = 0;
        $this->fail = [];
    }

}
