<?php

use App\Http\Controllers\Dashboard\AdminsController;
use App\Http\Controllers\Dashboard\CategoriesController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ImportProductsController;
use App\Http\Controllers\Dashboard\ProductsConrtoller;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RolesController;
use App\Http\Controllers\Dashboard\UsersController;
use App\Http\Middleware\CheckUserType;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => ['auth:admin'],
    'as' => 'dashboard.',
    'prefix' => 'admin/dashboard'
], function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

    route::get('/categories/trash', [CategoriesController::class, 'trash'])
        ->name('categories.trash');
    route::put('categories/{category}/restore', [CategoriesController::class , 'restore'])
        ->name('categories.restore');
    route::delete('categories/{category}/force-delete', [CategoriesController::class, 'forceDelete'])
        ->name('categories.force-delete');

    Route::get('products/import', [ImportProductsController::class, 'create'])
        ->name('products.import');
    Route::post('products/import', [ImportProductsController::class, 'store']);


    // Route::resource('/categories', CategoriesController::class);
    // Route::resource('/products', ProductsConrtoller::class);
    Route::resources([
        'products' => ProductsConrtoller::class,
        'categories' => CategoriesController::class,
        'roles' => RolesController::class,
        'users' => UsersController::class,
        'admins' => AdminsController::class,
    ]);




});




