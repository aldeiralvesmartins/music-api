<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{Admin\LayoutSectionController,
    AuthController,
    ProductController,
    CategoryController,
    OrderController,
    PaymentController,
    CartController,
    AddressController,
    UserController,
    ShippingController,
    UserIntegrationController
};
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AsaasController;

Route::middleware('handler.exception')->group(function () {
// Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Product routes (public)
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::get('categories/{category}/products', [ProductController::class, 'getByCategory']);

// Category routes (public)
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

// Package tracking (public)
    Route::post('/rastreio', [\App\Http\Controllers\RastreamentoController::class, 'rastrear'])->name('rastreio.consultar');
    Route::prefix('layout')->group(function () {
        Route::get('/', [LayoutSectionController::class, 'index']);

    });
// Public webhook for Asaas callbacks
    Route::post('/asaas/webhook', [AsaasController::class, 'webhook']);
// Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Shipping calculation (requires authentication to get user address)
        Route::post('/frete/calcular', [ShippingController::class, 'calcularFrete'])->name('frete.calcular');
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/me', [AuthController::class, 'user']); // Alias for /user to match frontend
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::put('/change-password', [UserController::class, 'changePassword']);

        Route::post('/upload-image', function (\Illuminate\Http\Request $request) {
            $urls = [];

            foreach ($request->file('images', []) as $image) {
                $path = $image->storePublicly('products', 's3');
                $urls[] = \Illuminate\Support\Facades\Storage::disk('s3')->url($path);
            }

            return response()->json(['urls' => $urls]);
        });

        // Order routes
        Route::apiResource('orders', OrderController::class);

        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
        Route::post('orders/{order}/retry-payment', [OrderController::class, 'retryPayment']);

        // Payment routes
        Route::apiResource('payments', PaymentController::class);
        Route::post('payments/webhook', [PaymentController::class, 'webhook']);

        // Asaas payment routes
        Route::prefix('asaas')->group(function () {
            // Charges
            Route::post('/charges/boleto', [AsaasController::class, 'chargeBoleto']);
            Route::post('/charges/pix', [AsaasController::class, 'chargePix']);
            Route::post('/charges/card', [AsaasController::class, 'chargeCard']);
            Route::get('/charges/{paymentId}', [AsaasController::class, 'getChargeStatus']);
            Route::delete('/charges/{paymentId}', [AsaasController::class, 'cancelCharge']);
            // Customer
            Route::post('/customers', [AsaasController::class, 'createCustomer']);
        });

        // Cart routes
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/add', [CartController::class, 'add']);
            Route::put('/update/{cartItem}', [CartController::class, 'update']);
            Route::delete('/remove/{cartItem}', [CartController::class, 'remove']);
            Route::delete('/clear', [CartController::class, 'clear']);
        });

        // Address routes
        Route::apiResource('addresses', AddressController::class);
        Route::put('addresses/{address}/set-default', [AddressController::class, 'setAsDefault']);

        // User profile routes
        Route::prefix('user')->group(function () {
            Route::get('/orders', [UserController::class, 'orders']);
            Route::get('/orders/{order}', [UserController::class, 'orderDetail']);
            Route::get('/integrations', [UserIntegrationController::class, 'index']);
            Route::get('/integrations/available-providers', [UserIntegrationController::class, 'availableProviders']);
            Route::post('/integrations', [UserIntegrationController::class, 'store']);
            Route::put('/integrations/{id}', [UserIntegrationController::class, 'update']);
            Route::patch('/integrations/{id}/toggle', [UserIntegrationController::class, 'toggle']);
            Route::patch('/integrations/{id}/set-default', [UserIntegrationController::class, 'setDefault']); // ← NOVA ROTA
            Route::delete('/integrations/{id}', [UserIntegrationController::class, 'destroy']);
        });

        // Admin routes
        Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
            // Dashboard
            Route::get('/dashboard', [AdminController::class, 'dashboard']);

            // User management
            Route::get('/users', [AdminController::class, 'listUsers']);
            Route::put('/users/{id}', [AdminController::class, 'updateUser']);
            Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

            // Product management
            Route::post('/products', [ProductController::class, 'store']);
            Route::put('/products/{product}', [ProductController::class, 'update']);
            Route::delete('/products/{product}', [ProductController::class, 'destroy']);

            // Order management
            Route::get('/orders', [AdminController::class, 'listOrders']);
            Route::get('/orders/{order}', [OrderController::class, 'show']);
            Route::put('/orders/{order}', [AdminController::class, 'updateOrder']);

            // Category management
            Route::get('/categories', [CategoryController::class, 'index']);
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::put('/categories/{category}', [CategoryController::class, 'update']);
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
            Route::prefix('layout')->group(function () {
                Route::get('/', [LayoutSectionController::class, 'index']);
                Route::post('/', [LayoutSectionController::class, 'store']);
                Route::put('/{layoutSection}', [LayoutSectionController::class, 'update']);
                Route::delete('/{layoutSection}', [LayoutSectionController::class, 'destroy']);
            });
        });
        Route::apiResource('cart', CartController::class);
    });
});
