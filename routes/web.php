<?php

use App\Livewire\Dashboards;
use Illuminate\Support\Facades\Route;
use App\Livewire\Products\ProductIndex;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Customers\CustomerCreate;
use App\Http\Controllers\RoutingController;
use App\Livewire\Globalsets\GlobalSetManager;

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
Route::middleware(['auth'])->group(function () {
    Route::get('/customers', CustomerIndex::class)->name('customers.index');
    Route::get('/customer/create', CustomerCreate::class)->name('customers.create');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [RoutingController::class, 'index'])->name('root');
    Route::get('/home', fn() => view('index'))->name('home');

    // ---- wildcard พวกนี้ต้องอยู่ “ท้ายสุด” ----
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
