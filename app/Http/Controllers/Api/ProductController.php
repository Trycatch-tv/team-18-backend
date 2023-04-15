<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $product = new Products();
        $file = $request->file('image');
        $nombre = e($request->input('name'));

        if ($request->hasFile('image')) {
            $rules = [
                'name' => 'required|string|max:100',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|numeric',
                'description' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                // 'image_url' => 'required|string|max:150',
            ];

            if (!Storage::disk('public')->exists('products')) {
                Storage::disk('public')->makeDirectory('products', 0775, true);
            }
            $slug = Str::slug($nombre, '-');
            $fileExt = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $fileName = rand(1, 9999) . '-' . Str::slug($nombre) . '.' . $fileExt;
            $final_file = $request->getSchemeAndHttpHost() . '/products/' . $fileName;
            $filesystem = Storage::disk('public');
            $filesystem->putFileAs('products', $file, $fileName);

            $product->image = $final_file;
            $product->image_path = $fileName;
        } else {
            $rules = [
                'name' => 'required|string|max:100',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|numeric',
                'description' => 'required|string|max:255',
                'image' => 'required|string|max:2048',
            ];

            $product->image = $request->input('image');
        }

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
        // Guarda la ruta del archivo en la base de datos o realiza otra acción necesaria
        $product->name = Str::headline($nombre);
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->description = $request->input('description');
        $product->category_id = $request->input('category_id');

        if ($product->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Product created successfully',
                'data' => $product,
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to create the product',
            ], 400);
        }

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
    public function update(string $id, Request $request)
    {
        $product = Products::with('category')->orderBy('id', 'DESC')->find($id);

        if ($product == null) {
            return response()->json([
                'status' => 400,
                'message' => "There is no product with the id entered, please enter another one",
            ], 400);
        }

        // return $request;
        $file = $request->file('image');
        $nombre = e($request->input('name'));
        $image_path_bd = $product->image_path;

        // return $file;
        if ($request->hasFile('image')) {
            if (isset($image_path_bd)) {
                // esto va bien
                Storage::disk('public')->delete('products/' . $product->image_path);
            }

            $rules = [
                'name' => 'required|string|max:100',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|numeric',
                'description' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ];

            if (!Storage::disk('public')->exists('products')) {
                Storage::disk('public')->makeDirectory('products', 0775, true);
            }
            $slug = Str::slug($nombre, '-');
            $fileExt = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $fileName = rand(1, 9999) . '-' . Str::slug($nombre) . '.' . $fileExt;
            $final_file = $request->getSchemeAndHttpHost() . '/products/' . $fileName;
            $filesystem = Storage::disk('public');
            $filesystem->putFileAs('products', $file, $fileName);

            $product->image = $final_file;
            $product->image_path = $fileName;
        } else {
            $rules = [
                'name' => 'required|string|max:100',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|numeric',
                'description' => 'required|string|max:255',
                'image' => 'required|string|max:2048',
            ];

            $product->image = $request->input('image');
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errores = [
                'status' => 400,
                'messages' => 'Oops we have detected errors',
                'errors' => $validator->errors(),
            ];

            return response()->json($errores, 400);
        }

        // Guarda la ruta del archivo en la base de datos o realiza otra acción necesaria
        $product->name = Str::headline($nombre);
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->description = $request->input('description');
        $product->category_id = $request->input('category_id');

        if ($product->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Product updated successfully',
                'data' => $product,
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to update the product',
            ], 400);
        }

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
        $image_path_bd = $product->image_path;
        if (isset($image_path_bd)) {
            // esto va bien
            Storage::disk('public')->delete('products/' . $product->image_path);
        }
        $product->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
