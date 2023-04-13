<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categories::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest  $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        // TODO: Falta ver mensajes de error de la validaciones. Funciona pero no devuelve un json de mensajes
        $category = Categories::create($request->all());
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $category = Categories::find($id);
        if ($category == null) {
            $msg = [
                "mensaje" => "No hay ninguna categoria con el id ingresado, ingrese otro por favor",
            ];
            return response()->json($msg, 404);
        }
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $category = Categories::find($id);

        if ($category == null) {
            $msg = [
                "mensaje" => "No hay ninguna categoria con el id ingresado, ingrese otro por favor",
            ];
            return response()->json($msg, 404);
        }

        // TODO: Falta ver mensajes de error de la validaciones. Funciona pero no devuelve un json de mensajes

        $request->validate( [
            'name' => 'required',
        ]);

        // if ($validator->fails()) {
        //     $errors = $validator->errors();

        //     $response = response()->json([
        //         'message' => 'Invalid data send',
        //         'details' => $errors->messages(),
        //     ], 422);
        // }


        $category->update($request->all());
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Categories::findOrFail($id);
        $category->delete();
        return response()->json($category);
    }
}
