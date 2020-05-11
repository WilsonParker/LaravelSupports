<?php
namespace App\Library\Push\Controllers;

class MobilePushController extends AdminController
{
    //-- 리턴 디폴트 페이지
    protected $BaseRedirectUrl = "/display/mobilepushmanage/mobilepushsendapp/";
    protected $ErrRedirectUrl = "/display/mobilepushmanage/mobilepushsendapp/";
    //-- 라우터 디폴트 페이지
    protected $BaseRouteAs = "Display.Mobilepushmanage.Mobilepushsendapp";
    protected $ErrRouteAs = "Display.Mobilepushmanage.Mobilepushsendapp";
    //-- 뷰 경로 정보
    protected $viewPath = "admin.display.mobilepushmanage.mobilepushsendapp.";

    /**
     * @brief   검색조건 처리용 변수
     * @autho   yhlim
     * @date    20181025
     * @bug
     * @todo
     * @see
     **/
    protected $conditions = array();
    //-- 입력항목 및 디폴트 값 세팅
    protected $arr_serachinput_set = [
        'is_default' => false,
        'hdn_uid' => '',
        'limit' => '1',
        'sel_memListRow' => '',
        'page' => '',
        'offset' => '',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->modelcmmcode = new cmmcode;
        $this->modelList = new mobilePushReserve;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return redirect()->action('\App\Http\Controllers\admin\Display\Mobilepushmanage\Mobilepushsendapp\MobilepushsendappController@create');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $DsItems = array();
        // 사용여부

        return view($this->viewPath . "create", ["DsItems" => $DsItems]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
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

        $validator = \Validator::make(
            $request->all(),
            $validatorDatas,
            $messages
        );

        if ($validator->passes()) {
            // 즉시 발송
            if ($send_time_type == 0) {
                $model = new MobilePushModel();
                $model->bindWithRequest($request);
                $result = PushHelper::pushAll($model);
                if ($result['code'] == 200) {
                    return redirect($this->BaseRedirectUrl . "create")->with(array("message" => "푸시 메시지 발송에 성공하였습니다"));
                } else {
                    return back()->with(array("message" => "푸시 등록에 실패하였습니다"));
                }
                // 예약 발송
            } else {
                $transactionCallback = function () use ($request, $date) {
                    $model = new mobilePushReserve();
                    $model->reserve_time = $date;
                    $model->bindWithRequest($request);
                    $result = $model->save();
                    return PushHelper::pushResultArray(200, $result);
                };
                $transactionFailure = function ($code, $message) {
                    return PushHelper::pushResultArray(500, $message);
                };
                $result = PushHelper::transactionQuery($transactionCallback, $transactionFailure);
                if ($result['code'] == 200) {
                    return redirect($this->BaseRedirectUrl . "create")->with(array("message" => "푸시 메시지 예약 발송 등록에 성공하였습니다"));
                } else {
                    return back()->with(array("message" => "푸시 메시지 예약 발송 등록에 실패하였습니다"));
                }
            }
        } else {
            return back()
                ->withInput($request->input())
                ->with("message", array_key_exists(0, $validator->errors()->all()) ? $validator->errors()->all()[0] : '푸시 등록에 실패하였습니다');
        }
    }

    /**
     * Ajax 를 이용하여 푸시 예약 건을 삭제 합니다
     *
     * @param
     * @return
     * @author  WilsonParker
     * @added   2019-08-05
     * @updated 2019-08-05
     */
    public function delete($sequence)
    {
        $model = mobilePushReserve::where("sequence", $sequence)->first();
        $result = $model->delete();
        $data = [
            "result" => $result
        ];
        return response()->json($data, $result ? 200 : 500);
    }

    /**
     * private function
     * START
     * */

    /**
     * 예약 설정한 날짜가 현재보다 후 인지 판별합니다
     *
     * @param String $date
     * @return bool
     * @author  WilsonParker
     * @added   2019-04-12
     * @updated 2019-04-12
     */
    private function isDateAfter(String $date): bool
    {
        return strtotime($date) > strtotime(date('YmdHi'));
    }

    /**
     * private function
     * END
     * */

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        //
    }

    /**
     * @brief   검색조건 처리용 함수
     * @autho   yhlim
     * @date    20181025
     * @bug
     * @todo
     * @see
     **/
    protected function setSearchForm(Request $request)
    {
        //-- 전달받은 검색 객체 정보 세팅
        if (empty($request->input("conditions")) === false) {
            $input = $request->input("conditions");
            foreach ($input as $key => $value) {
                $this->conditions[$key] = $value;
            }
        }
        //-- 사용 설정 항목에 대한 입력값 처리
        foreach ($this->arr_serachinput_set as $key => $value) {
            if (is_array($request->input($key)) || strlen($request->input($key))) {
                $this->conditions[$key] = $request->input($key);
            } elseif (isset($this->conditions[$key]) == false || (is_array($this->conditions[$key]) == false && strlen($this->conditions[$key]) === 0)) {
                $this->conditions[$key] = $value;
            }
        }

        if (count($request->all()) > 1) {
            $this->conditions['is_default'] = false;
        } else {
            $this->conditions['is_default'] = true;
        }

        //-- 후처리 필요 항목 정리
        $this->conditions['code'] = $this->conditions['hdn_uid'];
        $this->conditions['sel_memListRow'] = $this->conditions['limit'];
        $this->conditions['page'] = $this->conditions['page'] ?: 1;
        $this->conditions['offset'] = $this->conditions['offset'] ?: 0;

        if ($this->conditions['is_default'] === false) {
            if ($this->conditions['limit'] == 'ALL') {
                //-- 엑셀 최대치 강제 설정
                $this->conditions['page'] = 1;
                $this->conditions['limit'] = 'ALL';
                $this->conditions['offset'] = 0;
            } else {
                if ($this->conditions['page'] > 1) {
                    $this->conditions['offset'] = ($this->conditions['page'] - 1) * $this->conditions['limit'];
                }
            }
        }
    }
}
