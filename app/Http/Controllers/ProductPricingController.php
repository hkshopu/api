<?php

namespace App\Http\Controllers;

use App\ProductPricing;
use Illuminate\Http\Request;

class ProductPricingController extends Controller
{
    // public function showAllProductPricing()
    // {
    //     return response()->json(ProductPricing::all());
    // }

    // public function showOneProductPricing($id)
    // {
    //     return response()->json(ProductPricing::find($id));
    // }

    // public function create(Request $request)
    // {
    //     $productdPricing = ProductPricing::create($request->all());

    //     return response()->json($productdPricing, 201);
    // }

    // public function update($id, Request $request)
    // {
    //     $productdPricing = ProductPricing::findOrFail($id);
    //     $productdPricing->update($request->all());

    //     return response()->json($productdPricing, 200);
    // }

    // public function delete($id)
    // {
    //     ProductPricing::findOrFail($id)->delete();
    //     return response('Deleted Successfully', 200);
    // }
}

