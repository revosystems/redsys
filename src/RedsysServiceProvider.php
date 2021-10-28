<?php

namespace Revosystems\Redsys;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Revosystems\Redsys\Http\Livewire\ApplePayButton;
use Revosystems\Redsys\Http\Livewire\CheckStatus;
use Revosystems\Redsys\Http\Livewire\Redsys;
use Revosystems\Redsys\Http\Livewire\GooglePayButton;
use Revosystems\Redsys\Http\Livewire\TokenizedCards;
use Revosystems\Redsys\View\Components\RadioSelector;

class RedsysServiceProvider extends ServiceProvider
{
    //protected $defer = true;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'redsys');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'redsys');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewComponentsAs('redsys', [
            RadioSelector::class
        ]);
        Livewire::component('redsys', Redsys::class);
        Livewire::component('tokenized-cards', TokenizedCards::class);
        Livewire::component('check-status', CheckStatus::class);
//        Livewire::component('apple-pay-button', ApplePayButton::class);
//        Livewire::component('google-pay-button', GooglePayButton::class);

        $this->publishes([
            __DIR__.'/config/redsys.php' => config_path('redsys.php')
        ], 'config');
    }

    public function register()
    {
    }
}
