<?php

namespace App\Tools;

use Illuminate\Support\Facades\Http;

class PdfService
{
    protected $apiEndpoint;
    protected $authToken;

    public static function createPdf($html)
    {
        $response = Http::withToken(config('services.doppio.auth_token'))
            ->post('https://api.doppio.sh/v1/render/pdf/direct', [
                'page' => [
                    'pdf' => [
                        'format' => 'A4',
                    ],
                    'setContent' => [
                        'html' => base64_encode($html),
                    ],
                ],
            ]);
        if ($response->successful()) {
            return $response->body();
        } else {
            throw new \Exception('Failed to generate PDF: ' . $response->body());
        }
    }
}
