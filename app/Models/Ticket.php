<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    #[Scope]
    protected function available(Builder $query): void
    {
        $query->whereNull('order_id');
    }
}
