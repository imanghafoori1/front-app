<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Services\ImageUploadService;

class StoreProcustController
{
    public function __invoke(StoreProductRequest $request)
    {
        $product = Product::query()->create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        $product->image = $this->handleImage($request, $product);

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product added successfully');
    }

    private function handleImage($request, Product $product): string
    {
        if (! $request->hasFile('image')) {
            return 'product-placeholder.jpg';
        }

        $file = $request->file('image');

        return ImageUploadService::resolve()->handle($file, $product);
    }
}
