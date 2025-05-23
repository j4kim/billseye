<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Invoice;
use App\Orchid\Screens\Invoice\InvoiceDataScreen;
use App\Orchid\Screens\Invoice\InvoiceItemsScreen;
use App\Orchid\Screens\Invoice\InvoicePdfScreen;
use App\Orchid\Screens\Invoice\InvoicePreviewScreen;
use App\Orchid\Screens\Invoice\InvoiceListScreen;
use App\Orchid\Screens\HomeScreen;
use App\Orchid\Screens\Invoice\InvoiceEmailScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/home', HomeScreen::class)
    ->name('platform.home');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn(Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn(Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));


Route::screen('invoices', InvoiceListScreen::class)
    ->name('platform.invoice.list')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->parent('platform.index')->push('Invoices', route('platform.invoice.list'));
    });

Route::screen('invoice/new', InvoiceDataScreen::class)
    ->name('platform.invoice.new')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->parent('platform.invoice.list')->push("New Invoice");
    });

Route::screen('invoice/{invoice}/edit/data', InvoiceDataScreen::class)
    ->name('platform.invoice.edit.data')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->parent('platform.invoice.list')->push("Edit Invoice");
    });

Route::screen('invoice/{invoice}/edit/items', InvoiceItemsScreen::class)
    ->name('platform.invoice.edit.items')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->parent('platform.invoice.list')->push("Edit Invoice");
    });

Route::screen('invoice/{invoice}/edit/preview', InvoicePreviewScreen::class)
    ->name('platform.invoice.edit.preview')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->parent('platform.invoice.list')->push("Edit Invoice");
    });

Route::screen('invoice/{invoice}/edit/pdf', InvoicePdfScreen::class)
    ->name('platform.invoice.edit.pdf')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->parent('platform.invoice.list')->push("Edit Invoice");
    });

Route::screen('invoice/{invoice}/edit/email', InvoiceEmailScreen::class)
    ->name('platform.invoice.edit.email')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->parent('platform.invoice.list')->push("Edit Invoice");
    });

Route::get('invoice/{invoice}/preview', function (Invoice $invoice) {
    return view('invoice.preview', ['invoice' => $invoice]);
})->name('platform.invoice.preview');

Route::get('invoice/{invoice}/pdf', function (Invoice $invoice) {
    if ($invoice->pdf_path) {
        $pdf = Storage::get($invoice->pdf_path);
        return response($pdf)->header('Content-Type', 'application/pdf');
    }
    return "No PDF generated for this invoice";
})->name('platform.invoice.pdf');

Route::get('invoice/{invoice}/pdf/download', function (Invoice $invoice) {
    return Storage::download($invoice->pdf_path, $invoice->pdfFilename);
})->name('platform.invoice.pdf.download');

Route::get('accounts/{account}/make-selected', function (Account $account) {
    $account->makeSelected();
    Account::storeInSession();
    return redirect()->back();
})->name('platform.account.make-selected');
