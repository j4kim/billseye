<?php

namespace App\Orchid\Screens;

use App\Models\Customer;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class CustomerScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'customers' => Customer::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Customer';
    }

    public function description(): ?string
    {
        return 'Customer screen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add Customer')
                ->modal('customerModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('customers', [
                TD::make('name'),
            ]),

            Layout::modal('customerModal', Layout::rows([
                Input::make('customer.name')
                    ->title('Name')
                    ->placeholder('Enter customer name')
                    ->help('The name of the customer to be created.')
            ]))
                ->title('Create Customer')
                ->applyButton('Add Customer'),
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function create(Request $request)
    {
        // Validate form data, save task to database, etc.
        $request->validate([
            'customer.name' => 'required|max:255',
        ]);

        $c = new Customer();
        $c->name = $request->input('customer.name');
        $c->email = '';
        $c->street = '';
        $c->postal_code = '';
        $c->city = '';
        $c->country = '';
        $c->save();
    }
}
