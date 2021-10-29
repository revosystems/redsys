<?php

namespace Revosystems\Redsys;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Revosystems\Redsys\Http\Livewire\CheckStatus;
use Revosystems\Redsys\Http\Livewire\RedsysForm;
use Revosystems\Redsys\View\Components\RadioSelector;
use FA;

class RedsysServiceProvider extends ServiceProvider
{
    //protected $defer = true;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'redsys');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'redsys');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewComponentsAs('redsys', [
            RadioSelector::class,
        ]);

        Livewire::component('redsys-form', RedsysForm::class);
        Livewire::component('check-status', CheckStatus::class);
//        Livewire::component('apple-pay-button', ApplePayButton::class);
//        Livewire::component('google-pay-button', GooglePayButton::class);

        $this->publishes([
            __DIR__.'/config/redsys.php' => config_path('redsys.php')
        ], 'config');
    }

    public function register()
    {
        Blade::directive("icon", function ($icon) {
            return FA::fixedWidth($icon);
        });
    }
}
