<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonthlyBill;
use App\Models\RegularCustomer;
use Carbon\Carbon;
class MonthlyBillController extends Controller
{
    public function create()
    {
        $customers = RegularCustomer::all();
        return view('admin.monthlyBill.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:regular_customers,id',
            'amount' => 'required|numeric',
            'service' => 'required|in:lift,generator,lift and generator',
            'bill_month' => 'required|date_format:Y-m',
            'start_date' => 'required|date',

        ]);

        MonthlyBill::create([
            'regular_customer_id' => $request->customer_id,
            'customer_address' => $request->customer_address,
            'amount' => $request->amount,
            'service' => $request->service,
            'bill_month' => $request->bill_month,
            'start_date' => $request->start_date,
            'next_generation_date' => Carbon::parse($request->start_date)->addMonth(),
            // 'status' => $request->status,
        ]);

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Bill created successfully.');
    }

    public function index(Request $request)
    {
        $query = MonthlyBill::with('regularCustomer');

        if ($request->has('month')) {
            $query->where('bill_month', 'LIKE', $request->month . '%');
        }

        if ($request->has('customer_name')) {
            $query->whereHas('regularCustomer', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->customer_name . '%');
            });
        }

        $bills = $query->get();

        return view('admin.monthlyBill.index', compact('bills'));
    }

    public function destroy($id)
    {
        MonthlyBill::find($id)->delete();
        return redirect()->route('admin.monthlyBill.index')->with('success', 'Bill deleted successfully.');
    }
    public function toggleStatus($id)
    {
        $bill = MonthlyBill::findOrFail($id);

        $startDate = Carbon::parse($bill->start_date);
        $currentDate = Carbon::now();

        if ($bill->status == 'pending') {
            if ($startDate->diffInMonths($currentDate) < 1) {
                $bill->status = 'paid';
            } elseif ($startDate->diffInMonths($currentDate) >= 1) {
                $bill->status = 'due';
            }
        } else {
            $bill->status = 'pending'; // Reset status to pending if it's currently paid or due
        }

        $bill->save();

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Bill status toggled successfully.');
    }

    public function showInvoice($id)
    {
        $bill = MonthlyBill::with('regularCustomer')->findOrFail($id);
        $customer = $bill->regularCustomer;

        // Calculate previous month's due


        return view('admin.monthlyBill.invoice', compact('bill', 'customer', 'previousDue'));
    }
    public function showInvoicePrint(MonthlyBill $bill)
    {
        $customer = $bill->regularCustomer; // Assuming your relationship is correctly defined

        // Calculate previous month's due
        $previousMonth = Carbon::parse($bill->bill_month)->subMonth()->format('Y-m');
        $previousDue = MonthlyBill::where('regular_customer_id', $bill->regular_customer_id)
            ->where('bill_month', $previousMonth)
            ->where('status', 'due')
            ->sum('amount');

        return view('admin.monthlyBill.invoice_print', compact('bill', 'customer', 'previousDue'));
    }

}
