<?php

namespace LaravelSupports\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use LaravelSupports\Views\Components\Tables\DataTableComponent;
use LaravelSupports\Views\Components\Tables\DataTableSearchComponent;
use LaravelSupports\Views\Components\Tables\TableSearchComponent;

class PackageComponentProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Blade::component('table-search', TableSearchComponent::class);
        Blade::component('data-table', DataTableComponent::class);
        Blade::component('data-table-search', DataTableSearchComponent::class);
    }
}
