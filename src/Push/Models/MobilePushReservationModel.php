<?php

namespace LaravelSupports\Push\Models;

use DateTime;
use FlyBookModels\ImagesModel;
use Illuminate\Http\Request;
use LaravelSupports\Data\Traits\FileSaveTrait;

class MobilePushReservationModel extends AbstractMobilePushModel
{
    use FileSaveTrait;


    protected $table = 'mobile_push_reservation';

    public function image()
    {
        return $this->hasOne(ImagesModel::class, "ref_ix", "ix")->where("table_type", $this->tableType);
    }

    public function bindData(array $data)
    {
        parent::bindData($data);
        $this->push_type = "txt";
    }

    public function bindWithRequest(Request $request)
    {
        $this->reserve_type = 'P';
        $this->reserve_push_os = 'a';

        $date = DateTime::createFromFormat("YmdHi", $this->reserve_time);
        $this->reserve_time = $date->format('Y-m-d H:i:s');
        $this->bindPushData($request);
    }

    // @Override

    /**
     * MobilePushModel 에 값을 설정하고 저장하며
     * 푸시 예약 완료 설정을 합니다
     *
     * @return bool
     * @author  WilsonParker
     * @added   2019-04-11
     * @updated 2019-04-11
     */
    function execute()
    {
        $model = new MobilePushResultLogModel();
        $model->push_title = $this->push_title;
        $model->push_contents = $this->push_contents;
        $model->push_type = $this->push_type;
        $model->push_link = $this->push_link;
        $model->push_replace_text = $this->push_replace_text;
        $model->imageUrl = $this->imageurl;
        $result = $model->execute();
        if ($result) {
            $this->push_send = 1;
            return $this->update();
        }
        return false;
    }

    /**
     * String 타입의 json 값을 이용해서 serialize 에 관련된 database column 에 대입 합니다
     *
     * @param String $json
     * @return  void
     * @author  WilsonParker
     * @added   2019-04-11
     * @updated 2019-04-11
     */
    function setSerializeData(string $json)
    {
        // TODO: Implement setSerializeData() method.
    }

    /**
     * 푸시 예약 목록을 return 해줍니다
     * $condition 을 파라미터로 넘길 경우 해당 조건의 배너 목록을 제공해줍니다
     *
     * @param array $condition
     * @return  collector
     * @author  WilsonParker
     * @added   2019-09-05
     * @updated 2019-09-05
     */
    public function getList($condition = [])
    {
        // 예약 목록
        $query = $this->select()->with(["image"]);
        // 날짜 검색 조건이 있을 경우
        if (isset($condition["start_date"]) && isset($condition["end_date"])) {
            $query->where([
                ["created_at", ">=", date("Y-m-d 0:0:0", strtotime($condition["start_date"]))],
                ["created_at", "<=", date("Y-m-d 23:59:59", strtotime($condition["end_date"]))]
            ]);
        }

        // 제목으로 검색
        if (isset($condition["search_title"]) && isset($condition["search_title"])) {
            $query = $query->where("push_title", "like", '%' . $condition["search_title"] . '%');
        }
        $query = $query->orderby("push_reservation_time", "desc")->paginate($this->paginate);
        return $query;
    }

    protected function init()
    {
        $this->path = config("constants.images.image.mobilePush.path");
        // 파일, 이미지를 업로드 할 전체 경로 입니다
        $this->uploadPath = config("constants.images.image.mobilePush.uploadPath");
        // 파일, 이미지 테이블에 저장할 table type 입니다
        $this->tableType = config("constants.images.image.mobilePush.type");
    }

}
