<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Facture {{ $invoice->id }} - {{ $invoice->subject }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <style>
        body {
            padding: 0;
            margin: 0;
        }

        .page {
            background-color: white;
            width: 21cm;
            height: 29.6cm;
            position: relative;
        }

        .page>.qr-bill {
            position: absolute;
            bottom: 0;
        }

        .page .content {
            padding: 1cm 1cm 0.2cm;
            height: 19cm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .page .content .block.right {
            left: 11cm;
            width: 8cm;
            position: relative;
        }

        .page .content .footer p {
            margin-bottom: 0.4em;
        }

        .page.sm {
            font-size: 9pt;
        }

        .page.sm .table>thead>tr>th {
            padding: 0.5em;
        }

        .page.sm .table tbody tr td {
            padding: 0.5em;
        }

        .page.sm .content .footer p {
            margin-bottom: 0.2em;
        }
    </style>
</head>

<body>
    <div class="page {{ $invoice->layout }}">
        <div class="content">
            <div class="block">
                <strong>{{ $invoice->account->name }}</strong><br>
                {{ $invoice->account->street }} {{ $invoice->account->building_number }}<br>
                {{ $invoice->account->postal_code }} {{ $invoice->account->city }}<br>
                @if ($invoice->account->country != 'CH')
                    {{ $invoice->account->country }}<br>
                @endif
                @if ($invoice->account->email)
                    {{ $invoice->account->email }}<br>
                @endif
                @if ($invoice->account->phone)
                    {{ $invoice->account->phone }}<br>
                @endif
            </div>
            <div class="block right">
                @if ($invoice->customer)
                    <strong>{{ $invoice->customer->name }}</strong><br>
                    {{ $invoice->customer->street }} {{ $invoice->customer->building_number }}<br>
                    {{ $invoice->customer->postal_code }} {{ $invoice->customer->city }}<br>
                    @if ($invoice->account->country != 'CH')
                        {{ $invoice->account->country }}<br>
                    @endif
                @endif
            </div>
            <div class="block">
                <h4>Facture</h4>
                <div>{{ $invoice->subject }}</div>
                <div>
                    Date de la facture : {{ $invoice->date->format('d.m.Y') }}
                </div>
            </div>
            @if ($invoice->invoiceItems->count())
                <div class="block">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Quantité</th>
                                <th>Taux</th>
                                <th style="text-align:right">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->invoiceItems as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->unit_price }}</td>
                                    <td style="text-align:right">{{ $item->formattedTotal }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <h5 class="block" style="text-align: right; font-weight: 700">
                <span style="padding-right: 2cm">Total</span>
                {{ $invoice->formattedAmount }}
            </h5>
            @if ($invoice->footer)
                <div class="block footer">
                    {!! $invoice->footer !!}
                </div>
            @endif
        </div>
        <div class="qr-bill">
            {!! $invoice->generateQrBill() !!}
        </div>
    </div>
</body>

</html>
