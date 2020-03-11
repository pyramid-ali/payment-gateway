<?php


namespace Alish\PaymentGateway\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ZarinpalController extends Controller
{

    public function __invoke(Request $request)
    {
        if ($request->get('Status') === 'OK') {

            $authority = $request->get('Authority');

            $payment = Payment::retrieve('zarinpal', $authority);

            if ($payment->ref_id) {
                return redirect()->route('payment.success', ['payment' => $payment]);
            }

            $result = PaymentGateway::verify(
                $authority,
                $payment->amount
            );

            if ($result['ok']) {
                $payment->succeed($result['ref_id']);
                return redirect()->route('payment.success', ['payment' => $payment]);
            }

            $payment->failed();
        }

        return redirect()->route('payment.failed');

    }

}