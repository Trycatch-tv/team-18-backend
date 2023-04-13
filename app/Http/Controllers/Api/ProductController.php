<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::with('category')->orderBy('id', 'DESC')->get();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|decimal:16,2',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|numeric',
            'description' => 'required|string|max:255',
            'image_url' => 'required|string|max:150',
        ]);
        $product = Products::create($request->all());
        return response()->json($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Products::with('category')->find($id);
        if ($product == null) {
            $msg = [
                "mensaje" => "No hay ningun producto con el id ingresado, ingrese otro por favor",
            ];
            return response()->json($msg, 404);
        }
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Products::with('category')->find($id);
        if ($product == null) {
            $msg = [
                "mensaje" => "No hay ningun producto con el id ingresado, ingrese otro por favor",
            ];
            return response()->json($msg, 404);
        }
        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|decimal:16,2',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|numeric',
            'description' => 'required|string|max:255',
            'image_url' => 'required|string|max:150',
        ]);
        $product->update($request->all());
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Products::find($id);
        if ($product == null) {
            $msg = [
                "mensaje" => "No hay ningun producto con el id ingresado, ingrese otro por favor",
            ];
            return response()->json($msg, 404);
        }
        $product->delete();
        return response()->json($product);
    }
}
