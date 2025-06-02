<?php

use App\Livewire\Dashboards;

use Illuminate\Support\Facades\Route;

// use App\Livewire\Customers\CustomerEdit;
// use App\Livewire\Customers\CustomerIndex;
// use App\Livewire\Customers\CustomerCreate;

use App\Livewire\Products\ProductIndex;

use App\Livewire\Customers\CustomerEdit;
use Illuminate\Support\Facades\Response;
use App\Livewire\Customers\CustomerIndex;
use App\Models\Quotations\QuotationModel;
use App\Livewire\Customers\CustomerCreate;
use App\Http\Controllers\RoutingController;
use App\Livewire\Quotations\QuotationIndex;
use App\Livewire\Quotations\QuotationPrint;
use App\Livewire\Quotations\QuotationsForm;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Livewire\Globalsets\GlobalSetManager;
use App\Http\Controllers\customers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//QR Code for Quotation
Route::get('/qr/quotation/{id}', function (int $id) {
    $number = QuotationModel::whereKey($id)->value('quote_number') ?? abort(404);

    $svg = QrCode::format('svg')->size(300)->margin(1)->generate($number);

    return Response::make($svg, 200, [
        'Content-Type' => 'image/svg+xml',
        'Cache-Control' => 'public,max-age=604800',
        'ETag' => md5($svg),
    ]);
})
    ->whereNumber('id')
    ->name('qr.quotation');

// ✅ ประกาศให้ชัดก่อน
Route::get('/dashboards', \App\Livewire\Dashboards::class)->middleware('auth')->name('dashboards');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/products', ProductIndex::class)->name('products.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/global-sets', GlobalSetManager::class)->name('global-sets.index');
});
// Route::middleware(['auth'])->group(function () {
//     Route::get('/customers', CustomerIndex::class)->name('customers.index');
//     Route::get('/customers/create', CustomerCreate::class)->name('customers.create');
// Route::get('/customers/{id}/edit', CustomerEdit::class)->name('customers.edit');

// });

// Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
// Route::get('/quotations/{quotation_model}/edit', QuotationsForm::class)
//      ->name('quotations.edit');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('quotations')
        ->name('quotations.')
        ->group(function () {
            Route::get('/', QuotationIndex::class)->name('index');
            Route::get('/create', QuotationsForm::class)->name('create');
            Route::get('/{quotation}/edit', QuotationsForm::class)->name('edit');
        });
});

Route::get('/quotations/{quotation}/print', QuotationPrint::class)->middleware('auth')->name('quotations.print');

// routes/web.php
Route::get('/customers/edit/{customerId}', CustomerEdit::class)->name('customers.edit');
Route::get('/customers/create', CustomerCreate::class)->name('customers.create');
Route::get('/customers', CustomerIndex::class)->name('customers.index');

//Quotations
Route::get('/quotations/create', QuotationsForm::class)->name('quotations.create');

require __DIR__ . '/auth.php';
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [RoutingController::class, 'index'])->name('root');
    Route::get('/home', fn() => view('index'))->name('home');

    // ---- wildcard พวกนี้ต้องอยู่ “ท้ายสุด” ----
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
