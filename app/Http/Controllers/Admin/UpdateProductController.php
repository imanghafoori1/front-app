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
