<?php

namespace App\Providers;

use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\ExpenseRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        ExpenseRepositoryInterface::class => ExpenseRepository::class,
        UserRepositoryInterface::class    => UserRepository::class,
    ];

    public function register(): void {}

    public function boot(): void {}
}
