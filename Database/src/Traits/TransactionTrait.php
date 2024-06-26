<?php


namespace LaravelSupports\Database\Traits;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LaravelSupports\Exceptions\Contracts\Abortable;
use LaravelSupports\Http\Responses\ResponseTemplate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

trait TransactionTrait
{

    function runTransactionWithLock(
        string   $lock = '',
        int      $second = 5,
        callable $callback = null,
        callable $errorCallback = null,
        callable $validationCallback = null,
        bool     $loggable = true,
    ) {
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

    /**
     * @throws \Throwable
     */
    private function rollbackAction(
        Throwable $throwable,
        callable  $errorCallback = null,
        callable  $validationCallback = null,
    ) {
        // DB rollback 을 실행합니다
        DB::rollback();
        if (is_callable($validationCallback) && $throwable instanceof ValidationException) {
            $result = $validationCallback($throwable);
        } else if (is_callable($errorCallback)) {
            $result = $errorCallback($throwable);
        } else {
            throw $throwable;
        }
        return $result;
    }

    function runTransactionWithDefaultValidation(callable $callback, callable $errorCallback): ResponseTemplate
    {
        $validationCallback = function (ValidationException $e) {
            return new ResponseTemplate(ResponseAlias::HTTP_BAD_REQUEST, $e->getCode(), $e->getMessage());
        };
        return $this->runTransaction($callback, $errorCallback, $validationCallback);
    }

    /**
     * $callback 을 실행시키면서 Exception 이 발생 시 Rollback 을 시키고 $errorCallback 을 실행합니다
     *
     * @param callable      $callback
     * @param callable|null $errorCallback
     * @param callable|null $validationCallback
     * @param bool          $loggable
     * @return ResponseTemplate
     * @author  WilsonParker
     * @added   2019-08-27
     * @updated 2020-04-27
     * @updated 2020-04-27
     * $validationCallback is not working
     * @updated 2021-11-18
     * add abort
     */
    function runTransaction(
        callable $callback,
        callable $errorCallback = null,
        callable $validationCallback = null,
        bool     $loggable = true,
    ) {
        $result = null;
        try {
            $result = $this->runAction($callback);
            // transaction 중 에러 발생 시
        } catch (\Throwable $t) {
            $result = $this->rollbackAction($t, $errorCallback, $validationCallback, $loggable);
        } finally {
            if ($result instanceof Abortable) {
                abort($result->getCode());
            }
        }
        return $result;
    }

    protected function runTransactionWithErrors(callable $callback)
    {
        return $this->runTransaction($callback, function (Throwable $throwable) {
            return back()->withInput()->withErrors(
                [
                    'message' => $throwable->getMessage(),
                ]);
        });
    }
}
