<?php

namespace Tests\Feature;

use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function user_can_view_a_published_concert_listing(): void
    {
        $concert = Concert::create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555.',
            'published_at' => Carbon::parse('-1 week'),
        ]);

        $response = $this->get("/concerts/" . $concert->id);

        $response->assertStatus(200);

        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Lethargy');
        $response->assertSee('December 13, 2016');
        $response->assertSee('8:00pm');
        $response->assertSee('32.50');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laraville, ON 17916');
        $response->assertSee('For tickets, call (555) 555-5555.');
    }

    #[Test]
    public function user_cannot_view_unpublished_concert_listings(): void
    {
        $concert = Concert::factory()->create([
            'published_at' => null,
        ]);

        $results = $this->get("/concerts/{$concert->id}");
        $results->assertStatus(404);
    }
}
