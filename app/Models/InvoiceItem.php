<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class InvoiceItem extends Model
{
    use AsSource;

    protected $guarded = [];

    protected $touches = ['invoice'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function booted()
    {
        static::saved(function ($invoiceItem) {
            if ($invoiceItem->isDirty('quantity', 'unit_price')) {
                $invoiceItem->invoice->computeTotal();
            }
        });
        static::deleted(function ($invoiceItem) {
            $invoiceItem->invoice->computeTotal();
        });
    }

    public function total(): Attribute
    {
        return Attribute::make(
            fn() => $this->unit_price * $this->quantity
        );
    }
}
