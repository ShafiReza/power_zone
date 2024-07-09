<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\Admin\RegularCustomerController;
use App\Http\Controllers\Admin\IrregularCustomerController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\Admin\MonthlyBillController;
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

        // routes/web.php
        Route::get('get-customers', [BillController::class,'getCustomers'])->name('get-customers');
        Route::get('get-product', [BillController::class,'getProduct'])->name('get-product');
        Route::get('bill/', [BillController::class,'index'])->name('admin.bill.index');
        Route::get('bill/create', [BillController::class,'create'])->name('admin.bill.create');
        Route::post('bill/', [BillController::class,'store'])->name('bill.store');
        Route::get('bill/{id}/edit', [BillController::class,'edit'])->name('admin.bill.edit');
        Route::put('bill/{id}',  [BillController::class,'update'])->name('admin.bill.update');
        Route::get('bill/{id}/invoice', [BillController::class,'invoice'])->name('admin.bill.invoice');
        Route::get('bill/{id}/quotation', [BillController::class,'quotation'])->name('admin.bill.quotation');
        Route::get('bill/{id}/challan', [BillController::class,'challan'])->name('admin.bill.challan');
        Route::delete('bill/{bill}', [BillController::class, 'destroy'])->name('bill.destroy');


        Route::get('monthlyBill', [MonthlyBillController::class, 'index'])->name('admin.monthlyBill.index');
        Route::get('monthlyBill/create', [MonthlyBillController::class, 'create'])->name('admin.monthlyBill.create');
        Route::post('monthlyBill', [MonthlyBillController::class, 'store'])->name('monthlyBill.store');
        Route::delete('monthlyBill/{id}', [MonthlyBillController::class, 'destroy'])->name('monthlyBill.destroy');
        Route::post('monthlyBill/{id}/toggle-status', [MonthlyBillController::class, 'toggleStatus'])->name('monthlyBill.toggleStatus');


        Route::get("logout", [AdminController::class, "logout"]);
    });
});
