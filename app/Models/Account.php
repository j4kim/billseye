<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    protected static function booted(): void
    {
        // Scope only accounts attached to the current user
        static::addGlobalScope('mine', function (Builder $builder) {
            $builder->whereIn('id', session('account.ids') ?? []);
        });
    }

    public function isSelected(): bool
    {
        return $this->id === session('account.selectedId');
    }

    public static function storeInSession()
    {
        $accounts = auth()->user()->accounts;
        $selected = $accounts->where('pivot.selected')->first();
        session(['account' => [
            'ids' => $accounts->pluck('id')->toArray(),
            'names' => $accounts->pluck('id', 'name')->toArray(),
            'selected' => $selected,
            'selectedId' => $selected?->id,
        ]]);
    }

    public function makeSelected()
    {
        DB::table('account_user')
            ->where('user_id', auth()->id())
            ->update(['selected' => false]);
        DB::table('account_user')
            ->where('user_id', auth()->id())
            ->where('account_id', $this->id)
            ->update(['selected' => true]);
    }
}
