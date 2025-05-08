<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use NumberFormatter;
use Orchid\Screen\AsSource;

class InvoiceItem extends Model
{
    use AsSource;

    protected $guarded = [];

    public function total(): Attribute
    {
        return Attribute::make(
            fn() => $this->unit_price * $this->quantity
        );
    }
}
