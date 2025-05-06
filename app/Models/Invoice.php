<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class Invoice extends Model
{
    use AsSource;

    /**
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'date',
        'subject',
        'currency',
        'amount',
        'discount',
        'footer',
        'state',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
