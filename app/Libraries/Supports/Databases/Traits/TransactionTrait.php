<?php


namespace LaravelSupports\Libraries\Supports\Databases\Traits;


use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LaravelSupports\Libraries\Exceptions\Logs\ExceptionLogger;
use LaravelSupports\Libraries\Supports\Http\Responses\ResponseTemplate;
use Throwable;

trait TransactionTrait
{

    private function runAction(callable $callback)
    {
        // transaction 을 시작합니다
        DB::beginTransaction();
        // $callback 이 함수인지 확인합니다
        if (is_callable($callback)) {
            $result = $callback();
        } else {
            $result = null;
        }
        DB::commit();
        return $result;
    }

    private function rollbackAction(Throwable $t, callable $errorCallback = null, callable $validationCallback = null, bool $loggable = true)
    {
        // DB rollback 을 실행합니다
        DB::rollback();
        if ($loggable) {
            $logger = new ExceptionLogger();
            $logger->report($t);
        }

        // not working
        if (is_callable($validationCallback) && $t instanceof ValidationException) {
            $result = $validationCallback($t);
            // $errorCallback 이 함수인지 확인합니다
        } else if (is_callable($errorCallback)) {
            $result = $errorCallback($t);
        } else {
            // $errorCallback 이 함수가 아닐 경우 에러를 JsonObject 로 생성하여 return 합니다
            $result = new ResponseTemplate($t->getCode(), $t->getCode(), $t->getMessage(), [
                "line" => $t->getLine(),
                "string" => $t->getTraceAsString()
            ]);
        }
        return $result;
    }

    /**
     * $callback 을 실행시키면서 Exception 이 발생 시 Rollback 을 시키고 $errorCallback 을 실행합니다
     *
     * @param callable $callback
     * @param callable|null $errorCallback
     * @param callable|null $validationCallback
     * @param bool $loggable
     * @return ResponseTemplate
     * @author  WilsonParker
     * @added   2019-08-27
     * @updated 2020-04-27
     *
     * $validationCallback is not working
     * @updated 2020-04-27
     */
    function runTransaction(callable $callback, callable $errorCallback = null, callable $validationCallback = null, bool $loggable = true)
    {
        try {
            $result = $this->runAction($callback);
            // transaction 중 에러 발생 시
        } catch (\Throwable $t) {
            $result = $this->rollbackAction($t, $errorCallback, $validationCallback, $loggable);
        } finally {
            return $result;
        }
    }

    function runTransactionWithLock(string $lock = '', int $second = 5, callable $callback = null, callable $errorCallback = null, callable $validationCallback = null, bool $loggable = true)
    {
        try {
            $lock = Cache::lock($lock, $second);
            if (!$lock->get()) {
                $lock->block(10);
            }
            $result = $this->runAction($callback);
            // transaction 중 에러 발생 시
        } catch (\Throwable $t) {
            $result = $this->rollbackAction($t, $errorCallback, $validationCallback, $loggable);
        } finally {
            optional($lock)->release();
            return $result;
        }
    }

    function runTransactionWithDefaultValidation(callable $callback, callable $errorCallback): ResponseTemplate
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
     * @author  WilsonParker
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
        } catch (Exception $e) {
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
