<?php

namespace App\Orchid\Screens\Invoice;

use App\Orchid\Layouts\InvoiceTabMenu;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Layout;

class InvoiceEmailScreen extends InvoiceBaseScreen
{
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

            ...parent::commandBar(),
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
            InvoiceTabMenu::class,

            Layout::rows([
                Input::make('invoice.email_subject')
                    ->title('Email Subject')
                    ->value("Facture - " . $this->invoice->subject),

                Quill::make('invoice.email_content')
                    ->title('Email content'),

                Group::make([
                    Input::make('from')
                        ->title('From')
                        ->value($this->invoice->account->email)
                        ->disabled(),
                    Input::make('to')
                        ->title('To')
                        ->value($this->invoice->customer->email)
                        ->disabled(),
                ]),

                Group::make([
                    Input::make('invoice.email_cc')->title('cc'),
                    Input::make('invoice.email_bcc')->title('bcc'),
                ]),
            ]),
        ];
    }
}
