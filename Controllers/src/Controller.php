<?php

namespace LaravelSupports\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;
use Inertia\Response;
use LaravelSupports\Auth\Facades\AuthService;
use LaravelSupports\Database\Traits\TransactionTrait;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, TransactionTrait;

    protected array $params = [];
    protected string $lang = '';
    protected string $title = '';
    protected string $prefix = '';
    protected ?Authenticatable $user;

    public function __construct() { $this->init(); }

    protected function init(): void
    {
        $this->setMiddleware();
        $this->afterInit();
    }

    protected function setMiddleware(): void
    {
        $this->middleware('auth:web');
    }

    protected function afterInit() {}

    protected function buildView(string $view, array $params = [], string $prefix = null, string $title = null): Response
    {
        $this->buildLayout();

        $prefix = ($prefix ?? $this->prefix);
        $view = $prefix !== "" ? $prefix . '/' . $view : $view;
        return Inertia::render(
            $view,
            [
                'title' => $title ?? $this->title,
                'user'  => $this->getCurrentUser(),
                ...$this->params,
                ...$params,
            ],
        );
    }

    protected function buildLayout(): void
    {
        $this->params['layout'] = [
            'title' => $this->title,
        ];
    }

    public function getCurrentUser(): ?Authenticatable
    {
        return $this->user ?? $this->user = AuthService::currentUser();
    }
}
