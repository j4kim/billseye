<?php

namespace App\Orchid\Screens\Invoice;

use App\Orchid\Layouts\InvoiceTabMenu;

class InvoiceEmailScreen extends InvoiceBaseScreen
{
    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            InvoiceTabMenu::class,
        ];
    }
}
