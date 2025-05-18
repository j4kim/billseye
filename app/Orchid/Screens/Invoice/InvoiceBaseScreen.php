<?php

namespace App\Orchid\Screens\Invoice;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class InvoiceBaseScreen extends Screen
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
        return $this->invoice->exists ? "Edit invoice #{$this->invoice->id}" : 'Creating a new invoice';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Duplicate invoice')
                ->icon('copy')
                ->method('duplicate')
                ->canSee($this->invoice->exists),

            Button::make('Delete invoice')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->invoice->exists)
                ->confirm('After deleting, the invoice will be gone forever.'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->invoice->invoiceItems()->delete();
        $this->invoice->delete();

        Alert::info('You have successfully deleted the invoice.');

        return redirect()->route('platform.invoice.list');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate(Request $request)
    {
        $newInvoice = $this->invoice->replicate([
            'pdf_path',
            'pdf_generated_at',
            'email_sent_at',
        ])->fill([
            'date' => now(),
            'state' => 'Creating',
        ]);
        $newInvoice->save();
        $newInvoice->invoiceItems()->createMany(
            $this->invoice->invoiceItems()->select('description', 'quantity', 'order', 'unit_price')->get()->toArray()
        );
        return redirect()->route('platform.invoice.edit.data', $newInvoice);
    }
}
