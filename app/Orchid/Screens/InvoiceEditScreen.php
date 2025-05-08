<?php

namespace App\Orchid\Screens;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class InvoiceEditScreen extends Screen
{

    public $invoice;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Invoice $invoice): iterable
    {
        return [
            'invoice' => $invoice,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->invoice->exists ? 'Edit invoice' : 'Creating a new invoice';
    }

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
                ->method('createOrUpdate')
                ->canSee(!$this->invoice->exists),

            Button::make('Update Invoice')
                ->icon('check-circle')
                ->method('createOrUpdate')
                ->canSee($this->invoice->exists),

            Button::make('Remove')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->invoice->exists),
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
            Layout::rows([

                Group::make([
                    Relation::make('invoice.account_id')
                        ->title('Creditor')
                        ->fromModel(Account::class, 'name'),

                    Relation::make('invoice.customer_id')
                        ->title('Debtor')
                        ->fromModel(Customer::class, 'name'),
                ]),

                Group::make([
                    DateTimer::make('invoice.date')
                        ->title('Date')
                        ->format('Y-m-d'),

                    Input::make('invoice.subject')
                        ->title('Subject')
                        ->help('What is the invoice about?'),
                ]),

                Group::make([
                    Select::make('invoice.currency')
                        ->title('Curency')
                        ->options(['CHF' => 'CHF', 'EUR' => 'EUR']),

                    Input::make('invoice.amount')
                        ->title('Amount')
                        ->type('number')
                        ->help('The total amount of the invoice'),

                    Input::make('invoice.discount')
                        ->title('Discount')
                        ->type('number'),
                ]),

                Quill::make('invoice.footer')
                    ->title('Footer'),

                Select::make('invoice.state')
                    ->title('State')
                    ->options([
                        'Creating' => 'Creating',
                        'Ready' => 'Ready',
                        'Sent' => 'Sent',
                        'Paid' => 'Paid'
                    ]),
            ]),

            Layout::view('invoice.preview')
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Request $request)
    {
        $this->invoice->fill($request->get('invoice'))->save();

        Alert::info($this->invoice->wasRecentlyCreated ? 'You have successfully created an invoice.' : 'Invoice updated');

        return redirect()->route('platform.invoice.edit', $this->invoice);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->invoice->delete();

        Alert::info('You have successfully deleted the invoice.');

        return redirect()->route('platform.invoice.list');
    }
}
