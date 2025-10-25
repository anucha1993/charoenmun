<?php

use App\Livewire\Dashboards;
use App\Livewire\Orders\OrderForm;
use Illuminate\Support\Facades\Log;

// use App\Livewire\Customers\CustomerEdit;
// use App\Livewire\Customers\CustomerIndex;
// use App\Livewire\Customers\CustomerCreate;

use App\Livewire\Orders\OrderShow;

use App\Livewire\Orders\OrderIndex;
use App\Livewire\Orders\ScanInvoice;
use Illuminate\Support\Facades\Route;
use App\Livewire\Orders\OrderDelivery;
use App\Livewire\Products\ProductIndex;
use App\Livewire\Customers\CustomerEdit;
use App\Livewire\Orders\ConfirmPayments;
use Illuminate\Support\Facades\Response;
use App\Livewire\Customers\CustomerIndex;
use App\Models\Quotations\QuotationModel;
use App\Livewire\Customers\CustomerCreate;
use App\Livewire\Orders\OrderDeliveryEdit;
use App\Livewire\Orders\PaymentSlipUpload;
use App\Livewire\Orders\OrderPaymentForm;

use App\Models\Orders\OrderDeliverysModel;
use App\Http\Controllers\RoutingController;
use App\Livewire\Orders\OrderDeliveryPrint;
use App\Livewire\Quotations\QuotationIndex;
use App\Livewire\Quotations\QuotationPrint;
use App\Livewire\Quotations\QuotationsForm;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Livewire\Globalsets\GlobalSetManager;
use App\Livewire\Users\UserIndex;

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

    Route::get('/qr/deliveries/{id}', function (int $id) {
    $number = OrderDeliverysModel::whereKey($id)->value('order_delivery_number') ?? abort(404);

    $svg = QrCode::format('svg')->size(300)->margin(1)->generate($number);

    return Response::make($svg, 200, [
        'Content-Type' => 'image/svg+xml',
        'Cache-Control' => 'public,max-age=604800',
        'ETag' => md5($svg),
    ]);
})
    ->whereNumber('id')
    ->name('qr.deliveries');

// ✅ ประกาศให้ชัดก่อน
Route::get('/dashboards', \App\Livewire\Dashboards::class)->middleware('auth')->name('dashboards');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/products', ProductIndex::class)->name('products.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/global-sets', GlobalSetManager::class)->name('global-sets.index');
    
    // User Management (Admin Only)
    Route::group(['middleware' => 'admin'], function () {
        Route::get('/users', \App\Livewire\Users\UserIndex::class)->name('users.index');
        Route::get('/users/create', \App\Livewire\Users\UserForm::class)->name('users.create');
        Route::get('/users/{user}/edit', \App\Livewire\Users\UserForm::class)->name('users.edit');
    });
});

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
    Route::get('/orders', OrderIndex::class)->name('orders.index');
    Route::get('/orders/{order}', OrderShow::class)->name('orders.show');

    Route::get('/orders/{order}/show', OrderShow::class)->name('order.show');
    
    //deluvery 
    Route::get('orders/{order}/deliveries/create', OrderDelivery::class)->name('deliveries.create')->whereNumber('order');
    Route::get('orders/{order}/deliveries/{delivery}/edit', OrderDelivery::class)->name('deliveries.edit')->whereNumber('order')->whereNumber('delivery');
    Route::get('deliveries/{delivery}/print', OrderDeliveryPrint::class)->name('deliveries.printer');
    Route::get('deliveries/{delivery}/pdf', [App\Http\Controllers\DeliveryPdfController::class, 'generatePdf'])->name('deliveries.pdf');
    
    // Delivery Calendar
    Route::get('/delivery-calendar', App\Livewire\Deliveries\DeliveryCalendar::class)->name('deliveries.calendar');

     //payment
     Route::get('/confirm-payments', ConfirmPayments::class)->name('payments.confirm');

     //SCANS QR CODE
     Route::get('/scan-invoice', ScanInvoice::class)->name('scan.invoice');

     Route::get('/orders/{order}/payment', OrderPaymentForm::class)->name('orders.payment.livewire');
});

Route::get('/quotations/{quotation}/print', QuotationPrint::class)->middleware('auth')->name('quotations.print');

// routes/web.php
Route::get('/customers/edit/{customerId}', CustomerEdit::class)->name('customers.edit');
Route::get('/customers/create', CustomerCreate::class)->name('customers.create');
Route::get('/customers', CustomerIndex::class)->name('customers.index');

//Quotations
Route::middleware(['auth'])->group(function () {
Route::get('/quotations/create', QuotationsForm::class)->name('quotations.create');
Route::get('/index', QuotationIndex::class)->name('quotations.index');
Route::get('/home', QuotationIndex::class)->name('quotations.home');
});

require __DIR__ . '/auth.php';
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [RoutingController::class, 'index'])->name('root');
    // Route::get('/home', fn() => view('index'))->name('home');

    // ---- wildcard พวกนี้ต้องอยู่ “ท้ายสุด” ----
//     Route::get('/{first}', [RoutingController::class, 'root'])
//       ->where('first', '^(?!customers$).+');
//       Route::get('/{first}/{second}', [RoutingController::class, 'secondLevel'])
//       ->where('first', '^(?!customers$).+');

// Route::get('/{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])
//       ->where('first', '^(?!customers$).+');
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});

// ทดสอบ truck icons
Route::get('/test-truck-icons', function () {
    return view('test-truck-icons');
})->name('test.truck.icons');

// Demo truck icons แบบสวยงาม
Route::get('/demo-truck-icons', function () {
    return view('truck-icons-demo');
})->name('demo.truck.icons');
