<?php

namespace Tests\Feature;

use App\Models\Concert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    #[Test]
    public function customer_can_purchase_concert_tickets(): void
    {
        $concert = Concert::factory()->create(['ticket_price' => 3250]);
        $response = $this->postJson("/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(201);
        $this->assertEquals(9750, $paymentGateway->totalCharges());

        $order = $concert->orders->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);

        $this->assertEquals(3, $order->count());

    }
}
