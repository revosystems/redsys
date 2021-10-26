<?php


namespace Revosystems\RedsysPayment\Http\Controllers;

use Illuminate\Routing\Controller;
use Revosystems\RedsysPayment\Models\Redsys;

class CheckoutController extends Controller
{
    public function index()
    {
        $redsys = Redsys::get();
        return $redsys->render($redsys->getPaymentHandler(request('orderReference')), request('customerToken'), false, request('cardId'));
    }
}