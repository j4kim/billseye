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
        $query->join('account_user', 'accounts.id', '=', 'account_user.account_id')
            ->where('account_user.user_id', auth()->id())
            ->select('accounts.*', 'account_user.selected');
    }
}
