<div>
    <iframe src="{{ route('platform.invoice.pdf', $invoice) }}"
        style="width: 100%; height: 600px; display:block"></iframe>
</div>
<p>
    <a href="{{ route('platform.invoice.pdf', $invoice) }}" target="_blank">Ouvrir dans un nouvel onglet</a>
</p>
