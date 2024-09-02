<?php

namespace App\Http\Controllers;

use App\Models\NonInventory;
use Illuminate\Http\Request;

class NonInventoryController extends Controller
{
    public function index(Request $request)
    {
        $title="NonInventory";
        $query = NonInventory::query();

        if ($request->has('name') && $request->name) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        $nonInventories = $query->get();

        return view('admin.nonInventory.index', compact('nonInventories','title'));
    }


    public function create()
    {
        $title="NonInventory";
        return view('admin.nonInventory.create', compact('title'));
    }

    public function store(Request $request)
    {


        $nonInventory = new NonInventory($request->all());
        $nonInventory->total_amount = $nonInventory->purchase_price * $nonInventory->quantity;
        $nonInventory->save();

        return redirect()->route('admin.nonInventory.index')
            ->with('success', 'Non-Inventory Item created successfully.');
    }

    public function edit($id)
    {
        $nonInventory = NonInventory::findOrFail($id);
        return view('admin.nonInventory.edit', compact('nonInventory'));
    }

    public function update(Request $request, $id)
    {


        $nonInventory = NonInventory::findOrFail($id);
        $nonInventory->fill($request->all());
        $nonInventory->total_amount = $nonInventory->purchase_price * $nonInventory->quantity;
        $nonInventory->save();

        return redirect()->route('admin.nonInventory.index')
            ->with('success', 'Non-Inventory Item updated successfully.');
    }

    public function destroy($id)
    {
        $nonInventory = NonInventory::findOrFail($id);
        $nonInventory->delete();

        return redirect()->route('admin.nonInventory.index')
            ->with('success', 'Non-Inventory Item deleted successfully.');
    }
    public function toggleStatus($id)
    {
        $nonInventory = NonInventory::findOrFail($id);
        $nonInventory->status = $nonInventory->status === 'active' ? 'inactive' : 'active';
        $nonInventory->save();

        return redirect()->route('admin.nonInventory.index')
            ->with('success', 'Non-Inventory Item status updated successfully.');
    }
}
