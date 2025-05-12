<?php

namespace App\Orchid\Screens\Invoice;

use App\Orchid\Layouts\InvoiceTabMenu;
use Orchid\Support\Facades\Layout;

class InvoicePreviewScreen extends InvoiceBaseScreen
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
            Layout::view('invoice.iframe')
        ];
    }
}
