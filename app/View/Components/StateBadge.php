<?php

namespace App\View\Components;

use App\Models\Invoice;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StateBadge extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Invoice $invoice
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.state-badge');
    }
}
