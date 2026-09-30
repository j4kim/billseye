<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private Invoice $invoice
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->invoice->account->email, $this->invoice->account->name),
            subject: $this->invoice->email_subject,
            using: fn(Email $m) => $m->text($this->htmlToText($this->invoice->email_content))
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->wrapHtml($this->invoice->email_content),
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromStorage($this->invoice->pdf_path)
                ->as($this->invoice->pdfFilename)
                ->withMime('application/pdf'),
        ];
    }

    private function wrapHtml(string $content): string
    {
        // Retire les paragraphes vides de Quill et ajoute une marge aux autres
        $content = preg_replace('#<p>(\s|<br\s*/?>)*</p>#i', '', $content);
        $content = str_replace('<p>', '<p style="margin: 0 0 12px 0;">', $content);

        return '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>' . e($this->invoice->email_subject ?? 'Facture') . '</title></head>'
            . '<body style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.5; color: #222;">'
            . $content
            . '</body></html>';
    }

    private function htmlToText(string $html): string
    {
        // Les liens : "texte (url)" pour ne pas perdre l'URL
        $html = preg_replace(
            '#<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)</a>#is',
            '$2 ($1)',
            $html
        );

        // Sauts de ligne pour les blocs et les <br>
        $html = preg_replace('#</p>|<br\s*/?>|</li>|</h[1-6]>#i', "\n", $html);

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Réduit les lignes vides multiples
        return trim(preg_replace("/\n{3,}/", "\n\n", $text));
    }
}
