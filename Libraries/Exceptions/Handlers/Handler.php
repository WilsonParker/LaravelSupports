<?php

namespace LaravelSupports\Libraries\Exceptions\Handlers;

use Exception;
use Facades\LaravelSupports\Libraries\Exceptions\Log\ExceptionLogger;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use PharIo\Manifest\InvalidUrlException;
use SebastianBergmann\CodeCoverage\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
        App\Exceptions\LogException::class
    ];

    /**
     * Exception 에 따라 ExceptionHandleable 을 구현한 Handler 를 설정합니다
     * Handler => [ Exceptions ]
     *
     * @see
     */
    protected $handlers = [
        InvalidExceptionHandler::class => [
            InvalidUrlException::class,
            InvalidArgumentException::class
        ],
        Handler::class => [
            InvalidArgumentException::class
        ]
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
        // ExceptionLogger::report($exception);
        $this->handle($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Exception 종류에 따라 $handlers 에 설정된 handler 를 사용해 처리합니다
     *
     * @param Exception $exception
     * @return  Void
     * @author  WilsonParker
     * @added   2019.03.05
     * @updated 2019.03.05
     * @bug
     * @see
     */
    protected function handle($exception)
    {
        $exceptionCls = get_class($exception);
        foreach ($this->handlers as $key => $val_list) {
            foreach ($val_list as $val) {
                if ($exceptionCls == $val) {
                    ObjectHelper::createInstance($key)->handle($exception);
                    return;
                }
            }
        }
    }


    /**
     * Convert an authentication exception into a response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     */
    /*public function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()->guest($exception->redirectTo() ?? route('auth.index'));
    }*/
}
