<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Customer extends Model
{
    use HasFactory, AsSource, Filterable;

    /**
     * Scope only customers attached to accounts attached to the current user
     */
    #[Scope]
    protected function selectedAccounts(Builder $query): void
    {
        $query->where('account_id', session('account.selectedId'));
    }
}
