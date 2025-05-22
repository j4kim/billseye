<?php

namespace App\Orchid\Resources;

use App\Orchid\Filters\CustomerFilter;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class CustomerResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Customer::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Group::make([
                Input::make('name')->title('Name')->required(),
                Input::make('email')->title('Email'),
            ]),
            Group::make([
                Input::make('street')->title('Street')->required(),
                Input::make('building_number')->title('Building Number'),
            ]),
            Group::make([
                Input::make('postal_code')->title('Postal Code')->required(),
                Input::make('city')->title('City')->required(),
                Input::make('country')->title('Country')->required(),
            ]),
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
        return [
            CustomerFilter::class,
        ];
    }

    public static function displayInNavigation(): bool
    {
        return false;
    }
}
