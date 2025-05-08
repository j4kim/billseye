<?php

namespace App\Orchid\Screens;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class InvoiceEditScreen extends Screen
{

    public $invoice;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Invoice $invoice): iterable
    {
        return [
            'invoice' => $invoice,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->invoice->exists ? 'Edit invoice' : 'Creating a new invoice';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Create Invoice')
                ->icon('check-circle')
                ->method('createOrUpdate')
                ->canSee(!$this->invoice->exists),

            Button::make('Update Invoice')
                ->icon('check-circle')
                ->method('createOrUpdate')
                ->canSee($this->invoice->exists),

            Button::make('Remove')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->invoice->exists),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::tabs([
                'Invoice information' => Layout::rows([

                    Group::make([
                        Relation::make('invoice.account_id')
                            ->title('Creditor')
                            ->fromModel(Account::class, 'name')
                            ->required(),

                        Relation::make('invoice.customer_id')
                            ->title('Debtor')
                            ->fromModel(Customer::class, 'name'),
                    ]),

                    Group::make([
                        DateTimer::make('invoice.date')
                            ->title('Date')
                            ->format('Y-m-d')
                            ->required(),

                        Input::make('invoice.subject')
                            ->title('Subject')
                            ->help('What is the invoice about?')
                            ->required(),
                    ]),

                    Group::make([
                        Select::make('invoice.currency')
                            ->title('Curency')
                            ->options(['CHF' => 'CHF', 'EUR' => 'EUR']),

                        Input::make('invoice.amount')
                            ->title('Amount')
                            ->type('number')
                            ->help('The total amount of the invoice')
                            ->disabled($this->invoice ? $this->invoice->invoiceItems()->exists() : false),

                        Input::make('invoice.discount')
                            ->title('Discount')
                            ->type('number'),
                    ]),

                    Quill::make('invoice.footer')
                        ->title('Footer'),

                    Select::make('invoice.state')
                        ->title('State')
                        ->options([
                            'Creating' => 'Creating',
                            'Ready' => 'Ready',
                            'Sent' => 'Sent',
                            'Paid' => 'Paid'
                        ]),
                ]),

                'Invoice items' => [
                    Layout::modal('addInvoiceItemModal', [
                        Layout::rows([
                            Input::make('description')->title('Description')->required(),
                            Input::make('quantity')->title('Quantity')->type('number')->value(1),
                            Input::make('unit_price')->title('Unit price'),
                        ]),
                    ])->title('Add invoice item')->applyButton('Save'),

                    Layout::rows([
                        ModalToggle::make('Add item')
                            ->modal('addInvoiceItemModal')
                            ->method('addInvoiceItem')
                            ->icon('plus'),
                    ]),

                    Layout::table('invoice.orderedInvoiceItems', [
                        TD::make('description'),
                        TD::make('quantity'),
                        TD::make('unit_price', 'Unit price'),
                        TD::make('total'),
                        TD::make('Actions')
                            ->alignRight()
                            ->render(function (InvoiceItem $ii, $loop) {
                                return DropDown::make()
                                    ->icon('bs.three-dots-vertical')
                                    ->list([
                                        Button::make('Up')->icon('arrow-up')->method('moveInvoiceItem', ['itemId' => $ii->id, 'direction' => 'up'])->canSee(!$loop->first),
                                        Button::make('Down')->icon('arrow-down')->method('moveInvoiceItem', ['itemId' => $ii->id, 'direction' => 'down'])->canSee(!$loop->last),
                                        Link::make('Edit')->icon('pencil')->route('platform.invoice-item', [$ii->invoice_id, $ii->id]),
                                    ]);
                            }),
                    ])
                ],

                'Preview invoice' => Layout::view('invoice.preview')
                    ->canSee($this->invoice->exists),
            ]),

        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Request $request)
    {
        $this->invoice->fill($request->get('invoice'))->save();

        Alert::info($this->invoice->wasRecentlyCreated ? 'You have successfully created an invoice.' : 'Invoice updated');

        return redirect()->route('platform.invoice.edit', $this->invoice);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove()
    {
        $this->invoice->delete();

        Alert::info('You have successfully deleted the invoice.');

        return redirect()->route('platform.invoice.list');
    }

    public function addInvoiceItem(Request $request): void
    {
        $last = $this->invoice->invoiceItems()->orderBy('order', 'desc')->first();
        $this->invoice->invoiceItems()->create([
            ...$request->all(),
            'order' => ($last?->order ?? 0) + 128
        ]);
    }

    public function moveInvoiceItem(Request $request)
    {
        $items = $this->invoice->invoiceItems;
        $item = $items->find($request->itemId);

        $op = $request->direction === 'up' ? '<=' : '>=';
        $dir = $request->direction === 'up' ? 'desc' : 'asc';
        $inc = $request->direction === 'up' ? -128 : 128;

        $others = $items->where('order', $op, $item->order)
            ->where('id', '!=', $item->id)
            ->sortBy(['order', $dir])
            ->values();

        if ($others->count() === 0) {
            return;
        } else if ($others->count() === 1) {
            $neworder = $others->get(0)->order + $inc;
        } else {
            $neworder = ($others->get(0)->order + $others->get(1)->order) / 2;
        }

        $item->order = $neworder;
        $item->save();
    }
}
