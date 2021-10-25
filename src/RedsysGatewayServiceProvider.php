<?php

namespace Revosystems\RedsysGateway;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Revosystems\RedsysGateway\Http\Livewire\ApplePayButton;
use Revosystems\RedsysGateway\Http\Livewire\CheckStatus;
use Revosystems\RedsysGateway\Http\Livewire\Form;
use Revosystems\RedsysGateway\Http\Livewire\GooglePayButton;
use Revosystems\RedsysGateway\View\Components\RadioSelector;

class RedsysGatewayServiceProvider extends ServiceProvider
{
    //protected $defer = true;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'redsys-gateway');
        $this->loadViewComponentsAs('redsys-gateway', [
            RadioSelector::class
        ]);
        Livewire::component('form', Form::class);
        Livewire::component('check-status', CheckStatus::class);
        Livewire::component('apple-pay-button', ApplePayButton::class);
        Livewire::component('google-pay-button', GooglePayButton::class);
//        $this->publishes([
//            __DIR__.'/config/redsys-gateway.php' => config_path('redsys-gateway.php')
//        ], 'config');
    }

    public function register()
    {
//        Livewire::component('redsys-gateway::form', Form::class);
//        Livewire::component('redsys-gateway::check-status', Form::class);
//        \Illuminate\Contracts\View\View::component('redsys-gateway::radio-selector', RadioSelector::class);
    }

    public function provides()
    {
    }
}
