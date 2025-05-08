<?php

namespace App\Orchid\Screens;

use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class InvoiceItemScreen extends Screen
{
    public $invoiceItem;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(InvoiceItem $invoiceItem): iterable
    {
        return [
            'invoiceItem' => $invoiceItem
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Invoice item';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Update')
                ->icon('check-circle')
                ->method('update'),

            Button::make('Remove')
                ->icon('trash')
                ->method('remove'),
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
                Input::make('invoiceItem.description')->title("Description")->required(),
                Input::make('invoiceItem.quantity')->title('Quantity')->type('number'),
                Input::make('invoiceItem.unit_price')->title('Unit price'),
            ])
        ];
    }
    public function update(Request $request)
    {
        $this->invoiceItem->fill($request->invoiceItem)->save();
        return redirect()->route('platform.invoice.edit', $this->invoiceItem->invoice_id);
    }

    public function remove()
    {
        $this->invoiceItem->delete();
        return redirect()->route('platform.invoice.edit', $this->invoiceItem->invoice_id);
    }
}
