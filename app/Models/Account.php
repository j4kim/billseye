<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
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
        if (App::runningInConsole()) return;
        // Scope only accounts attached to the current user
        static::addGlobalScope('mine', function (Builder $builder) {
            $builder->whereIn('id', session('account.ids') ?? []);
        });
    }

    public function isSelected(): bool
    {
        return $this->id === session('account.selected.id');
    }

    public static function storeInSession()
    {
        $user = auth()->user();
        session(['account' => [
            'ids' => $user->accounts->pluck('id')->toArray(),
            'names' => $user->accounts->pluck('id', 'name')->toArray(),
            'selected' => $user->accounts->find($user->selected_account_id),
        ]]);
    }

    public function makeSelected()
    {
        auth()->user()->update(['selected_account_id' => $this->id]);
    }
}
