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

    public function edit(Product $product)
    {
        return view('admin.edit_product', compact('product'));
    }

    public function delete(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }
}
