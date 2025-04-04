<?php

namespace App\Http\Controllers;

use App\Jobs\SendPriceChangeNotification;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    public function updateProduct(Request $request, $id)
    {
        // Validate the name field
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::findOrFail($id);

        // Store the old price before updating
        $oldPrice = $product->price;

        $product->update($request->only(['name', 'description', 'price']));

        if ($request->hasFile('image')) {
            $this->uploadImage($request, $product);
            $product->save();
        }

        // Check if price has changed
        if ($oldPrice != $product->price) {
            $exception = SendPriceChangeNotification::forProduct($product, $oldPrice);
            $exception && $this->logFailedNotification($exception);
        }

        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
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
            $this->uploadImage($request, $product);
        } else {
            $product->image = 'product-placeholder.jpg';
        }

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product added successfully');
    }

    private function uploadImage(Request $request, Product $product): void
    {
        $file = $request->file('image');

        // $filename = $file->getClientOriginalExtension();  <== this user input value is not safe!
        $safeName = $this->getSafeFilename($product, $file);
        $file->move(public_path('uploads'), $safeName);
        $product->image = 'uploads/'.$safeName;
    }

    /**
     * @see https://securinglaravel.com/laravel-security-file-upload-vulnerability/
     */
    private function getSafeFilename(Product $product, UploadedFile $file): string
    {
        return Str::limit(md5($product->getKey()), 20, '').'.'.$file->extension();
    }

    private function logFailedNotification($e)
    {
        Log::error('Failed to dispatch price change notification: '.$e->getMessage());
    }
}
