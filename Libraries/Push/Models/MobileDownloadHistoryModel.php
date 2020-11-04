<?php

namespace LaravelSupports\Libraries\Push\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class MobileDownloadHistoryModel extends Model
{
    public $incrementing = true;
    public $timestamps = false;
    protected $primaryKey = 'seq';
    protected $table = 'mm_app_download_history';
    protected $dateFormat = "Y-m-d";

    public function addHistory($os_type)
    {
        $model = $this->getHistory();
        switch ($os_type) {
            case "i" :
                $model->ios += 1;
                break;
            case "a" :
                $model->android += 1;
                break;
            default :
                throw new Exception("Not found os_type", 500);
        }
        return $model->save();
    }

    protected function getHistory(): MobileDownloadHistoryModel
    {
        $date = date($this->dateFormat);
        $this->download_date = $date;
        $query = function () use ($date) {
            return $this->where('download_date', $date)->first();
        };
        $model = $query();
        if ($model == null) {
            $this->save();
            $model = $query();
        }
        return $model;
    }
}
