<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IrregularCustomer;
use App\Models\Customer;
class IrregularCustomerController extends Controller
{
    public function index(Request $request)
    {
        $title = "Irregular Customers";
        $query = IrregularCustomer::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $irregularCustomers = $query->get();

        return view('admin.irregularCustomer.index', compact('irregularCustomers','title'));
    }

    public function create()
    {
        $title = "Irregular Customers";
        return view('admin.irregularCustomer.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'area' => 'required',
            'city' => 'required',
            'status' => 'required'
        ]);

        $irregularCustomer = IrregularCustomer::create($request->all());

        Customer::create([
            'irregular_customer_id' => $irregularCustomer->id,
            'customer_type' => 'irregular',
            'customer_name' => $irregularCustomer->name,

        ]);

        //IrregularCustomer::create($request->all());
        return redirect()->route('admin.irregularCustomer.index')
            ->with('success', 'Irregular Customer created successfully.');
    }

    public function edit(IrregularCustomer $customer)
    {
        $title = "Irregular Customers";
        return view('admin.irregularCustomer.edit', compact('customer','title'));
    }

    public function update(Request $request, IrregularCustomer $customer)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'area' => 'required',
            'city' => 'required',
            'status' => 'required'
        ]);

        $customer->update($request->all());
        return redirect()->route('admin.irregularCustomer.index')
            ->with('success', 'Irregular Customer updated successfully.');
    }

    public function destroy(IrregularCustomer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.irregularCustomer.index')
            ->with('success', 'Irregular Customer deleted successfully.');
    }

    public function toggleStatus(IrregularCustomer $customer)
    {
        $customer->status = $customer->status == 'active' ? 'inactive' : 'active';
        $customer->save();

        return redirect()->route('admin.irregularCustomer.index')
            ->with('success', 'Irregular Customer status updated successfully.');
    }

}
