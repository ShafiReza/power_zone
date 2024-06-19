<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IrregularCustomer;

class IrregularCustomerController extends Controller
{
    public function index()
    {
        $irregularCustomers = IrregularCustomer::all();
        return view('admin.irregularCustomer.index', compact('irregularCustomers'));
    }

    public function create()
    {
        return view('admin.irregularCustomer.create');
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

        IrregularCustomer::create($request->all());
        return redirect()->route('admin.irregularCustomer.index')
            ->with('success', 'Irregular Customer created successfully.');
    }

    public function edit(IrregularCustomer $customer)
    {
        return view('admin.irregularCustomer.edit', compact('customer'));
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
