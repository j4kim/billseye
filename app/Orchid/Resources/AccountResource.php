<?php

namespace App\Orchid\Resources;

use App\Models\Account;
use Orchid\Crud\Resource;
use Orchid\Crud\ResourceRequest;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
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
            Group::make([
                Input::make('name')->title('Name')->required(),
                Input::make('email')->title('Email')->required(),
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
            Input::make('iban')->title('IBAN')->required(),
            Group::make([
                Input::make('smtp_config.host')->title('SMTP host'),
                Input::make('smtp_config.port')->value(587)->title('SMTP port'),
                Input::make('smtp_config.password')->title('SMTP password'),
            ]),
            CheckBox::make('selected')
                ->value(session('account.selectedId') && request()->route('id') == session('account.selectedId'))
                ->disabled(session('account.selectedId') && request()->route('id') == session('account.selectedId'))
                ->title('Selected')
                ->placeholder('Select this account'),
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

            TD::make('name')->render(function ($model) {
                return $model->name . ($model->isSelected() ? ' <span class="badge bg-primary">Selected</span>' : '');
            }),

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
            Sight::make('selected')->render(function ($model) {
                return $model->isSelected() ? ' <span class="badge bg-primary">Selected</span>' : '';
            }),
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

    /**
     * Action to create and update the model
     *
     * @param ResourceRequest $request
     * @param Account         $Account
     */
    public function onSave(ResourceRequest $request, Account $account)
    {
        $account->forceFill($request->except('selected'))->save();
        if ($account->wasRecentlyCreated) {
            $account->users()->attach(auth()->id(), ['selected' => false]);
        }
        if ($request->selected) {
            $account->makeSelected();
        }
        Account::storeInSession();
    }
}
