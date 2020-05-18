<?php


namespace LaravelSupports\Libraries\Supports\Databases\Traits;


use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LaravelSupports\Libraries\Exceptions\Logs\ExceptionLogger;
use LaravelSupports\Libraries\Supports\Http\Responses\ResponseTemplate;

trait TransactionTrait
{
    /**
     * $callback 을 실행시키면서 Exception 이 발생 시 Rollback 을 시키고 $errorCallback 을 실행합니다
     *
     * @param callable $callback
     * @param callable $errorCallback
     * @param callable|null $validationCallback
     * @return ResponseTemplate
     * @throws \ReflectionException
     * @author  TaehyunJeong
     * @added   2019-08-27
     * @updated 2020-04-27
     *
     * $validationCallback is not working
     * @updated 2020-04-27
     */
    function runTransaction(callable $callback, callable $errorCallback = null, callable $validationCallback = null)
    {
        $result = true;
        try {
            // transaction 을 시작합니다
            DB::beginTransaction();
            // $callback 이 함수인지 확인합니다
            if (is_callable($callback)) {
                $result = $callback();
            }
            DB::commit();
            // transaction 중 에러 발생 시
        } catch (\Throwable $e) {
            // DB rollback 을 실행합니다
            DB::rollback();
            $logger = new ExceptionLogger();
            $logger->report($e);

            // not working
            if (is_callable($validationCallback) && $e instanceof ValidationException) {
                $result = $validationCallback($e);
                // $errorCallback 이 함수인지 확인합니다
            } else if (is_callable($errorCallback)) {
                $result = $errorCallback($e);
            } else {
                // $errorCallback 이 함수가 아닐 경우 에러를 JsonObject 로 생성하여 return 합니다
                $result = new ResponseTemplate($e->getCode(), $e->getMessage(), [
                    "line" => $e->getLine(),
                    "string" => $e->getTraceAsString()
                ]);
            }
        } finally {
            return $result;
        }
    }

    function runTransactionWithDefaultValidation(callable $callback, callable $errorCallback)
    {
        $validationCallback = function (ValidationException $e) {
            return new ResponseTemplate(Response::HTTP_BAD_REQUEST, $e->getCode(), $e->getMessage());
        };
        return $this->runTransaction($callback, $errorCallback, $validationCallback);
    }

    /**
     * $callback 을 실행시키면서 Exception 이 발생 시 Rollback 을 시키고 $errorCallback 을 실행합니다
     * ajax 통신 용으로 return 을 JsonObject 를 return 합니다
     *
     * @param   $callback
     * @param   $errorCallback
     * @return  mixed
     * @author  TaehyunJeong
     * @added   2019-08-27
     * @updated 2019-08-27
     */
    function runTransactionWithAjax($callback, $errorCallback)
    {
        $result = "";
        try {
            // transaction 을 시작합니다
            DB::beginTransaction();
            // $callback 이 함수인지 확인합니다
            if (is_callable($callback)) {
                $result = $callback();
            }
            DB::commit();
            // transaction 중 에러 발생 시
        } catch (\Exception $e) {
            // DB rollback 을 실행합니다
            DB::rollback();
            // $errorCallback 이 함수인지 확인합니다
            if (is_callable($errorCallback)) {
                $result = $errorCallback($e);
            } else {
                // $errorCallback 이 함수가 아닐 경우 에러를 JsonObject 로 생성하여 return 합니다
                $result = new ResponseTemplate($e->getCode(), $e->getMessage(), [
                    "line" => $e->getLine(),
                    "string" => $e->getTraceAsString()
                ]);
            }
        }
        return $result;
    }
}
