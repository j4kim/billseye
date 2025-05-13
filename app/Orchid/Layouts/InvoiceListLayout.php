<?php

namespace App\Orchid\Layouts;

use App\Models\Invoice;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class InvoiceListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'invoices';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID'),

            TD::make('date', 'Date'),

            TD::make('customer', 'Customer')->render(function (Invoice $invoice) {
                return $invoice->customer?->name;
            }),

            TD::make('subject', 'Subject'),

            TD::make('amount', 'Amount')->render(function (Invoice $invoice) {
                return '<span style="white-space:nowrap">' . $invoice->amount . ' ' . $invoice->currency . '</span>';
            }),

            TD::make('state', 'State')
                ->render(
                    fn(Invoice $invoice) => ModalToggle::make($invoice->state)
                        ->modal('setStateModal')
                        ->modalTitle($invoice->id)
                        ->method('updateState')
                        ->asyncParameters([
                            'invoice' => $invoice->id,
                        ])
                        ->addClass(match ($invoice->state) {
                            'Creating' => 'bg-secondary text-white',
                            'Ready' => 'bg-warning text-white',
                            'Sent' => 'bg-primary text-white',
                            'Paid' => 'bg-success text-white',
                        })
                ),

            TD::make('Actions')
                ->alignRight()
                ->render(function (Invoice $invoice) {
                    return Link::make('Edit')
                        ->icon('pencil')
                        ->route('platform.invoice.edit.data', $invoice);
                }),
        ];
    }
}
