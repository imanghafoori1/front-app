<?php

namespace App\Http\Controllers;

use App\Models\Product;

class AdminController extends Controller
{
    public function products()
    {
        $products = Product::all();

        return view('admin.products', compact('products'));
    }

    public function editProduct($id)
    {
        $product = Product::query()->findOrFail($id);

        return view('admin.edit_product', compact('product'));
    }

    public function deleteProduct($id)
    {
        Product::query()->findOrFail($id)->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function addProductForm()
    {
        return view('admin.add_product');
    }
}
