<?php

namespace Revosystems\RedsysPayment;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Revosystems\RedsysPayment\Http\Livewire\ApplePayButton;
use Revosystems\RedsysPayment\Http\Livewire\CheckStatus;
use Revosystems\RedsysPayment\Http\Livewire\Form;
use Revosystems\RedsysPayment\Http\Livewire\GooglePayButton;
use Revosystems\RedsysPayment\View\Components\RadioSelector;

class RedsysPaymentServiceProvider extends ServiceProvider
{
    //protected $defer = true;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'redsys-payment');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'redsys-payment');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewComponentsAs('redsys-payment', [
            RadioSelector::class
        ]);
        Livewire::component('form', Form::class);
        Livewire::component('check-status', CheckStatus::class);
        Livewire::component('apple-pay-button', ApplePayButton::class);
        Livewire::component('google-pay-button', GooglePayButton::class);

        $this->publishes([
            __DIR__.'/config/redsys-payment.php' => config_path('redsys-payment.php')
        ], 'config');
    }

    public function register()
    {
    }
}
