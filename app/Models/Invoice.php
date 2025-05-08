<?php

namespace App\Models;

use App\Tools\QrBillGenerator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NumberFormatter;
use Orchid\Screen\AsSource;

class Invoice extends Model
{
    use AsSource;

    /**
     * @var array
     */
    protected $fillable = [
        'account_id',
        'customer_id',
        'date',
        'subject',
        'currency',
        'amount',
        'discount',
        'footer',
        'state',
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
        $formatter = new NumberFormatter(config("app.locale"), NumberFormatter::CURRENCY);
        return Attribute::make(
            fn() => $formatter->formatCurrency($this->amount, $this->currency),
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
            'additional-information' => $this->subject,
        ]);
    }
}
