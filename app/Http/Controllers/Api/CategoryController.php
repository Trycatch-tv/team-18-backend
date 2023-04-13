<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categories::orderBy('id', 'DESC')->paginate(15);
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $messages = [
            'name.required' => 'Se requiere un Nombre para la Categoría',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = [
                'status' => 400,
                'message' => 'Oops we have detected errors',
                'errors' => $validator->errors(),
            ];

            return response()->json($errors, 400);
        }

        $category = Categories::create($request->all());
        return response()->json([
            'status' => 201,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $category = Categories::orderBy('id', 'DESC')->find($id);
        if ($category == null) {
            return response()->json([
                'status' => 400,
                'message' => "There is no category with the id entered, please enter another one",
            ], 400);
        }
        return response()->json([
            'status' => 200,
            'data' => $category,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $category = Categories::orderBy('id', 'DESC')->find($id);

        return response()->json([
            'status' => 400,
            'message' => "There is no category with the id entered, please enter another one",
        ], 400);

        // Ok: Validacion lista

        $rules = [
            'name' => 'required',
        ];

        $messages = [
            'name.required' => 'Se requiere un Nombre para la Categoría',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = [
                'status' => 400,
                'message' => 'Oops we have detected errors',
                'errors' => $validator->errors(),
            ];

            return response()->json($errors, 400);
        }

        $category->update($request->all());
        return response()->json([
            'status' => 200,
            'message' => 'Category updated successfully',
            'data' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Categories::find($id);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => "There is no category with the id entered, please enter another one",
            ], 400);
        }

        $category->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
