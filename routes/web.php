<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegularCustomerController;
use App\Http\Controllers\IrregularCustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\MonthlyBillController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\NonInventoryController;

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
Route::prefix("admin")->namespace("App\Http\Controllers")->group(function () {
    Route::match(['get', 'post'], 'login', [AdminController::class, "login"])->name('admin.login');
    Route::group(['middleware' => [\App\Http\Middleware\Admin::class]], function () {
        Route::get("dashboard", [AdminController::class, "dashboard"])->name('admin.dashboard');
        Route::get('/monthly-bill-due', [AdminController::class, 'showDueBills'])->name('monthlyBill.showDueBills');

        Route::get('regular-customers', [RegularCustomerController::class, 'index'])->name('admin.regularCustomer.index');
        Route::get('regular-customers/create', [RegularCustomerController::class, 'create'])->name('admin.regularCustomer.create');
        Route::post('regular-customers', [RegularCustomerController::class, 'store'])->name('admin.regularCustomer.store');
        Route::post('regular-customers/regular/', [BillController::class, 'storeRegularCustomer'])->name('admin.regularCustomer.storeRegularCustomer');

        Route::get('regular-customer/{customer}/edit', [RegularCustomerController::class, 'edit'])->name('admin.regularCustomer.edit');
        Route::put('regular-customer/{customer}/update', [RegularCustomerController::class, 'update'])->name('admin.regularCustomer.update');
        Route::post('regular-customer/{customer}/toggle-status', [RegularCustomerController::class, 'toggleStatus'])->name('admin.regularCustomer.toggleStatus');
        Route::delete('regular-customer/{customer}', [RegularCustomerController::class, 'destroy'])->name('admin.regularCustomer.destroy');

        Route::get('irregular-customers', [IrregularCustomerController::class, 'index'])->name('admin.irregularCustomer.index');
        Route::get('irregular-customers/create', [IrregularCustomerController::class, 'create'])->name('admin.irregularCustomer.create');
        Route::post('irregular-customers', [IrregularCustomerController::class, 'store'])->name('admin.irregularCustomer.store');
        Route::post('irregular-customers/irregular', [BillController::class, 'storeIrregularCustomer'])->name('admin.irregularCustomer.storeIrregularCustomer');
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
        Route::post('/product/updateQuantity/{product}', [ProductController::class, 'updateQuantity']);
        Route::get('/product/{id}/sales', [ProductController::class, 'sales'])->name('admin.product.sales');
        Route::post('product/add-product', [ProductController::class, 'addProduct'])->name('admin.product.addProduct');
        Route::get('product/{id}/stock-list', [ProductController::class, 'stockList'])->name('admin.product.stockList');

        Route::get('non-inventory-items', [NonInventoryController::class, 'index'])->name('admin.nonInventory.index');
        Route::get('non-inventory-items/create', [NonInventoryController::class, 'create'])->name('admin.nonInventory.create');
        Route::post('non-inventory-items', [NonInventoryController::class, 'store'])->name('admin.nonInventory.store');
        Route::get('non-inventory-item/{id}/edit', [NonInventoryController::class, 'edit'])->name('admin.nonInventory.edit');
        Route::put('non-inventory-item/{id}/update', [NonInventoryController::class, 'update'])->name('admin.nonInventory.update');
        Route::post('non-inventory-item/{id}/toggle-status', [NonInventoryController::class, 'toggleStatus'])->name('admin.nonInventory.toggleStatus');
        Route::delete('non-inventory-item/{id}', [NonInventoryController::class, 'destroy'])->name('admin.nonInventory.destroy');




        // routes/web.php
        Route::get('get_customers', [BillController::class, 'getCustomers'])->name('get_customers');
        Route::get('get-product', [BillController::class, 'getProduct'])->name('get-product');
        Route::get('getProductsByCategory', [BillController::class, 'getProductsByCategory'])->name('getProductsByCategory');
        Route::get('bill/', [BillController::class, 'index'])->name('admin.bill.index');
        Route::get('bill/create', [BillController::class, 'create'])->name('admin.bill.create');
        Route::post('bill/', [BillController::class, 'store'])->name('bill.store');

        Route::put('bill/{id}', [BillController::class, 'update'])->name('admin.bill.update');
        Route::get('bill/{id}/invoice', [BillController::class, 'invoice'])->name('admin.bill.invoice');
        Route::get('bill/{id}/challan', [BillController::class, 'challan'])->name('admin.bill.challan');
        Route::delete('bill/{bill}', [BillController::class, 'destroy'])->name('bill.destroy');

        Route::post('/bill/markPaid', [BillController::class, 'markPaid'])->name('bill.markPaid');
        Route::get('/admin/bill/payment-history/{bill}', [BillController::class, 'paymentHistory'])->name('admin.bill.paymentHistory');
        Route::get('bill/{id}/edit', [BillController::class, 'edit'])->name('admin.bill.edit');
        Route::post('bill/{id}/update', [BillController::class, 'update'])->name('bill.update');
        Route::post('bill/bulk-invoice', [BillController::class, 'bulkInvoice'])->name('admin.bill.bulkInvoice');
        // Route::delete('/payment/{id}', [BillController::class, 'PaymentDestroy'])->name('payment.delete');



        Route::get('monthlyBill', [MonthlyBillController::class, 'index'])->name('admin.monthlyBill.index');
        Route::get('monthlyBill/create', [MonthlyBillController::class, 'create'])->name('admin.monthlyBill.create');
        Route::post('monthlyBill', [MonthlyBillController::class, 'store'])->name('monthlyBill.store');
        Route::delete('monthlyBill/{id}', [MonthlyBillController::class, 'destroy'])->name('monthlyBill.destroy');
        Route::post('monthlyBill/{id}/toggle-status', [MonthlyBillController::class, 'toggleStatus'])->name('monthlyBill.toggleStatus');
        Route::get('admin/invoice/{clientId}/{month}', [MonthlyBillController::class, 'showInvoice'])->name('admin.monthlyBill.invoice');
        Route::get('admin/monthly-bill/{bill}/print', [MonthlyBillController::class, 'showInvoicePrint'])->name('admin.monthlyBill.showInvoicePrint');
        Route::get('monthlyBill/{customer}/edit', [MonthlyBillController::class, 'edit'])->name('admin.monthlyBill.edit');
        Route::put('/monthlyBill/{id}', [MonthlyBillController::class, 'update'])->name('monthlyBill.update');
        Route::post('/monthlyBill/payment', [MonthlyBillController::class, 'storePayment'])->name('monthlyBill.storePayment');
        Route::get('/monthly-bills/{id}', [MonthlyBillController::class, 'showBill'])->name('admin.monthlyBill.showBill');
        Route::post('/monthlyBill/paid', [MonthlyBillController::class, 'Paid'])->name('monthlyBill.Paid');
        Route::get('monthlyBill/generateMonthlyBills', [MonthlyBillController::class, 'generateMonthlyBills'])->name('monthlyBill.generateMonthlyBills');
       // Route::post('admin/monthlyBill/bulkInvoice', [MonthlyBillController::class, 'bulkInvoice'])->name('admin.monthlyBill.bulkInvoice');
       Route::post('admin/monthlyBill/bulkInvoice/{clientId}/{month}', [MonthlyBillController::class, 'bulkInvoice'])->name('admin.monthlyBill.bulkInvoice');
       Route::post('/monthlyBill/bulkPaid', [MonthlyBillController::class, 'bulkPaid'])->name('monthlyBill.bulkPaid');





        Route::get('get-customers', [QuotationController::class, 'getCustomers'])->name('get-customers');
        Route::get('/quotations', [QuotationController::class, 'index'])->name('admin.quotation.index');
        Route::get('/quotations/create', [QuotationController::class, 'create'])->name('admin.quotation.create');
        Route::get('quotations/{id}/quotation', [QuotationController::class, 'quotation'])->name('admin.quotation.quotation');
        Route::post('/quotations', [QuotationController::class, 'store'])->name('quotation.store');
        Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotation.destroy');

        Route::get("logout", [AdminController::class, "logout"]);
    });
});
