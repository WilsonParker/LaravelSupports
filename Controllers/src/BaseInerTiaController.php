<?php


namespace LaravelSupports\Controllers;

use App\Models\User\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Inertia\Inertia;
use Inertia\Response;
use LaravelSupports\Auth\Facades\AuthService;
use LaravelSupports\Database\Traits\TransactionTrait;

abstract class BaseInerTiaController extends Controller
{
    use AuthorizesRequests, ValidatesRequests, TransactionTrait;

    protected array $params = [];
    protected string $lang = '';
    protected string $title = '';
    protected string $prefix = '';
    protected ?User $user;

    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        $this->setMiddleware();
        $this->afterInit();
    }

    protected function setMiddleware(): void
    {
        $this->middleware('auth:web');
    }

    protected function afterInit(): void {}

    protected function buildView(string $view, array $params = [], string $prefix = null): Response
    {
        $this->buildLayout();
        return Inertia::render(
            ($prefix ?? $this->prefix) . '/' . $view,
            [
                'user' => $this->getCurrentUser(),
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

    public function getCurrentUser(): ?User
    {
        return $this->user ?? $this->user = AuthService::currentUser();
    }
}
