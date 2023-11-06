<?php


namespace LaravelSupports\Html\Traits;


use LaravelSupports\Supports\Html\Traits\Model;

trait TagBuildTrait
{

    /**
     * html tag 중 순서 이동이 발생하는 li, tr 등에 attributes 를 추가하는 코드 입니다
     *
     * @param   $ix
     * @param   $sort
     * @return  string
     * @author  WilsonParker
     * @added   2019-08-26
     * @updated 2019-08-26
     */
    public function buildSortAttribute($ix, $sort)
    {
        return $this->buildDataObjAttributes([
            "ix" => $ix,
            "old_sort" => $sort
        ]);
        // return "data-obj=\"{ 'ix' : '$ix', 'old_sort' : '$sort' }\"";
    }

    /**
     * html tag 에 data-obj attributes 를 생성합니다
     *
     * @param array $data
     * ex)
     * [
     *  "ix" => 1,
     *  "old_sort" => 5
     * ]
     *
     * @return  string
     * ex)
     * data-obj="{'ix' : '1','old_sort' : '5'}"
     *
     * @author  WilsonParker
     * @added   2019-08-26
     * @updated 2019-08-26
     */
    public function buildDataObjAttributes(array $data)
    {
        $result = "data-obj=\"{";
        foreach ($data as $key => $val) {
            $result .= "'$key' : '$val'";
            if ($key !== array_key_last($data))
                $result .= ",";
        }
        $result .= "}\"";
        return $result;
    }

    /**
     * 기본으로 model 의 created_at 을 return 하며
     * created_at 과 updated_at 이 다를 경우 <br>로 구분을 하여 둘 다 return 합니다
     *
     * @param Model
     * @return  string
     * @author  WilsonParker
     * @added   2019-08-26
     * @updated 2019-08-26
     */
    public function buildCreatedAndUpdatedTime($model)
    {
        $result = $model->created_at;
        if (date("Y-m-d h:i:s", strtotime($model->created_at)) != date("Y-m-d h:i:s", strtotime($model->updated_at))) {
            $result .= "<br/> / " . $model->updated_at;
        }
        return $result;
    }

    /**
     * select tag 에서 option 에 selected attribute 를 설정해줍니다
     *
     * ex)
     * {{ $checkSelectTag($model->target, "new", "target") }
     * {{ $checkSelectTag($model->display, "1", "display", "checked") }}
     *
     * @param   $from
     * 해당 model 의 ix
     * 고정으로 비교할 값
     * @param   $to
     * for 로 생성되는 list item 의 ix
     * 변경되면서 비교할 값
     * @param   $old
     * old 값을 가져올 name
     * @param string $selected
     * $from 과 $to, $old 와 $to 를 비교해가며 같을 경우 return 하는 값이며
     * 기본으로 "selected" 를 제공합니다
     * @return  string
     * @author  WilsonParker
     * @added   2019-08-21
     * @updated 2019-08-21
     */
    public function checkSelectTag($from, $to, $old, $selected = "selected")
    {
        if (isset($old) && !is_null(old($old))) {
            if (old($old) == $to)
                return $selected;
        } else if ($from == $to) {
            return $selected;
        }
        return "";
    }

}
