<?php

namespace App\Orchid\Screens\Invoice;

use App\Models\Account;
use App\Models\Customer;
use App\Orchid\Layouts\InvoiceTabMenu;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class InvoiceDataScreen extends InvoiceBaseScreen
{
    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Create Invoice')
                ->icon('check-circle')
                ->method('create')
                ->canSee(!$this->invoice->exists),

            Button::make('Update')
                ->icon('check-circle')
                ->method('update')
                ->canSee($this->invoice->exists),

            ...parent::commandBar(),
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
            InvoiceTabMenu::class,

            Layout::rows([

                Group::make([
                    Relation::make('invoice.account_id')
                        ->title('Creditor')
                        ->fromModel(Account::class, 'name')
                        ->required(),

                    Relation::make('invoice.customer_id')
                        ->title('Debtor')
                        ->fromModel(Customer::class, 'name'),
                ]),

                Group::make([
                    DateTimer::make('invoice.date')
                        ->title('Date')
                        ->format('Y-m-d')
                        ->value(now())
                        ->required(),

                    Input::make('invoice.subject')
                        ->title('Subject')
                        ->help('What is the invoice about?')
                        ->required(),
                ]),

                Group::make([
                    Select::make('invoice.currency')
                        ->title('Curency')
                        ->options(['CHF' => 'CHF', 'EUR' => 'EUR']),

                    Input::make('invoice.amount')
                        ->title('Amount')
                        ->type('number')
                        ->help('The total amount of the invoice')
                        ->value(0)
                        ->disabled($this->invoice ? $this->invoice->invoiceItems()->exists() : false),

                    Input::make('invoice.discount')
                        ->title('Discount')
                        ->type('number'),
                ]),

                Quill::make('invoice.footer')
                    ->title('Footer'),

                Group::make([
                    Select::make('invoice.state')
                        ->title('State')
                        ->options([
                            'Creating' => 'Creating',
                            'Ready' => 'Ready',
                            'Sent' => 'Sent',
                            'Paid' => 'Paid'
                        ]),

                    RadioButtons::make('invoice.layout')
                        ->title('Layout')
                        ->value('sm')
                        ->options([
                            'sm' => 'small',
                            'md' => 'medium',
                        ]),

                    DateTimer::make('invoice.paid_at')
                        ->title('Paid at')
                        ->format('Y-m-d'),
                ]),

            ])
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $this->invoice->fill($request->get('invoice'))->save();
        Alert::info('You have successfully created an invoice.');
        return redirect()->route('platform.invoice.edit.items', $this->invoice);
    }
}
