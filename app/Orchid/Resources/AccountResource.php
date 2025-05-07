<?php

namespace App\Orchid\Resources;

use Illuminate\Database\Eloquent\Model;
use Orchid\Crud\Resource;
use Orchid\Crud\ResourceRequest;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class AccountResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Account::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('name')->title('Name'),
            Input::make('street')->title('Street'),
            Input::make('building_number')->title('Building Number'),
            Input::make('postal_code')->title('Postal Code'),
            Input::make('city')->title('City'),
            Input::make('country')->title('Country'),
            Input::make('email')->title('Email'),
            Input::make('iban')->title('IBAN'),
            Input::make('smtp_config.smtp_host')->title('SMTP host'),
            Input::make('smtp_config.smtp_port')->title('SMTP port'),
            Input::make('smtp_config.smtp_username')->title('SMTP username'),
            Input::make('smtp_config.smtp_password')->title('SMTP password'),
        ];
    }

    /**
     * Get the columns displayed by the resource.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('id'),

            TD::make('name'),

            TD::make('created_at', 'Date of creation')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                }),

            TD::make('updated_at', 'Update date')
                ->render(function ($model) {
                    return $model->updated_at->toDateTimeString();
                }),
        ];
    }

    /**
     * Get the sights displayed by the resource.
     *
     * @return Sight[]
     */
    public function legend(): array
    {
        return [
            Sight::make('id'),
            Sight::make('name'),
            Sight::make('street'),
            Sight::make('building_number'),
            Sight::make('postal_code'),
            Sight::make('city'),
            Sight::make('country'),
            Sight::make('email'),
            Sight::make('iban'),
            Sight::make('created_at'),
            Sight::make('updated_at'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(): array
    {
        return [];
    }

    public static function displayInNavigation(): bool
    {
        return false;
    }
}
