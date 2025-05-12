<?php

namespace App\Orchid\Screens\Invoice;

use App\Models\InvoiceItem;
use App\Orchid\Layouts\InvoiceTabMenu;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class InvoiceItemsScreen extends InvoiceBaseScreen
{
    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add item')
                ->modal('editInvoiceItemModal')
                ->modalTitle('Add Invoice item')
                ->method('addInvoiceItem')
                ->icon('plus'),

            ...parent::commandBar(),
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
            InvoiceTabMenu::class,

            Layout::table('invoice.orderedInvoiceItems', [
                TD::make('description'),
                TD::make('quantity'),
                TD::make('unit_price', 'Unit price'),
                TD::make('total'),
                TD::make('Actions')
                    ->alignRight()
                    ->render(function (InvoiceItem $ii, $loop) {
                        return DropDown::make()
                            ->icon('three-dots-vertical')
                            ->list([
                                Button::make('Up')
                                    ->icon('arrow-up')
                                    ->method('moveInvoiceItem', ['itemId' => $ii->id, 'swap' => $loop->index - 1])
                                    ->canSee(!$loop->first),

                                Button::make('Down')
                                    ->icon('arrow-down')
                                    ->method('moveInvoiceItem', ['itemId' => $ii->id, 'swap' => $loop->index + 1])
                                    ->canSee(!$loop->last),

                                ModalToggle::make('Edit')
                                    ->icon('pencil')
                                    ->modal('editInvoiceItemModal', ['invoiceItem' => $ii->id])
                                    ->method('saveInvoiceItem'),

                                Button::make('Remove')
                                    ->icon('trash')
                                    ->method('removeInvoiceItem', ['itemId' => $ii->id]),
                            ]);
                    }),
            ]),

            Layout::modal('editInvoiceItemModal', [
                Layout::rows([
                    Input::make('invoiceItem.description')->title('Description')->required(),
                    Input::make('invoiceItem.quantity')->title('Quantity')->type('number')->value(1),
                    Input::make('invoiceItem.unit_price')->title('Unit price'),
                ]),
            ])->title('Edit invoice item')->applyButton('Save')->deferred('loadInvoiceItemModal'),
        ];
    }

    public function loadInvoiceItemModal(InvoiceItem $invoiceItem)
    {
        return [
            'invoiceItem' => $invoiceItem
        ];
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
}
