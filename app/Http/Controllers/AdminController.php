<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function products()
    {
        $products = Product::all();

        return view('admin.products', compact('products'));
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);

        return view('admin.edit_product', compact('product'));
    }

    public function deleteProduct($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function addProductForm()
    {
        return view('admin.add_product');
    }

    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        if ($request->hasFile('image')) {
            ImageUploadService::resolve()->handle($request->file('image'), $product);
        } else {
            $product->image = 'product-placeholder.jpg';
        }

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product added successfully');
    }
}
