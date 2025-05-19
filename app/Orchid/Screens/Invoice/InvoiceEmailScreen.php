<?php

namespace App\Orchid\Screens\Invoice;

use App\Mail\InvoiceMail;
use App\Orchid\Layouts\InvoiceTabMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Alert;
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
            Button::make('Send')
                ->icon('send')
                ->method('send'),

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

    public function send(Request $request)
    {
        $this->invoice->fill($request->get('invoice'));

        config([
            'mail.mailers.smtp' => array_merge(
                config('mail.mailers.smtp'),
                ['username' => $this->invoice->account->email],
                $smtp_config
            )
        ]);

        Mail::to($this->invoice->customer->email)
            ->cc($this->invoice->email_cc)
            ->bcc($this->invoice->email_bcc)
            ->send(new InvoiceMail($this->invoice));

        Alert::info('Email sent');

        $this->invoice->email_sent_at = now();
        $this->invoice->state = 'Sent';
        $this->invoice->save();
    }
}
