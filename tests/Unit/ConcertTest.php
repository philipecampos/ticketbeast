<?php

namespace Tests\Unit;

use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function can_get_formatted_date(): void
    {
        $concert = Concert::factory()->make([
            'date' => Carbon::parse('2016-12-01 8:00pm'),
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    #[Test]
    public function can_get_formatted_start_time(): void
    {
        $concert = Concert::factory()->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    #[Test]
    public function can_get_ticket_price_in_dollars(): void
    {
        $concert = Concert::factory()->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals(67.50, $concert->ticket_price_in_dollars);
    }

    #[Test]
    public function concerts_with_a_published_at_date_are_published(): void
    {
        $publishedConcertA = Concert::factory()->create([
            'published_at' => Carbon::parse('-1 week'),
        ]);
        $publishedConcertB = Concert::factory()->create([
            'published_at' => Carbon::parse('-1 week'),
        ]);
        $unpublishedConcert = Concert::factory()->create([
            'published_at' => null,
        ]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    #[Test]
    public function can_order_concert_tickets(): void
    {
        $concert = Concert::factory()->create();
        $concert->addTickets(3);

        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
    }

    #[Test]
    public function can_add_tickets(): void
    {
        $concert = Concert::factory()->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }
    
    #[Test]
    public function tickets_remaining_does_not_include_tickets_associated_with_an_order(): void
    {
        $concert = Concert::factory()->create();
        $concert->addTickets(50);
        $concert->orderTickets('jane@example.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());
    }
    
    #[Test]
    public function trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        $concert = Concert::factory()->create();
        $concert->addTickets(10);
        try {
            $concert->orderTickets('jane@example.com', 11);
        } catch (NotEnoughTicketsException $e) {
            $order = $concert->orders()->where('email', 'jane@example.com')->first();
            $this->assertNull($order);
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order succeeded even though there were not enough tickets remaining.');
    }

    #[Test]
    public function cannot_order_tickets_that_have_already_been_purchased()
    {
        $concert = Concert::factory()->create();
        $concert->addTickets(10);
        $concert->orderTickets('jane@example.com', 8);

        try {
            $concert->orderTickets('jane@example.com', 3);
        } catch (NotEnoughTicketsException $e) {
            $johnsOrder = $concert->orders()->where('email', 'john@example.com')->first();
            $this->assertNull($johnsOrder);
            $this->assertEquals(2, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order succeeded even though not enough tickets remaining.');
    }
}
