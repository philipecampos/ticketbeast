<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    #[Test]
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new FakePaymentGateway();

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }
    
    #[Test]
    public function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new FakePaymentGateway();
            $paymentGateway->charge(2500, 'invalid-token');
            $this->assertEquals(0, $paymentGateway->totalCharges());
        } catch (PaymentFailedException $e) {
            $this->assertTrue(true);
        }
    }
}
