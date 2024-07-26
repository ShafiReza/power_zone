<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'details' => 'nullable',
            'status' => 'required'
        ]);

        Category::create($request->all());
        return redirect()->route('admin.category.index')
            ->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
            'details' => 'nullable',
            'status' => 'required'
        ]);

        $category->update($request->all());
        return redirect()->route('admin.category.index')
            ->with('success', 'Category updated successfully.');
    }


    public function toggleStatus(Category $category)
    {
        $category->status = $category->status == 'active' ? 'inactive' : 'active';
        $category->save();

        return redirect()->route('admin.category.index')
            ->with('success', 'Category status updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.category.index')
            ->with('success', 'Category deleted successfully.');
    }
}
