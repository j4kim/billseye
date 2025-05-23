<?php

namespace App\Orchid\Screens\Invoice;

use App\Models\Invoice;
use App\Orchid\Layouts\InvoiceListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class InvoiceListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'invoices' => Invoice::with('customer')->filters()->defaultSort('id')->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Invoices';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Create Invoice')
                ->icon('plus-circle')
                ->route('platform.invoice.new')
                ->canSee(!!session('account.selectedId'))
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
            Layout::modal('setStateModal', Layout::rows([
                RadioButtons::make('state')
                    ->title('State')
                    ->options([
                        'Creating' => 'Creating',
                        'Ready' => 'Ready',
                        'Sent' => 'Sent',
                        'Paid' => 'Paid'
                    ]),
            ]))
                ->deferred('loadInvoiceOnOpenModal'),

            InvoiceListLayout::class
        ];
    }
    public function loadInvoiceOnOpenModal(Invoice $invoice): iterable
    {
        return [
            'state' => $invoice->state,
        ];
    }

    public function updateState(Request $request, Invoice $invoice)
    {
        $invoice->state = $request->state;
        $invoice->save();
    }
}
