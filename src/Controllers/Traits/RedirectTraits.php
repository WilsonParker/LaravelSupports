<?php

namespace LaravelSupports\Controllers\Traits;

use LaravelSupports\Supports\Data\StringHelper;
use Throwable;

trait RedirectTraits
{
    /**
     * config 정보를 포함하여 이전 페이지로 이동 합니다
     *
     * @param string $prefix
     * @param bool $redirect
     * @param bool $isSuccess
     * @param array $replace
     * @param string|null $message
     * @return \Illuminate\Http\RedirectResponse
     * @author  WilsonParker
     * @added   2020/12/08
     * @updated 2021/10/06
     */
    protected function backWithConfig(string $prefix, bool $redirect = true, bool $isSuccess = true, array $replace = [], string $message = null): \Illuminate\Http\RedirectResponse
    {
        if (!isset($message)) {
            $message = $isSuccess ? config($prefix . '.success.message') : config($prefix . '.fail.message');
        }
        $helper = new StringHelper();
        $message = $helper->replaceWithCollection($replace, $message);
        return $this->backWithMessage($message, $redirect);
    }

    protected function backWithMessage(string $message, bool $redirect = true): \Illuminate\Http\RedirectResponse
    {
        if ($redirect) {
            return redirect()->back()->with([
                'message' => $message
            ]);
        } else {
            return back()->withInput()->with([
                'message' => $message
            ]);
        }
    }

    protected function backWithErrors(Throwable $e): \Illuminate\Http\RedirectResponse
    {
        return back()->withInput()->withErrors($e->getMessage())->with([
            'message' => $e->getMessage()
        ]);
    }

    protected function redirectUrlWithMessage(string $message, string $url = '', array $params = []): \Illuminate\Http\RedirectResponse
    {
        $url = $url != '' ? $url : url()->previous();
        return redirect($url)->with([
            'message' => $message
        ]);
    }

    protected function redirectRouteWithMessage(string $message, string $route, array $params = []): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route($route, $params)->with([
            'message' => $message
        ]);
    }
}
