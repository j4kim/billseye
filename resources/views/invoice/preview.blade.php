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
.invoice-preview .page-wrapper .page .content {
    padding: 1cm 1cm 0.2cm;
    height: 19cm;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
}
.invoice-preview .page-wrapper .page .content .block.right {
    left: 50%;
    width: 50%;
    position: relative;
}
.invoice-preview .page-wrapper .page .content .footer p {
    margin-bottom: 0.5em;
}
</style>

<div>Invoice preview</div>
<div class="invoice-preview">
    <div class="page-wrapper">
        <div class="page">
            <div class="content">
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
                    @if($invoice->account->phone)
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
                    <h4>Facture</h4>
                    <div>{{ $invoice->subject }}</div>
                    <div>
                        Date: {{ $invoice->date->format('d.m.Y') }}
                    </div>
                </div>
                @if($invoice->invoiceItems->count())
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
                @if($invoice->footer)
                    <div class="block footer">
                        {!! $invoice->footer !!}
                    </div>
                @endif
            </div>
            <div class="qr-bill">
                {!! $invoice->generateQrBill() !!}
            </div>
        </div>
    </div>
</div>