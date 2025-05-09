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
use Orchid\Screen\Fields\RadioButtons;
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
        return $this->invoice->exists ? "Edit invoice #{$this->invoice->id}" : 'Creating a new invoice';
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

            Button::make('Duplicate')
                ->icon('copy')
                ->method('duplicate')
                ->canSee($this->invoice->exists),

            Button::make('Update')
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
            Layout::modal('editInvoiceItemModal', [
                Layout::rows([
                    Input::make('invoiceItem.description')->title('Description')->required(),
                    Input::make('invoiceItem.quantity')->title('Quantity')->type('number')->value(1),
                    Input::make('invoiceItem.unit_price')->title('Unit price'),
                ]),
            ])->title('Edit invoice item')->applyButton('Save')->deferred('loadInvoiceItemModal'),

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

                    Group::make([
                        Select::make('invoice.state')
                            ->title('State')
                            ->options([
                                'Creating' => 'Creating',
                                'Ready' => 'Ready',
                                'Sent' => 'Sent',
                                'Paid' => 'Paid'
                            ]),

                        RadioButtons::make('invoice.layout')
                            ->title('Layout')
                            ->options([
                                'sm' => 'small',
                                'md' => 'medium',
                            ]),
                    ]),
                ]),

                'Invoice items' => [

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
                                        Button::make('Up')->icon('arrow-up')->method('moveInvoiceItem', ['itemId' => $ii->id, 'swap' => $loop->index - 1])->canSee(!$loop->first),
                                        Button::make('Down')->icon('arrow-down')->method('moveInvoiceItem', ['itemId' => $ii->id, 'swap' => $loop->index + 1])->canSee(!$loop->last),
                                        ModalToggle::make('Edit')->icon('pencil')
                                            ->modal('editInvoiceItemModal', ['invoiceItem' => $ii->id])
                                            ->method('saveInvoiceItem'),
                                        Button::make('Remove')->icon('trash')->method('removeInvoiceItem', ['itemId' => $ii->id]),
                                    ]);
                            }),
                    ]),

                    Layout::rows([
                        ModalToggle::make('Add item')
                            ->modal('editInvoiceItemModal')
                            ->modalTitle('Add Invoice item')
                            ->method('addInvoiceItem')
                            ->icon('plus'),
                    ])->canSee($this->invoice->exists),
                ],

                'Preview invoice' => Layout::view('invoice.iframe')
                    ->canSee($this->invoice->exists),
            ]),

        ];
    }

    public function loadInvoiceItemModal(InvoiceItem $invoiceItem)
    {
        return [
            'invoiceItem' => $invoiceItem
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
            ...$request->invoiceItem,
            'order' => ($last?->order ?? -1) + 1
        ]);
    }

    public function saveInvoiceItem(Request $request, InvoiceItem $invoiceItem)
    {
        $invoiceItem->fill($request->invoiceItem)->save();
    }

    public function removeInvoiceItem(Request $request)
    {
        $ii = InvoiceItem::find($request->itemId);
        $ii->delete();
    }

    public function moveInvoiceItem(Request $request)
    {
        $items = $this->invoice->orderedInvoiceItems;
        $item = $items->find($request->itemId);
        $swapItem = $items->get($request->swap);
        [$order, $swapOrder] = [$item->order, $swapItem->order];
        $item->order = $swapOrder;
        $swapItem->order = $order;
        $item->save();
        $swapItem->save();
    }

    public function duplicate(Request $request)
    {
        $newInvoice = $this->invoice->replicate()->fill([
            'date' => now(),
            'state' => 'Creating',
        ]);
        $newInvoice->save();
        $newInvoice->invoiceItems()->createMany(
            $this->invoice->invoiceItems()->select('description', 'quantity', 'order', 'unit_price')->get()->toArray()
        );
        return redirect()->route('platform.invoice.edit', $newInvoice);
    }
}
