<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Home')
                ->icon('house')
                ->title('Navigation')
                ->route(config('platform.index')),

            Menu::make('Invoices')
                ->icon('file-earmark-text')
                ->route('platform.invoice.list'),

            Menu::make('Customers')
                ->icon('people')
                ->route('platform.resource.list', ['customer-resources']),

            Menu::make('Accounts')
                ->icon('person-badge')
                ->route('platform.resource.list', ['account-resources'])
                ->divider(),

            Menu::make(session('account.selected.name') ?? 'No account selected')
                ->title('Selected account')
                ->icon('person-badge')
                ->list(
                    collect(
                        session('account.names')
                    )->filter(
                        fn($id) => $id != session('account.selectedId')
                    )->map(
                        fn($id, $name) => Menu::make("$name")->route('platform.account.make-selected', [$id])
                    )->toArray()
                )
                ->divider()
                ->canSee(count(session('account.ids')) > 1 || !session('account.selectedId')),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
