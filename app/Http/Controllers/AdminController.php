<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegularCustomer;
use App\Models\IrregularCustomer;
use App\Models\MonthlyBill;
use App\Models\PaymentHistory;
use App\Models\Bill;
use App\Models\Product;
use App\Models\BillItem2;
use Auth;
use Validator;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get the current month and year
        $currentMonth = \Carbon\Carbon::now()->month;
        $currentYear = \Carbon\Carbon::now()->year;

        // Calculate total number of regular customers for the current month
        $totalRegularCustomers = RegularCustomer::count();
        $totalIrregularCustomers = IrregularCustomer::count();

        $totalDueAmount = MonthlyBill::where('status', 'Due')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        $totalPaidAmount = MonthlyBill::where('status', 'Paid')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        $totalPaid = PaymentHistory::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('receivable_amount');



        $totalDue = BillItem2::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('due_amount');
        $totalExtraDue = Bill::where('due_amount', '>', 0)
            ->where('status', 'pending')
            ->sum('due_amount');
        $totalProductAmount = Product::sum('total_amount');
        // Pass the count to the view
        return view("admin.dashboard", compact('totalRegularCustomers', 'totalIrregularCustomers', 'totalDueAmount', 'totalPaidAmount', 'totalPaid', 'totalDue', 'totalProductAmount', 'totalExtraDue'));
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required|max:30'
            ];

            $customMessages = [
                'email.required' => 'Email is required',
                'email.email' => 'Valid email is required',
                'password.required' => 'Password is required',


            ];

            $this->validate($request, $rules, $customMessages);
            if (Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
                return redirect('admin/dashboard');
            } else {
                return redirect()->back()->with('error_message', "Invalid Email or Password");
            }
        }
        return view("admin.login");
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }
    public function showDueBills()
    {
        $currentMonth = \Carbon\Carbon::now()->format('F Y');
        $dueBills = MonthlyBill::where('status', 'due')
            ->whereMonth('bill_month', now()->month)
            ->whereYear('bill_month', now()->year)
            ->get();

        $totalDueAmount = $dueBills->sum('amount');

        return view('admin.monthlyBill.index', compact('dueBills', 'totalDueAmount', 'currentMonth'));
    }
}
