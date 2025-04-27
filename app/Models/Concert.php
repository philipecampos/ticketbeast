<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
