<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/registration', [AuthController::class, 'register'])->name('registration');
Route::post('/auth', [AuthController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}/products', [CategoryController::class, 'products']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::middleware('auth:sanctum')->post('/products/{product_id}/buy', [OrderController::class, 'buy']);

Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'index']);

Route::post('/payment-webhook', [PaymentWebhookController::class, 'handlePaymentWebhook'])->name('payment.webhook');

Route::post('/test-payments', function (Request $request) {
    return response()->json([
        'pay_url' => 'http://example.com/pay/' . uniqid(),
        'order_id' => uniqid()
    ]);
});