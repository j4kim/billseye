<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Layouts\TabMenu;

class InvoiceTabMenu extends TabMenu
{
    /**
     * Get the menu elements to be displayed.
     *
     * @return Menu[]
     */
    protected function navigations(): iterable
    {
        $invoice = request()->invoice;

        if (!$invoice) {
            return [
                Menu::make('Invoice information')->route('platform.invoice.new'),
            ];
        }

        $params = ['invoice' => $invoice];

        return [
            Menu::make('Invoice information')
                ->route('platform.invoice.edit.data', $params),

            Menu::make('Invoice items')
                ->route('platform.invoice.edit.items', $params),

            Menu::make('Preview')
                ->route('platform.invoice.edit.preview', $params),

            Menu::make('PDF')
                ->route('platform.invoice.edit.pdf', $params),
        ];
    }
}
