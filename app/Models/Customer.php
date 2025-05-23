<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Customer extends Model
{
    use HasFactory, AsSource, Filterable;

    protected static function booted(): void
    {
        if (App::runningInConsole()) return;
        // Scope only customers attached to accounts attached to the current user
        static::addGlobalScope('selectedAccount', function (Builder $builder) {
            $builder->where('account_id', session('account.selectedId'));
        });
    }
}
