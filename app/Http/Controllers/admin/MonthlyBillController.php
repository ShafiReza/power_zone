<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use App\Models\MonthlyBill;
use App\Models\RegularCustomer;
use App\Models\Payment;
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
            'description' => 'nullable|string',
            'service' => 'required|in:lift,generator,lift and generator',
            'bill_month' => 'required|date_format:Y-m',
            'start_date' => 'required|date',

        ]);

        MonthlyBill::create([
            'regular_customer_id' => $request->customer_id,
            'customer_address' => $request->customer_address,
            'amount' => $request->amount,
            'description' => $request->description,
            'service' => $request->service,
            'bill_month' => $request->bill_month,
            'type' => 'initial',
            'start_date' => $request->start_date,
            'next_generation_date' => Carbon::parse($request->start_date)->addMonth(),
            // 'status' => $request->status,
        ]);

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Bill created successfully.');
    }
    public function index(Request $request)
    {
        $query = MonthlyBill::query();

        if ($request->filled('month')) {
            $query->whereMonth('bill_month', '=', $request->month);
        }

        if ($request->filled('customer_name')) {
            $query->whereHas('regularCustomer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        $bills = $query->with('regularCustomer')->get();

        return view('admin.monthlyBill.index', compact('bills'));
    }

    public function destroy($id)
    {
        $bill = MonthlyBill::findOrFail($id);
        $bill->delete();

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Monthly Bill deleted successfully.');
    }

    public function showInvoice($clientId, $month)
    {
        $customer = RegularCustomer::findOrFail($clientId); // Retrieve the customer
        $bills = MonthlyBill::where('regular_customer_id', $clientId)
            ->where('bill_month', '<', $month) // Exclude the current month
            ->orderBy('bill_month')
            ->get();

        $previousDues = [];

        foreach ($bills as $bill) {
            if ($bill->status == 'due' || $bill->status == 'partial') {
                $previousDues[] = [
                    'sl_no' => $bill->id,
                    'month' => $bill->bill_month,
                    'due_amount' => $bill->due_amount ?? 0,
                ];
            }
        }

        return view('admin.monthlyBill.invoice', compact('bills', 'previousDues', 'customer'));
    }


    public function showInvoicePrint(MonthlyBill $bill)
    {
        $customer = $bill->regularCustomer;

        // Calculate previous months' due
        $previousBills = MonthlyBill::where('regular_customer_id', $bill->regular_customer_id)
            ->where(function ($q) {
                $q->where('status', 'Due')
                  ->orWhere('status', 'partial')
                  ->orWhere('status', 'Mark as Paid');
            })
            ->where('bill_month', '<', $bill->bill_month) // Exclude the current bill's month
            ->orderBy('bill_month', 'asc')
            ->get(['bill_month', 'amount', 'due_amount', 'status']); // Include status in the select

        return view('admin.monthlyBill.invoice_print', compact('bill', 'customer', 'previousBills'));
    }

    public function Paid(Request $request)
    {


        $bill = MonthlyBill::findOrFail($request->bill_id);

        if ($bill->status === 'pending') {
            $billAmount = $bill->amount;
        } else {
            $billAmount = $bill->due_amount;
        }

        $receivableAmount = $request->receivable_amount;
        $dueAmount = $billAmount - $receivableAmount;
        $status = ($dueAmount <= 0) ? 'paid' : 'partial';
        $bill->type = 'Ongoing';

        $bill->due_amount = $dueAmount;
        $bill->status = $status;
        $bill->save();

        $payment = Payment::create([
            'bill_id' => $bill->id,
            'description' => $request->description,
            'amount' => $billAmount,
            'receivable_amount' => $receivableAmount,

            'due_amount' => $dueAmount,
        ]);

        return response()->json(['success' => true, 'dueAmount' => $payment->due_amount]);
    }



    public function showBill($billId)
    {
        $payments = Payment::where('bill_id', $billId)->latest()->get();
        return view('admin.monthlyBill.storePayment', compact('payments'));
    }

    public function generateMonthlyBills()
    {
        $regularCustomers = RegularCustomer::all();

        foreach ($regularCustomers as $customer) {
            $start_date = now()->startOfMonth();
            $bill_month = $start_date->format('Y-m');

            // Fetch the most recent bill amount or set a default amount
            $lastBill = MonthlyBill::where('regular_customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $amount = $lastBill ? $lastBill->amount : 0; // Replace 100 with your default logic if necessary

            MonthlyBill::create([
                'regular_customer_id' => $customer->id,
                'amount' => $amount,
                'description' => 'Monthly bill for ' . $start_date->format('F Y'),
                'service' => $lastBill ? $lastBill->service : 'default_service', // Use previous service or set a default
                'bill_month' => $bill_month,
                'start_date' => $start_date,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Monthly Bills generated successfully.');
    }
    public function getBillDetails(RegularCustomer $customer)
    {
        // Fetch the most recent bill amount or set a default amount
        $lastBill = MonthlyBill::where('regular_customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $amount = $lastBill ? $lastBill->amount : 0; // Replace with default logic if necessary
        $description = $lastBill->description;
        $next_generation_date = $lastBill->next_generation_date;

        return [
            'amount' => $amount,
            'description' => $description,
            'service' => $lastBill ? $lastBill->service : 'default_service', // Use previous service or set a default
            'next_generation_date'=> $next_generation_date
        ];
    }
}
