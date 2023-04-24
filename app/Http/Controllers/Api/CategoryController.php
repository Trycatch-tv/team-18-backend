<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('verified');
    }

    /**
     * List all category records
     * @OA\Get (
     * security={{"Bearer":{}}},
     *     path="/api/categories",
     *     tags={"Category"},
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *                    @OA\Property(
     *                         property="current_page",
     *                         type="number",
     *                         example="1"
     *                     ),
     *             @OA\Property(
     *                 type="array",
     *                 property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="TV 50' Samsung"
     *                     ),
     *                     @OA\Property(
     *                         property="user",
     *                         type="array",
     *                         @OA\Items(
     *                         type="object",     *
     *                              @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                                   ),
     *                    @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Aderson Jara"
     *                     ),
     *                    @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="andersonjara@gmail.com"
     *                     ),)
     *                     ),
     *                 )
     *             ),
	 *                    @OA\Property(
     *                         property="first_page_url",
     *                         type="string",
     *                         example="https://api-inventario.onrender.com/api/categories?page=1"
     *                     ),
	 *                    @OA\Property(
     *                         property="from",
     *                         type="number",
     *                         example="1"
     *                     ),
	 *                    @OA\Property(
     *                         property="last_page",
     *                         type="number",
     *                         example="1"
     *                     ),
	 *                    @OA\Property(
     *                         property="last_page_url",
     *                         type="string",
     *                         example="https://api-inventario.onrender.com/api/categories?page=1"
     *                     ),
     *             @OA\Property(
     *                 type="array",
     *                 property="links",
	 *					example={{
	 *                  "url": "null",
	 *                  "label": "&laquo; Previous",
	 *                  "active": false,
	 *                  },{
	 *                  "url": "https://localhost:8000/api/categories?page=1",
	 *                  "label": "1",
	 *                  "active": true,
	 *                  },{
	 *                  "url": "null",
	 *                  "label": "Next &raquo;",
	 *                  "active": false,
	 *                  }},
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="url",
     *                         type="string",
     *                         example="null"
     *                     ),
	 *                     @OA\Property(
     *                         property="label",
     *                         type="string",
     *                         example="&laquo; Previous"
     *                     ),
	 *                     @OA\Property(
     *                         property="active",
     *                         type="boolean",
     *                         example="false"
     *                     ),
     *                 ),
     *             ),
	 *                    @OA\Property(
     *                         property="next_page_url",
     *                         type="string",
     *                         example="null"
     *                     ),
	 *                    @OA\Property(
     *                         property="path",
     *                         type="string",
     *                         example="https://api-inventario.onrender.com/api/categories"
     *                     ),
	 *                    @OA\Property(
     *                         property="per_page",
     *                         type="number",
     *                         example="15"
     *                     ),
	 *                    @OA\Property(
     *                         property="prev_page_url",
     *                         type="string",
     *                         example="null"
     *                     ),
	 *                    @OA\Property(
     *                         property="to",
     *                         type="number",
     *                         example="7"
     *                     ),
	 *                    @OA\Property(
     *                         property="total",
     *                         type="number",
     *                         example="7"
     *                     ),
     *         )
     *     ),
     *  @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Unauthenticated"
     *                     ),
     *                 )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = Categories::with('user')->orderBy('id', 'DESC')->paginate(15);
        return response()->json($categories, 200);
    }

    /**
     * Register the information of a category
     * @OA\Post (
     * security={{"Bearer":{}}},
     *     path="/api/categories",
     *     tags={"Category"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"Aderson Lara"
     *                }
     *             )
     *         )
     *      ),
     *           @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *                    @OA\Property(
     *                         property="succes",
     *                         type="number",
     *                         example="200"
     *                     ),
     *             @OA\Property(
     *                 type="array",
     *                 property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Aderson Jara"
     *                     ),
     *                     @OA\Property(
     *                         property="user",
     *                         type="array",
     *                         @OA\Items(
     *                         type="object",     *
     *                              @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                                   ),
     *                    @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Aderson Jara"
     *                     ),
     *                    @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="andersonjara@gmail.com"
     *                     ),)
     *                     ),
     *                 )
     *             )
     *         )
     *     ),
     *   @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
	 *						@OA\Property(
     *                         property="status",
     *                         type="number",
     *                         example="400"
     *                     ),
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Oops we have detected errors"
     *                     ),
	 * @OA\Property(
     *                 type="array",
     *                 property="errors",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="name",
     *                         type="array",
     *                         @OA\Items(
     *                          example="The name field is required."
     *                          )
     *                     ),
     *                 )
     *             )
     *                 )
     *         ),
     *
     *      @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Unauthenticated"
     *                     ),
     *                 )
     *         )
     * )
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

        $category = new Categories();
        $category->name = e($request->input('name'));
        $category->user_id = auth()->user()->id;

        if ($category->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Category created successfully',
                'data' => $category,
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to update the category',
            ], 400);
        }
    }

    /**
     * Displays the information of a category
     * @OA\Get (
     * security={{"Bearer":{}}},
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *                    @OA\Property(
     *                         property="succes",
     *                         type="number",
     *                         example="200"
     *                     ),
     *             @OA\Property(
     *                 type="array",
     *                 property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Aderson Jara"
     *                     ),
     *                     @OA\Property(
     *                         property="user",
     *                         type="array",
     *                         @OA\Items(
     *                         type="object",     *
     *                              @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                                   ),
     *                    @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Aderson Jara"
     *                     ),
     *                    @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="andersonjara@gmail.com"
     *                     ),)
     *                     ),
     *                 )
     *             )
     *         )
     *     ),
     *  @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
	 *						@OA\Property(
     *                         property="status",
     *                         type="number",
     *                         example="404"
     *                     ),
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="There is no category with the id entered, please enter another one"
     *                     ),
     *                 )
     *         ),
	 @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Unauthenticated"
     *                     ),
     *                 )
     *         )
     *     )
     */
    public function show(int $id)
    {
        $category = Categories::with('user')->orderBy('id', 'DESC')->find($id);
        if ($category == null) {
            return response()->json([
                'status' => 404,
                'message' => "There is no category with the id entered, please enter another one",
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'data' => $category,
        ], 200);
    }

    /**
     * Update the information of a category
     * @OA\Put (
     *     security={{"Bearer":{}}},
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="number")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *                    @OA\Property(
     *                         property="succes",
     *                         type="number",
     *                         example="200"
     *                     ),
     *             @OA\Property(
     *                 type="array",
     *                 property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Aderson Jara"
     *                     ),
     *                     @OA\Property(
     *                         property="user",
     *                         type="array",
     *                         @OA\Items(
     *                         type="object",     *
     *                              @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                                   ),
     *                    @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Aderson Jara"
     *                     ),
     *                    @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="andersonjara@gmail.com"
     *                     ),
     *                     ),
	 ),
     *                 )
     *             )
     *         )
     *     ),
     *  @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
	 *						@OA\Property(
     *                         property="status",
     *                         type="number",
     *                         example="400"
     *                     ),
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Oops we have detected errors"
     *                     ),
	 @OA\Property(
     *                 type="array",
     *                 property="errors",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="name",
     *                         type="array",
     *                         @OA\Items(
     *                          example="The name field is required."
     *                          )
     *                     ),
     *                 )
     *             )
	 )
	 ),
     *
     *   @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
	 *						@OA\Property(
     *                         property="status",
     *                         type="number",
     *                         example="404"
     *                     ),
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="There is no category with the id entered, please enter another one"
     *                     ),
     *                 )
     *         ),
     *
     *  @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Unauthenticated"
     *                     ),
     *                 )
     *         )
     * )
     */
    public function update(Request $request, int $id)
    {
        $category = Categories::orderBy('id', 'DESC')->find($id);
        if ($category == null) {
            return response()->json([
                'status' => 404,
                'message' => "There is no category with the id entered, please enter another one",
            ], 404);
        }

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

        $category->name = e($request->input('name'));
        $category->user_id = auth()->user()->id;

        if ($category->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Category created successfully',
                'data' => $category,
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to update the category',
            ], 400);
        }
    }

    /**
     * Delete the information of a category
     * @OA\Delete (
     *     security={{"Bearer":{}}},
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *  @OA\JsonContent(
	 *						@OA\Property(
     *                         property="status",
     *                         type="number",
     *                         example="200"
     *                     ),
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Category deleted successfully"
     *                     ),
     *                 )
     *     ),
     *   @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
	 *						@OA\Property(
     *                         property="status",
     *                         type="number",
     *                         example="404"
     *                     ),
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="There is no category with the id entered, please enter another one"
     *                     ),
     *                 )
     *         ),
     *   @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="Unauthenticated"
     *                     ),
     *                 )
     *         ),
     * )
     */
    public function destroy(string $id)
    {
        $category = Categories::find($id);
        if ($validator->fails()) {
            return response()->json([
                'status' => 404,
                'message' => "There is no category with the id entered, please enter another one",
            ], 404);
        }

        $category->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
