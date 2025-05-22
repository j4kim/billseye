<?php

namespace App\Models;

use App\Tools\QrBillGenerator;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Screen\AsSource;

class Invoice extends Model
{
    use AsSource, Filterable;

    protected static function booted(): void
    {
        // Scope only invoices attached to accounts attached to the current user
        static::addGlobalScope('selectedAccount', function (Builder $builder) {
            $builder->where('account_id', session('account.selectedId'));
        });
    }

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Name of columns to which http sorting can be applied
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'date',
        'amount',
        'state',
    ];

    /**
     * Name of columns to which http filter can be applied
     *
     * @var array
     */
    protected $allowedFilters = [
        'subject' => Like::class,
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'float',
        ];
    }

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            fn() => 'CHF ' . number_format($this->amount, 2, '.', ' '),
        );
    }

    protected function pdfFilename(): Attribute
    {
        return Attribute::make(
            fn() => $this->date->format('Y_m_d_')
                . __('invoice') . '_' . $this->id . '_'
                . Str::slug($this->account->name, '_') . '_'
                . Str::slug($this->customer->name, '_')
                . '.pdf'
        );
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function orderedInvoiceItems(): HasMany
    {
        return $this->invoiceItems()->orderBy('order', 'asc');
    }

    public function computeTotal()
    {
        $this->amount = $this->invoiceItems->pluck('total')->sum();
        $this->save();
    }

    public function generateQrBill(): string
    {
        return QrBillGenerator::generate([
            'creditor' => [
                'name' => $this->account->name,
                'street' => $this->account->street,
                'buildingNumber' => $this->account->building_number,
                'postalCode' => $this->account->postal_code,
                'city' => $this->account->city,
                'country' => $this->account->country,
            ],
            'debtor' => $this->customer ? [
                'name' => $this->customer->name,
                'street' => $this->customer->street,
                'buildingNumber' => $this->customer->building_number,
                'postalCode' => $this->customer->postal_code,
                'city' => $this->customer->city,
                'country' => $this->customer->country,
            ] : [],
            'iban' => $this->account->iban,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'reference' => $this->id,
            'additional-information' => Str::replaceMatches('/\. .*/', '', $this->subject),
        ]);
    }
}
