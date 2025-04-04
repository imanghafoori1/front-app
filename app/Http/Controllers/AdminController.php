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

        $product = Product::query()->create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        $product->image = $this->handleImage($request, $product);

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product added successfully');
    }

    private function handleImage(Request $request, Product $product): string
    {
        if (! $request->hasFile('image')) {
            return 'product-placeholder.jpg';
        }

        $file = $request->file('image');

        return ImageUploadService::resolve()->handle($file, $product);
    }
}
