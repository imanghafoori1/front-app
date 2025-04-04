<?php

namespace App\Http\Controllers;

use App\Models\Product;

class AdminController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('admin.products', compact('products'));
    }

    public function edit($id)
    {
        $product = Product::query()->findOrFail($id);

        return view('admin.edit_product', compact('product'));
    }

    public function delete($id)
    {
        Product::query()->findOrFail($id)->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }
}
