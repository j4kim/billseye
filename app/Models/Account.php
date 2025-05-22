<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Account extends Model
{
    use AsSource, Filterable, HasFactory;

    protected function casts(): array
    {
        return [
            'smtp_config' => 'encrypted:array',
        ];
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Scope only accounts attached to the current user
     */
    #[Scope]
    protected function mine(Builder $query): void
    {
        $query->whereIn('id', session('account.ids'));
    }

    public function isSelected(): bool
    {
        return $this->id === session('account.selectedId');
    }
}
