<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::with('category')->orderBy('id', 'DESC')->paginate(15);
        return response()->json($products, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|numeric',
            'description' => 'required|string|max:255',
            'image_url' => 'required|string|max:150',
        ];

        // Por si quieren mensajes en español
        $messages = [
            'name.required' => 'Se requiere un nombre para el producto',
            'name.max' => 'Solo se aceptan 100 caracteres como máximo',
            'price.required' => 'Se requiere un precio para el producto',
            'price.numeric' => 'El campo de precio debe ser un número.',
            'price.decimal' => 'El campo de precio debe tener 16-2 decimales',
            'category_id.required' => 'Debes ingresar una categoria para el producto',
            'category_id.exists' => 'Debes ingresar una categoria valida para el producto',
            'stock.required' => 'Se requiere una cantidad para el producto',
            'stock.numeric' => 'El campo de stock debe ser un número.',
            'description.required' => 'Se requiere una descripcion para el producto',
            'description.max' => 'Solo se aceptan 255 caracteres como máximo',
            'image_url.required' => 'Se requiere una url para la imagen del producto',
            'image_url.max' => 'Solo se aceptan 150 caracteres como máximo',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errores = [
                'status' => 400,
                'messages' => 'Oops we have detected errors',
                'errors' => $validator->errors(),
            ];

            return response()->json($errores, 400);
        }

        // TODO: Ver temas de aumentar o disminuir stock
        $product = Products::create($request->all());
        return response()->json([
            'status' => 200,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Products::with('category')->orderBy('id', 'DESC')->find($id);
        if ($product == null) {
            return response()->json([
                'status' => 400,
                'message' => "There is no product with the id entered, please enter another one",
            ], 400);
        }
        return response()->json([
            'status' => 200,
            'data' => $product,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Products::with('category')->orderBy('id', 'DESC')->find($id);
        if ($product == null) {
            return response()->json([
                'status' => 400,
                'message' => "There is no product with the id entered, please enter another one",
            ], 400);
        }
        $rules = [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|numeric',
            'description' => 'required|string|max:255',
            'image_url' => 'required|string|max:150',
        ];

        $messages = [
            'name.required' => 'Se requiere un nombre para el producto',
            'name.max' => 'Solo se aceptan 100 caracteres como máximo',
            'price.required' => 'Se requiere un precio para el producto',
            'price.numeric' => 'El campo de precio debe ser un número.',
            'price.decimal' => 'El campo de precio debe tener 16-2 decimales',
            'category_id.required' => 'Debes ingresar una categoria para el producto',
            'category_id.exists' => 'Debes ingresar una categoria valida para el producto',
            'stock.required' => 'Se requiere una cantidad para el producto',
            'stock.numeric' => 'El campo de stock debe ser un número.',
            'description.required' => 'Se requiere una descripcion para el producto',
            'description.max' => 'Solo se aceptan 255 caracteres como máximo',
            'image_url.required' => 'Se requiere una url para la imagen del producto',
            'image_url.max' => 'Solo se aceptan 150 caracteres como máximo',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errores = [
                'status' => 400,
                'messages' => 'Oops we have detected errors',
                'errors' => $validator->errors(),
            ];
        }

        $product->update($request->all());
        return response()->json([
            'status' => 200,
            'message' => 'Product updated successfully',
            'data' => $product,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Products::find($id);
        if ($product == null) {
            return response()->json([
                'status' => 400,
                'message' => "There is no product with the id entered, please enter another one",
            ], 400);
        }
        $product->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
