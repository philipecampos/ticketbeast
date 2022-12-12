<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $subtitle
 * @property Carbon $date
 * @property int $ticket_price
 * @property string $venue
 * @property string $venue_address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $additional_information
 * @property string $created_at
 * @property string $updated_at
 *
 * @property string $formatted_date
 * @property string $formatted_start_time
 * @property string $ticket_price_in_dollars
 *
 * @method $this published
 */
class Concert extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['date'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /*
     * TODAS as formas comentadas funcionam
     *
     */

//    public function getFormattedDateAttribute()
//    {
//        return $this->date->format('F j, Y');
//    }

    protected function formattedDate(): Attribute
    {

//        return Attribute::make(
//            get: fn ($value) => $this->date->format('F j, Y')
//        );

//        return Attribute::get(function ($value) { return $this->date->format('F j, Y'); });
        return Attribute::get(fn($value) => $this->date->format('F j, Y'));
    }

//    public function getFormattedStartTimeAttribute()
//    {
//        return $this->date->format('g:ia');
//    }

    public function formattedStartTime(): Attribute
    {
        return Attribute::get(fn($value) => $this->date->format('g:ia'));
    }

    public function ticketPriceInDollars(): Attribute
    {
        return Attribute::get(fn($value) => number_format($this->ticket_price / 100, 2));
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets(string $email, int $ticketQuantity): Order
    {
        /** @var Order $order */
        $order = $this->orders()->create(['email' => $email]);
        $tickets = $this->tickets()->take($ticketQuantity)->get();

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function addTickets(int $quantity): void
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }
    }

    public function ticketsRemaining(): int
    {
        return $this->tickets()->whereNull('order_id')->count();
    }
}
