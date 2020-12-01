<?php

namespace LaravelSupports\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use LaravelSupports\Views\Components\Cards\CardSearchComponent;
use LaravelSupports\Views\Components\Inputs\CheckBoxListComponent;
use LaravelSupports\Views\Components\Inputs\GridCheckBoxListComponent;
use LaravelSupports\Views\Components\Inputs\InlineCheckBoxListComponent;
use LaravelSupports\Views\Components\Inputs\LikeRadioComponent;
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
        Blade::component('table-search', TableSearchComponent::class);
        Blade::component('data-table', DataTableComponent::class);
        Blade::component('data-table-search', DataTableSearchComponent::class);
        Blade::component('like-radio', LikeRadioComponent::class);
        Blade::component('checkbox-list', CheckBoxListComponent::class);
        Blade::component('inline-checkbox-list', InlineCheckBoxListComponent::class);
        Blade::component('grid-checkbox-list', GridCheckBoxListComponent::class);
        Blade::component('card-search', CardSearchComponent::class);
    }
}
