<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concert extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at');
    }

    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date->format('F j, Y'),
        );
    }

    protected function formattedStartTime(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date->format('g:ia'),
        );
    }

    protected function ticketPriceInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->ticket_price / 100, 2),
        );
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets(string $email, int $ticketQuantity): Order
    {
        $order = $this->orders()->create(['email' => $email]);
        $tickets = $this->tickets()->take($ticketQuantity)->get();

        foreach($tickets as $ticket) {
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
