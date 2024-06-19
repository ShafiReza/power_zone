<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\Admin\RegularCustomerController;
use App\Http\Controllers\Admin\IrregularCustomerController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\BillController;
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

Route::get('/', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('admin.login');
    }
});
Route::prefix("admin")->namespace("App\Http\Controllers\Admin")->group(function () {
    Route::match(['get', 'post'], 'login', [AdminController::class, "login"])->name('admin.login');
    Route::group(['middleware' => [\App\Http\Middleware\Admin::class]], function () {
        Route::get("dashboard", [AdminController::class, "dashboard"])->name('admin.dashboard');

        Route::get('regular-customers', [RegularCustomerController::class, 'index'])->name('admin.regularCustomer.index');
        Route::get('regular-customers/create', [RegularCustomerController::class, 'create'])->name('admin.regularCustomer.create');
        Route::post('regular-customers', [RegularCustomerController::class, 'store'])->name('admin.regularCustomer.store');
        Route::get('regular-customer/{customer}/edit', [RegularCustomerController::class, 'edit'])->name('admin.regularCustomer.edit');
        Route::put('regular-customer/{customer}/update', [RegularCustomerController::class, 'update'])->name('admin.regularCustomer.update');
        Route::post('regular-customer/{customer}/toggle-status', [RegularCustomerController::class, 'toggleStatus'])->name('admin.regularCustomer.toggleStatus');
        Route::delete('regular-customer/{customer}', [RegularCustomerController::class, 'destroy'])->name('admin.regularCustomer.destroy');

        Route::get('irregular-customers', [IrregularCustomerController::class, 'index'])->name('admin.irregularCustomer.index');
        Route::get('irregular-customers/create', [IrregularCustomerController::class, 'create'])->name('admin.irregularCustomer.create');
        Route::post('irregular-customers', [IrregularCustomerController::class, 'store'])->name('admin.irregularCustomer.store');
        Route::get('irregular-customer/{customer}/edit', [IrregularCustomerController::class, 'edit'])->name('admin.irregularCustomer.edit');
        Route::put('irregular-customer/{customer}/update', [IrregularCustomerController::class, 'update'])->name('admin.irregularCustomer.update');
        Route::post('irregular-customer/{customer}/toggle-status', [IrregularCustomerController::class, 'toggleStatus'])->name('admin.irregularCustomer.toggleStatus');
        Route::delete('irregular-customer/{customer}', [IrregularCustomerController::class, 'destroy'])->name('admin.irregularCustomer.destroy');

        Route::get('suppliers', [SupplierController::class, 'index'])->name('admin.supplier.index');
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('admin.supplier.create');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('admin.supplier.store');
        Route::get('supplier/{supplier}/edit', [SupplierController::class, 'edit'])->name('admin.supplier.edit');
        Route::put('supplier/{supplier}/update', [SupplierController::class, 'update'])->name('admin.supplier.update');
        Route::post('supplier/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('admin.supplier.toggleStatus');
        Route::delete('supplier/{supplier}', [SupplierController::class, 'destroy'])->name('admin.supplier.destroy');

        Route::get('categories', [CategoryController::class, 'index'])->name('admin.category.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('admin.category.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('admin.category.store');
        Route::get('category/{category}/edit', [CategoryController::class, 'edit'])->name('admin.category.edit');
        Route::put('category/{category}/update', [CategoryController::class, 'update'])->name('admin.category.update');
        Route::post('category/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('admin.category.toggleStatus');
        Route::delete('category/{category}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');



        Route::get('products', [ProductController::class, 'index'])->name('admin.product.index');
        Route::get('products/create', [ProductController::class, 'create'])->name('admin.product.create');
        Route::post('products', [ProductController::class, 'store'])->name('admin.product.store');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('admin.product.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('admin.product.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('admin.product.destroy');
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('admin.product.toggleStatus');

        // Route::post('/bill/save', [BillController::class, 'save'])->name('admin.bill.create');
        // Route::get('/bill/save', [BillController::class, 'save'])->name('admin.bill.create');
        Route::get('/bill/create', function () {
            return view('admin.bill.create');
        })->name('admin.bill.create');

        Route::post('/bill/save', [BillController::class, 'save'])->name('admin.bill.store');
        Route::get("logout", [AdminController::class, "logout"]);
    });
});
