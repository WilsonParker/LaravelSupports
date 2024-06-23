<?php

namespace LaravelSupports\Auth;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use LaravelSupports\Auth\Contracts\AuthRepositoryContract;

class AuthService
{
    protected string $middleware;

    public function __construct(
        private readonly AuthRepositoryContract $repository,
    )
    {
        $this->middleware = config('auth.middleware', 'auth:api');
    }

    public function currentUser(): ?Authenticatable
    {
        return Auth::guard($this->middleware)->user();
    }

    public function check(): bool
    {
        return Auth::guard($this->middleware)->check();
    }

    /**
     * @throws \Exception
     */
    public function testUser(): Authenticatable
    {
        if (config('app.debug')) {
            return $this->repository->findOrFail(1);
        }
        throw new Exception('You can only use this function in test mode');
    }

}
