<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendPriceChangeNotification;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UpdateProductController
{
    public function __invoke(Request $request, $id)
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
            ImageUploadService::resolve()->handle($request->file('image'), $product);
            $product->save();
        }

        // Check if price has changed
        if ($oldPrice != $product->price) {
            $exception = SendPriceChangeNotification::forProduct($product, $oldPrice);
            $exception && $this->logFailedNotification($exception);
        }

        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    private function logFailedNotification($e)
    {
        Log::error('Failed to dispatch price change notification: '.$e->getMessage());
    }
}
