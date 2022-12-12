<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    public function tests_when_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new FakePaymentGateway();
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /** @test */
    public function charges_with_an_invalid_payment_token_fail()
    {
        $paymentGateway = new FakePaymentGateway();
        $this->expectException(PaymentFailedException::class);

        //test code that generates exception
        $paymentGateway->charge(2500, 'invalid-payment-token');
    }
}
