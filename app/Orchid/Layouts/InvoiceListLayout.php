<?php

namespace App\Orchid\Layouts;

use App\Models\Invoice;
use App\View\Components\StateBadge;
use Orchid\Screen\Actions\Link;
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
                return $invoice->amount . ' ' . $invoice->currency;
            }),

            TD::make('state', 'State')->component(StateBadge::class),

            TD::make('Actions')
                ->alignRight()
                ->render(function (Invoice $invoice) {
                    return Link::make('Edit')
                        ->icon('pencil')
                        ->route('platform.invoice.edit', $invoice);
                }),
        ];
    }
}
