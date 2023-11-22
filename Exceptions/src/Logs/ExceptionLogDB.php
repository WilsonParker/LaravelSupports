<?php

namespace LaravelSupports\Exceptions\Logs;

use Exception;
use LaravelSupports\Exceptions\Contracts\ExceptionRecordable;
use LaravelSupports\Exceptions\Models\ExceptionModel;

/**
 * Database 에 Exception Log 를 기록합니다
 *
 * @author  WilsonParker
 * @class   ExceptionLogDB.php
 * @added   2019.03.04
 * @updated 2020.04.22
 */
class ExceptionLogDB implements ExceptionRecordable
{

    /**
     * exception 을 database 에 저장합니다
     *
     * @param Exception $exception
     * @return  Void
     * @author  WilsonParker
     * @added   2019.03.04
     * @updated 2020.04.22
     */
    public function record($exception)
    {
        try {
            $data = [
                ExceptionModel::KEY_CODE => (string)$exception->getCode(),
                ExceptionModel::KEY_MESSAGE => $exception->getMessage(),
                ExceptionModel::KEY_URL => request()->fullUrl(),
                ExceptionModel::KEY_FILE => $exception->getFile(),
                ExceptionModel::KEY_CLASS => get_class($exception),
                ExceptionModel::KEY_TRACE => $exception->err_trace
            ];

            $model = new ExceptionModel();
            $model->bind($data);
            $model->save();

        } catch (Exception $e) {
            dd($e);
        }
    }

}
