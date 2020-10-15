<?php


namespace LaravelSupports\Libraries\SMS\Helpers;


use App\Models\Members\MemberModel;
use App\Models\Members\PlusMemberModel;
use Exception;
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
    const TEMPLATE_PLUS_MEMBER = "plus_member";
    const TEMPLATE_PLUS_SEND_MEMBER = "plus_send_member";
    const TEMPLATE_RECOMMEND_MEMBER = "recom_member";
    const TEMPLATE_LAST_MEMBER = "last_member";
    // 마지막 회원 재전송
    const TEMPLATE_LAST_MEMBER_RE_SEND = "re_send_last_member";
    const TEMPLATE_SEND_PLUS = "send_plus";
    const TEMPLATE_SEND_SECRET_BOOK = "send_secret_book";
    // 플러스 단체 회원 쿠폰 발송 template code
    const TEMPLATE_PLUS_COUPON = "plus_coupon";
    // 엑셀 파일의 이름, 연락처로 발송
    const TEMPLATE_SEND_EXCEL = "send_excel";
    // 구독 결제 결과 template code
    const TEMPLATE_SUBSCRIBE_PAYMENT_SUCCESS = "subscribe_payment_success";
    const TEMPLATE_SUBSCRIBE_PAYMENT_FAIL = "subscribe_payment_fail";
    const TEMPLATE_SUBSCRIBE_PAYMENT_FAIL_REASON = "subscribe_payment_fail_reason";
    // 결제 실패 template code
    const TEMPLATE_PAYMENT_FAIL = "payment_fail";
    // KakaoPay 결과 template code
    const TEMPLATE_KAKAO_PAY_SUCCESS = "kakaopay_success";
    const TEMPLATE_KAKAO_PAY_FAIL = "kakaopay_fail";

    private $key = "NDU2Mi0xNDY0NjYzNjE3OTI1LWJlZjkxNjUyLTMwNzctNDNjZC1iOTE2LTUyMzA3N2YzY2Q3MQ==";
    private $callback = "07050291422";
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
     * @author  dew9163
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
                case self::TEMPLATE_SEND_PLUS :
                    break;
                case self::TEMPLATE_RECOMMEND_MEMBER :
                    break;
                case self::TEMPLATE_LAST_MEMBER :
                    $plusMemberModel = new PlusMemberModel();
                    $plusExpireMembers = $plusMemberModel->plusExpireInAMonth();
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
                case self::TEMPLATE_SEND_SECRET_BOOK :
                    break;
                case self::TEMPLATE_PLUS_COUPON :
                    /**
                     * @author  dew9163
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
                case self::TEMPLATE_SUBSCRIBE_PAYMENT_FAIL_REASON:
                    collect($data)->each(function ($item) use ($template) {
                        $message = str_replace("#{회원이름}", $item[self::KEY_NAME], $template[self::KEY_MESSAGE]);
                        $message = str_replace("#{내용}", $item[self::KEY_CONTENT], $message);
                        $this->send($template[self::KEY_TEMPLATE_CODE], $item['phone'], $message);
                    });
                    break;
                case self::TEMPLATE_SUBSCRIBE_PAYMENT_SUCCESS:
                    $header = "
[플라이북] #{결제일} 멤버십 자동 결제
자동 결제 완료 회원 정보 목록 입니다
            ";
                    $messageTemplate = "
결제 모듈 : #{결제모듈}
회원 번호 : #{회원번호}
회원 이름 : #{회원이름}
연락처 : #{회원연락처}
자동결제일 : #{회원결제일}
상품명 : #{상품명}
메시지 : #{메시지}

            ";
                    $this->sendPaymentMessage($template, $header, $messageTemplate, $data);
                    break;
                case self::TEMPLATE_SUBSCRIBE_PAYMENT_FAIL:
                    $header = "
[플라이북] #{결제일} 멤버십 자동 결제
자동 결제 실패 회원 정보 목록 입니다
            ";
                    $messageTemplate = "
결제 모듈 : #{결제모듈}
회원 번호 : #{회원번호}
회원 이름 : #{회원이름}
연락처 : #{회원연락처}
자동결제일 : #{회원결제일}
상품명 : #{상품명}
메시지 : #{메시지}

            ";
                    $this->sendPaymentMessage($template, $header, $messageTemplate, $data);
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
                    $this->send($template[self::KEY_TEMPLATE_CODE], "01066193581", $message);
                    break;
            }
        } catch (Exception $e) {
            dd($e);
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
     *
     * @param $template_code
     * @param $phone
     * @param $message
     * @return SMS
     * @author  dew9163
     * @added   2020/04/16
     * @updated 2020/04/16
     */
    public function send($template_code, $phone, $message)
    {
        try {
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

            $this->successCount++;
            array_push($this->success, $phone);
        } catch (Exception $e) {
            $this->failCount++;
            array_push($this->fail, [
                'phone' => $phone,
                'message' => $e->getMessage(),
            ]);
        }

        return $smsObj;
    }

    private function initializeResult()
    {
        $this->successCount = 0;
        $this->success = [];
        $this->failCount = 0;
        $this->fail = [];
    }

    private function sendPaymentMessage($template, $header, $messageTemplate, $data)
    {
        $dateHelper = new DateHelper();
        $message = str_replace("#{결제일}", $dateHelper->getNowMonthAndDayOfMonth(), $header);

        foreach ($data as $item) {
            $instanceTemplate = str_replace("#{결제모듈}", $item[self::KEY_PAYMENT_MODULE], $messageTemplate);
            $instanceTemplate = str_replace("#{회원번호}", $item[self::KEY_MEMBER_ID], $instanceTemplate);
            $instanceTemplate = str_replace("#{회원이름}", $item[self::KEY_NAME], $instanceTemplate);
            $instanceTemplate = str_replace("#{회원연락처}", $item[self::KEY_PHONE], $instanceTemplate);
            $instanceTemplate = str_replace("#{회원결제일}", $item[self::KEY_MEMBER_PAYMENT_DATE], $instanceTemplate);
            $instanceTemplate = str_replace("#{상품명}", $item[self::KEY_PAYMENT_NAME], $instanceTemplate);
            $instanceTemplate = str_replace("#{메시지}", $item[self::KEY_MESSAGE], $instanceTemplate);
            $message .= $instanceTemplate;
        }
        $this->send($template[self::KEY_TEMPLATE_CODE], "01051318537", $message);
        $this->send($template[self::KEY_TEMPLATE_CODE], "01066193581", $message);
    }
}
