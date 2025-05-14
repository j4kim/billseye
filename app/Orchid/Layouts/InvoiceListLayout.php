<?php

namespace App\Orchid\Layouts;

use App\Models\Invoice;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\Currency;
use Orchid\Screen\Components\Cells\DateTime;
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
            TD::make('id', 'ID')->sort(),

            TD::make('date', 'Date')
                ->usingComponent(DateTime::class, format: 'd.m.Y')
                ->sort(),

            TD::make('customer', 'Customer')->render(function (Invoice $invoice) {
                return $invoice->customer?->name;
            }),

            TD::make('subject', 'Subject'),

            TD::make('amount', 'Amount')
                ->usingComponent(Currency::class, before: 'CHF', thousands_separator: ' ')
                ->sort(),

            TD::make('state', 'State')
                ->render(
                    fn(Invoice $invoice) => ModalToggle::make($invoice->state)
                        ->modal('setStateModal')
                        ->modalTitle("Set state for invoice $invoice->id")
                        ->method('updateState')
                        ->asyncParameters([
                            'invoice' => $invoice->id,
                        ])
                        ->addClass('rounded-pill text-white')
                        ->addClass(match ($invoice->state) {
                            'Creating' => 'bg-secondary',
                            'Ready' => 'bg-warning',
                            'Sent' => 'bg-primary',
                            'Paid' => 'bg-success',
                        })
                )
                ->sort(),

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
