<?php


namespace Revosystems\RedsysPayment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Revosystems\RedsysPayment\Models\PaymentHandler;
use Revosystems\RedsysPayment\Models\RedsysPaymentGateway;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $paymentHandler = PaymentHandler::get($request->input('orderReference'));
        return RedsysPaymentGateway::get()->render($paymentHandler, $request->input('customerToken'), false, $request->input('cardId'));
    }
}