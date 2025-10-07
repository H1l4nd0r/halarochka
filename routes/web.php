<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DealsController;
use App\Http\Controllers\ApplicationsController;
use App\Http\Controllers\PaydaysController;
use App\Http\Controllers\RepaymentsController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CashfundController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/register', [RegisteredUserController::class, 'create']);
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);

// DASH
Route::view('/', 'layouts.landing');

Route::get('/dashboard',[ PagesController::class, 'index'])->middleware('auth');

// REPORTS
Route::get('/reports/cashflow', [ ReportsController::class, 'cashFlowReport'])->middleware('auth');
Route::get('/reports/nextpaydays', [ ReportsController::class, 'nextPayDaysReport'])->middleware('auth');
Route::get('/reports', [ ReportsController::class, 'index'])->middleware('auth');

// RESOURCES

// CLIENTS
Route::get('/clients',[ ClientsController::class, 'index'])->middleware('auth');
Route::get('/clients/create', [ClientsController::class, 'create'])->middleware('auth');
Route::post('/clients', [ClientsController::class, 'store'])->middleware('auth')->can('create');
Route::get('/clients/{client}', [ClientsController::class, 'show'])->middleware('auth');
Route::get('/clients/{client}/edit', [ClientsController::class, 'edit'])->middleware('auth')->can('edit','client');
Route::patch('/clients/{client}', [ClientsController::class, 'update'])->middleware('auth')->can('edit','client');
Route::delete('/clients/{client}/delpic/{picId}', [ClientsController::class, 'delpic'])->middleware('auth')->can('edit','client');

// APPLICATIONS
Route::get('/applications',[ ApplicationsController::class, 'index'])->middleware('auth');
Route::get('/applications/create', [ApplicationsController::class, 'create'])->middleware('auth');
Route::post('/applications', [ApplicationsController::class, 'store']);
Route::get('/applications/{deal}', [ApplicationsController::class, 'show'])->middleware('auth');
Route::get('/applications/{deal}/edit', [ApplicationsController::class, 'edit'])->middleware('auth')->can('edit','deal');
Route::patch('/applications/{deal}', [ApplicationsController::class, 'update'])->middleware('auth')->can('edit','deal');
Route::delete('/applications/{deal}/delpic/{picId}', [ApplicationsController::class, 'delpic'])->middleware('auth')->can('edit','deal');

// DEALS
Route::get('/deals',[ DealsController::class, 'index'])->middleware('auth');
Route::get('/deals/create', [DealsController::class, 'create'])->middleware('auth');
Route::post('/deals', [DealsController::class, 'store'])->middleware('auth')->can('create');
Route::get('/deals/{deal}/pdf', [DealsController::class, 'pdf'])->middleware('auth');
Route::get('/deals/{deal}', [DealsController::class, 'show'])->middleware('auth');
Route::get('/deals/{deal}/edit', [DealsController::class, 'edit'])->middleware('auth')->can('edit','deal');
Route::patch('/deals/{deal}', [DealsController::class, 'update'])->middleware('auth')->can('edit','deal');
Route::delete('/deals/{deal}/delpic/{picId}', [DealsController::class, 'delpic'])->middleware('auth')->can('edit','deal');

// REPAYMENTS
Route::get('/repayments',[ RepaymentsController::class, 'index'])->middleware('auth');
Route::get('/repayments/create', [RepaymentsController::class, 'create'])->middleware('auth');
Route::post('/repayments', [RepaymentsController::class, 'store'])->middleware('auth')->can('create');
Route::get('/repayments/{repayment}', [RepaymentsController::class, 'show'])->middleware('auth');
Route::get('/repayments/{repayment}/edit', [RepaymentsController::class, 'edit'])->middleware('auth')->can('edit','repayment');
Route::patch('/repayments/{repayment}', [RepaymentsController::class, 'update'])->middleware('auth')->can('edit','repayment');


// PAYDAYS
Route::get('/paydays',[ PaydaysController::class, 'index'])->middleware('auth');
Route::get('/paydays/create', [PaydaysController::class, 'create'])->middleware('auth');
Route::post('/paydays', [PaydaysController::class, 'store'])->middleware('auth')->can('create');
Route::get('/paydays/{payday}', [PaydaysController::class, 'show'])->middleware('auth');
Route::get('/paydays/{payday}/edit', [PaydaysController::class, 'edit'])->middleware('auth')->can('edit','payday');
Route::patch('/paydays/{payday}', [PaydaysController::class, 'update'])->middleware('auth')->can('edit','payday');


// CASHFUND
Route::get('/cash',[ CashfundController::class, 'index'])->middleware('auth');
Route::get('/cash/create', [CashfundController::class, 'create'])->middleware('auth');
Route::post('/cash', [CashfundController::class, 'store'])->middleware('auth')->can('create');
Route::get('/cash/{cash}', [CashfundController::class, 'show'])->middleware('auth');
Route::get('/cash/{cash}/edit', [CashfundController::class, 'edit'])->middleware('auth')->can('edit','cash');
Route::patch('/cash/{cash}', [CashfundController::class, 'update'])->middleware('auth')->can('edit','cash');