<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Account extends Model
{
    use AsSource, Filterable;

    protected function casts(): array
    {
        return [
            'smtp_config' => 'encrypted:array',
        ];
    }
}
