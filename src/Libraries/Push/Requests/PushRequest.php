<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Validator;

class PushRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        switch ($request->method()) {
            case "GET" :
                break;
            case "POST" :
            case "PUT" :
                return [
                    "name" => "required",
                    "departments" => "required",
                    "ref_management_permission_level" => "required|numeric",
                    "id" => "required|min:4|max:16|not_regex:/0-9a-zA-Z_/i",
                    "password" => "required",
                    "passwordCheck" => "required|same:password"
                ];
                break;
            case "DELETE" :
                break;
            default :
                break;
        }

        $validatorDatas = [
            'send_time_type' => 'required',
            'title' => 'required',
            'push_title' => 'required',
            'link' => 'required',
        ];

        $send_time_type = $request->input("send_time_type");    // 0 | 1
        // 예약 발송
        $date = "";
        if ($send_time_type == 1) {
            $validatorDatas['send_time_sms'] = 'required';
            $validatorDatas['send_time_hour'] = 'required|numeric|max:23';
            $validatorDatas['send_time_minute'] = 'required|numeric|max:59';

            $send_time_sns = str_replace("-", "", $request->input('send_time_sms'));
            $send_time_hour = $request->input('send_time_hour');
            $send_time_minute = $request->input('send_time_minute');
            $date = $send_time_sns . sprintf("%02d%02d", $send_time_hour, $send_time_minute);
            if (!$this->isDateAfter($date)) {
                return back()->with("message", "예약 시간이 올바르지 않습니다");
            }
        }

        $contents_type = $request->input("contents_type");      // txt | img | noti_img
        switch ($contents_type) {
            case 'txt' :
                $validatorDatas['contents'] = 'required';
                break;
            case 'img' :
                $validatorDatas['push_img'] = 'required';
                break;
            case 'noti_img' :
                $validatorDatas['noti_contents'] = 'required';
                $validatorDatas['noti_img'] = 'required|mimes:jpeg,jpg,bmp,gif,png';
                break;
        }

        $validator = Validator::make(
            $request->all(),
            $validatorDatas,
            $messages
        );

        return [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'send_time_type.required' => '발송구분은 필수선택사항입니다',
            'title.required' => '관리제목은 필수입력사항입니다',
            'push_title.required' => '푸시 제목은 필수입력사항입니다',
            'link.required' => '푸시 링크는 필수입력사항입니다',
            'send_time_sms.required' => '발송시간은 필수입력사항입니다',
            'send_time_hour.required' => '발송시간은 필수입력사항입니다',
            'send_time_hour.numeric' => '발송시간(시)은 숫자만 입력가능합니다',
            'send_time_hour.max' => '발송시간(시)은 0~23까지 입력 가능합니다',
            'send_time_minute.required' => '발송시간은 필수입력사항입니다',
            'send_time_minute.numeric' => '발송시간(분)은 숫자만 입력가능합니다',
            'send_time_minute.max' => '발송시간(분)은 0~59까지 입력 가능합니다',
            'noti_img.required' => '푸시 이미지는 필수입력사항입니다',
            'noti_img.mimes' => '푸시 이미지에는 이미지 파일만 등록가능합니다(jpeg,jpg,bmp,gif,png)',
            'push_img.required' => '푸시 이미지는 필수입력사항입니다',
            'push_img.mimes' => '푸시 이미지에는 이미지 파일만 등록가능합니다(jpeg,jpg,bmp,gif,png)',
            'contents.required' => '푸시 내용은 필수입력사항입니다',
            'noti_contents.required' => '푸시 내용은 필수입력사항입니다',
        ];
    }

}
