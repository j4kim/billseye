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
</style>

<div>Invoice preview</div>
<div class="invoice-preview">
    <div class="page-wrapper">
        <div class="page">
            {{ $invoice->subject }}
            <div class="qr-bill">
                {!! $invoice->generateQrBill() !!}
            </div>
        </div>
    </div>
</div>