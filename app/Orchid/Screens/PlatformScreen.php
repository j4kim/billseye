<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Invoice;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'metrics' => [
                'invoices' => ['value' => Invoice::count()],
                'Creating' => ['value' => Invoice::where('state', 'Creating')->count()],
                'Ready' => ['value' => Invoice::where('state', 'Ready')->count()],
                'Sent' => ['value' => Invoice::where('state', 'Sent')->count()],
                'Paid' => ['value' => Invoice::where('state', 'Paid')->count()],
            ],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Home';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Total Invoices' => 'metrics.invoices',
                'Creating' => 'metrics.Creating',
                'Ready' => 'metrics.Ready',
                'Sent' => 'metrics.Sent',
                'Paid' => 'metrics.Paid',
            ]),
        ];
    }
}
