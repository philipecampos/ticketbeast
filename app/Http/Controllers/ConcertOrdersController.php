<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Models\Concert;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    /**
     * @param PaymentGateway $paymentGateway
     */
    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(request(), [
            'email' => 'required|email',
            'ticket_quantity' => 'required|integer|min:1',
            'payment_token' => 'required'
        ]);

        try {
            /** @var Concert $concert */
            $this->paymentGateway->charge(request('ticket_quantity') * $concert->ticket_price, request('payment_token'));
            $order = $concert->orderTickets(request('email'), request('ticket_quantity'));
            return response()->json([], 201);
        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        }
    }
}
