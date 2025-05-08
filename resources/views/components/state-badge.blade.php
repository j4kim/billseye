

<span @class([
    'badge',
    'bg-secondary' => $invoice->state === 'Creating',
    'bg-warning' => $invoice->state === 'Ready',
    'bg-primary' => $invoice->state === 'Sent',
    'bg-success' => $invoice->state === 'Paid',
])>{{ $invoice->state }}</span>