<style>
.invoice-preview {
    overflow: auto;
}
.invoice-preview .page-wrapper {
    width: 21cm;
    height: 29.7cm;
    margin: 10px;
    box-shadow: var(--bs-box-shadow-sm);
}
.invoice-preview .page-wrapper .page {
    background-color: white;
    width: 21cm;
    height: 29.7cm;
    position: relative;
}
.invoice-preview .page-wrapper .page > .qr-bill {
    position: absolute;
    bottom: 0;
}
.invoice-preview .page-wrapper .page .block {
    padding: 0.6cm 1.2cm;
}
.invoice-preview .page-wrapper .page .block.right {
    left: 50%;
    width: 50%;
    position: relative;
}
</style>

<div>Invoice preview</div>
<div class="invoice-preview">
    <div class="page-wrapper">
        <div class="page">
            <div class="block">
                <strong>{{ $invoice->account->name }}</strong><br>
                {{ $invoice->account->street }} {{ $invoice->account->building_number }}<br>
                {{ $invoice->account->postal_code }} {{ $invoice->account->city }}<br>
                @if($invoice->account->country != 'CH')
                    {{ $invoice->account->country }}<br>
                @endif
                @if($invoice->account->email)
                    {{ $invoice->account->email }}<br>
                @endif
                @if($invoice->account->phone != 'CH')
                    {{ $invoice->account->phone }}<br>
                @endif
            </div>
            <div class="block right">
                @if ($invoice->customer)
                    <strong>{{ $invoice->customer->name }}</strong><br>
                    {{ $invoice->customer->street }} {{ $invoice->customer->building_number }}<br>
                    {{ $invoice->customer->postal_code }} {{ $invoice->customer->city }}<br>
                    @if($invoice->account->country != 'CH')
                        {{ $invoice->account->country }}<br>
                    @endif
                @endif
            </div>
            <div class="block">
                <h3>Facture</h3>
                <h5>{{ $invoice->subject }}</h5>
                <div>
                    Date: {{ $invoice->date->format('d.m.Y') }}
                </div>
            </div>
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
            <h5 class="block" style="text-align: right; font-weight: 700">
                <span style="padding-right: 2cm">Total</span>
                {{ $invoice->formattedAmount }}
            </h5>
            <div class="qr-bill">
                {!! $invoice->generateQrBill() !!}
            </div>
        </div>
    </div>
</div>