<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DealsController;
use App\Http\Controllers\PaydaysController;
use App\Http\Controllers\RepaymentsController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\PagesController;
use App\Models\Client;
use Illuminate\Support\Facades\Route;
use App\Models\Deal;
use App\Models\Repayment;


// Auth
Route::get('/register', [RegisteredUserController::class, 'create']);
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', [SessionController::class, 'create']);
Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);

// DASH
Route::view('/', 'auth.login')->name('login');

Route::get('/dashboard',[ PagesController::class, 'index'])->middleware('auth');

// REPORTS
Route::view('/reports', 'reports');

// RESOURCES

// CLIENTS
Route::get('/clients',[ ClientsController::class, 'index'])->middleware('auth');
Route::get('/clients/create', [ClientsController::class, 'create'])->middleware('auth');
Route::post('/clients', [ClientsController::class, 'store'])->middleware('auth')->can('admin');
Route::get('/clients/{client}', [ClientsController::class, 'show'])->middleware('auth');
Route::get('/clients/{client}/edit', [ClientsController::class, 'edit'])->middleware('auth')->can('admin');
Route::patch('/clients/{client}', [ClientsController::class, 'update'])->middleware('auth')->can('admin');
Route::delete('/clients/{client}/delpic/{picId}', [ClientsController::class, 'delpic'])->middleware('auth')->can('admin');

// DEALS
Route::get('/deals',[ DealsController::class, 'index'])->middleware('auth');
Route::get('/deals/create', [DealsController::class, 'create'])->middleware('auth');
Route::post('/deals', [DealsController::class, 'store'])->middleware('auth')->can('admin');
Route::get('/deals/{deal}/pdf', [DealsController::class, 'pdf'])->middleware('auth');
Route::get('/deals/{deal}', [DealsController::class, 'show'])->middleware('auth');
Route::get('/deals/{deal}/edit', [DealsController::class, 'edit'])->middleware('auth')->can('admin');
Route::patch('/deals/{deal}', [DealsController::class, 'update'])->middleware('auth')->can('admin');

// REPAYMENTS
Route::get('/repayments',[ RepaymentsController::class, 'index'])->middleware('auth');
Route::get('/repayments/create', [RepaymentsController::class, 'create'])->middleware('auth');
Route::post('/repayments', [RepaymentsController::class, 'store'])->middleware('auth')->can('admin');
Route::get('/repayments/{repayment}', [RepaymentsController::class, 'show'])->middleware('auth');
Route::get('/repayments/{repayment}/edit', [RepaymentsController::class, 'edit'])->middleware('auth')->can('admin');
Route::patch('/repayments/{repayment}', [RepaymentsController::class, 'update'])->middleware('auth')->can('admin');


// PAYDAYS
Route::get('/paydays',[ PaydaysController::class, 'index'])->middleware('auth');
Route::get('/paydays/create', [PaydaysController::class, 'create'])->middleware('auth');
Route::post('/paydays', [PaydaysController::class, 'store'])->middleware('auth')->can('admin');
Route::get('/paydays/{payday}', [PaydaysController::class, 'show'])->middleware('auth');
Route::get('/paydays/{payday}/edit', [PaydaysController::class, 'edit'])->middleware('auth')->can('admin');
Route::patch('/paydays/{payday}', [PaydaysController::class, 'update'])->middleware('auth')->can('admin');


