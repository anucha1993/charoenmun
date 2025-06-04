<?php

use App\Livewire\Dashboards;

use App\Livewire\Orders\OrderForm;

// use App\Livewire\Customers\CustomerEdit;
// use App\Livewire\Customers\CustomerIndex;
// use App\Livewire\Customers\CustomerCreate;

use App\Livewire\Orders\OrderShow;

use App\Livewire\Orders\OrderIndex;
use Illuminate\Support\Facades\Route;
use App\Livewire\Products\ProductIndex;
use App\Livewire\Customers\CustomerEdit;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Customers\CustomerCreate;
use App\Livewire\Orders\OrderDeliveryEdit;
use App\Http\Controllers\RoutingController;
<<<<<<< HEAD
use App\Livewire\Quotations\QuotationIndex;
use App\Livewire\Quotations\QuotationPrint;
use App\Livewire\Quotations\QuotationsForm;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Livewire\Orders\OrderDelivery;
use App\Livewire\Globalsets\GlobalSetManager;

=======
use App\Livewire\Globalsets\GlobalSetManager;
use App\Http\Controllers\customers\CustomerController;
use App\Livewire\Quotations\QuotationsForm;
>>>>>>> 6007f9832596d98b7c0b77242e4c01e44199da09

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

require __DIR__ . '/auth.php';

// ✅ ประกาศให้ชัดก่อน
Route::get('/dashboards', \App\Livewire\Dashboards::class)->middleware('auth')->name('dashboards');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/products', ProductIndex::class)->name('products.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/global-sets', GlobalSetManager::class)->name('global-sets.index');
});


<<<<<<< HEAD

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('quotations')
        ->name('quotations.')
        ->group(function () {
            Route::get('/', QuotationIndex::class)->name('index');
            Route::get('/create', QuotationsForm::class)->name('create');
            Route::get('/{quotation}/edit', QuotationsForm::class)->name('edit');
        });
});

Route::middleware(['auth'])->group(function () {
    // 1) แสดงรายการ Order / ดู Order / แก้ไข Order
    Route::get('/orders',             OrderIndex::class)->name('orders.index');
    Route::get('/orders/{order}',     OrderShow::class)->name('orders.show');
    Route::get('/orders/{order}/edit', OrderForm::class)->name('orders.edit');

    // 2) สร้างรอบจัดส่งใหม่
    Route::get('/orders/{order}/delivery/create', OrderDelivery::class)
         ->name('order-delivery.create');

    // 3) แก้ไขรอบจัดส่ง
    // Route::get('/orders/{order}/delivery/{delivery}/edit', OrderDeliveryEdit::class)
    //      ->name('order-delivery.edit');
});

Route::get('/quotations/{quotation}/print', QuotationPrint::class)->middleware('auth')->name('quotations.print');
=======
// Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
>>>>>>> 6007f9832596d98b7c0b77242e4c01e44199da09

// routes/web.php
Route::get('/customers/edit/{customerId}', CustomerEdit::class)->name('customers.edit');
Route::get('/customers/create', CustomerCreate::class)->name('customers.create');
Route::get('/customers', CustomerIndex::class)->name('customers.index');

//Quotations
Route::get('/quotations/create',QuotationsForm::class)->name('quotations.create');

<<<<<<< HEAD

require __DIR__ . '/auth.php';
=======
>>>>>>> 6007f9832596d98b7c0b77242e4c01e44199da09
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [RoutingController::class, 'index'])->name('root');
    Route::get('/home', fn() => view('index'))->name('home');

    // ---- wildcard พวกนี้ต้องอยู่ “ท้ายสุด” ----
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
