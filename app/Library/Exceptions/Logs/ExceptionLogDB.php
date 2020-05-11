<?php

namespace App\Library\Exceptions\Logs;

use App\Library\Exceptions\Contracts\ExceptionRecordable;
use App\Library\Exceptions\Models\ExceptionModel;
use App\Library\Supports\Data\TableIterator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Database 에 Exception Log 를 기록합니다
 *
 * @author  WilsonParker
 * @class   ExceptionLogDB.php
 * @added   2019.03.04
 * @updated 2020.04.22
 * @bug
 * @todo
 * @see
 */
class ExceptionLogDB implements ExceptionRecordable
{

    /**
     * exception 을 database 에 저장합니다
     *
     * @param \Exception $exception
     * @return  Void
     * @author  WilsonParker
     * @added   2019.03.04
     * @updated 2020.04.22
     * @bug
     * @see
     */
    public function record($exception)
    {
        try {
            $data = [
                ExceptionModel::KEY_CODE => (string)$exception->getCode(),
                ExceptionModel::KEY_MESSAGE => $exception->getMessage(),
                ExceptionModel::KEY_URL => Request::fullUrl(),
                ExceptionModel::KEY_FILE => $exception->getFile(),
                ExceptionModel::KEY_CLASS => get_class($exception),
                ExceptionModel::KEY_TRACE => $exception->err_trace
            ];

            $model = new ExceptionModel();
            $model->bind($data);
            $model->save();

        } catch (\Exception $e) {
            dd($e);
        }
    }

}
