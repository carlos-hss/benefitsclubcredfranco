<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getAllProducts()
    {
        $products = Product::all();

        if (empty($products)) {
            return response()->json(['message' => 'No products registered'], 200);
        }

        return response()->json($products, 200);
    }

    public function getAllActiveProducts()
    {
        $products = Product::where('status', 'A')->get();

        if (empty($products)) {
            return response()->json(['message' => 'No products registered'], 200);
        }

        return response()->json($products, 200);
    }

    public function createProduct(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:1',
            'description' => 'required',
            'price' => 'required',
            'weight' => 'required',
            'points_cost' => 'required',
        ], [
            'name.required' => 'The name field is required.',
            'name.min' => 'The name field cannot be empty.',
            'description.required' => 'The description field is required.',
            'price.required' => 'The price field is required.',
            'weight.required' => 'The weight field is required.',
            'points_cost.required' => 'The points_cost field is required.',
        ]);

        $validatedData['status'] = 'A';

        $product = Product::create($validatedData);

        return response()->json(['data' => $product, 'message' => 'Product successfully created!'], 200);
    }

    public function getProduct($id)
    {
        $product = Product::find($id);

        if(!$product){
            response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product, 200);
    }

    public function updateProduct(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:products',
            'password' => 'nullable|min:8',
            'status' => 'nullable'
        ], [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.'
        ]);

        if (empty($validatedData)) {
            return response()->json(['message' => 'Bad Request'], 400);
        }

        $product = Product::find($id);
        
        if(!$product){
            response()->json(['message' => 'Product not found'], 404);
        }

        $product->update($validatedData);

        return response()->json(['data' => $product, 'message' => 'Product successfully updated!'], 200);
    }

    public function deleteProduct(string $id)
    {
        $product = Product::find($id);

        if(!$product){
            response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product successfully deleted!'], 200);
    }
}
