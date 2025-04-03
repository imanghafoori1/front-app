<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $exchangeRate = $this->getExchangeRate();

        return view('products.list', compact('products', 'exchangeRate'));
    }

    public function show(Request $request)
    {
        $id = $request->route('product_id');
        $product = Product::findOrFail($id);
        $exchangeRate = $this->getExchangeRate();

        return view('products.show', compact('product', 'exchangeRate'));
    }

    private function getExchangeRate(): float
    {
        /*
         * we resolve the object from container so that
         * we can mock it when running tests and avoid
         * calling an external service over the wire.
         */
        return resolve(ExchangeRateService::class)->getRate();
    }
}
