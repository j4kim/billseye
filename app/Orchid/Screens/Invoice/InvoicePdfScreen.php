<?php

namespace App\Orchid\Screens\Invoice;

use App\Orchid\Layouts\InvoiceTabMenu;
use App\Tools\PdfService;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;

class InvoicePdfScreen extends InvoiceBaseScreen
{
    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Download PDF')
                ->icon('download')
                ->target('_blank')
                ->route('platform.invoice.pdf.download', $this->invoice->id)
                ->canSee(!!$this->invoice->pdf_path),

            Button::make('Generate PDF')
                ->icon('magic')
                ->method('generatePDF'),

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

            Layout::view('invoice.pdf-iframe')
                ->canSee(!!$this->invoice->pdf_path),
        ];
    }

    public function generatePDF()
    {
        $html = view('invoice.preview', ['invoice' => $this->invoice])->render();
        $pdf = PdfService::createPdf($html);
        $path = "{$this->invoice->id}.pdf";
        if (Storage::put($path, $pdf)) {
            $this->invoice->pdf_path = $path;
            $this->invoice->pdf_generated_at = now();
            $this->invoice->save();
        } else {
            abort(500, "Unable to store PDF file");
        }
    }
}
