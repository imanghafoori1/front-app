<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UpdateProductRequest;
use App\Jobs\SendPriceChangeNotification;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Log;

class UpdateProductController
{
    public function __invoke(UpdateProductRequest $request, Product $product)
    {
        $product->fill($request->only(['name', 'description', 'price']));

        if ($request->hasFile('image')) {
            $product->image = ImageUploadService::resolve()->handle($request->file('image'), $product);
        }

        // Check if price has changed
        if ($product->isDirty('price')) {
            $exception = SendPriceChangeNotification::forProduct($product);
            $exception && $this->logFailedNotification($exception);
        }
        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    private function logFailedNotification($e)
    {
        Log::error('Failed to dispatch price change notification: '.$e->getMessage());
    }
}
