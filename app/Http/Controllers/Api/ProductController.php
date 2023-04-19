<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('verified');

    }

	/**
     * List all products records
     * @OA\Get (
     * security={{"Bearer":{}}},
     *     path="/api/products",
     *     tags={"Product"},
     *     @OA\Response(response=200, description="Ok",
     *         @OA\JsonContent(
     *                    @OA\Property(property="current_page", type="number", example="1"),
     *             @OA\Property(type="array", property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="number", example="1"),
     *                     @OA\Property(property="name", type="string", example="Product name"),
     *                     @OA\Property(property="description", type="string", example="Product description"),
     *                     @OA\Property(property="price", type="number", format="float", example=10.99),
     *                     @OA\Property(property="stock", type="integer", example=10),
     *                     @OA\Property(property="image", type="string", example="http://example.com/products/product.jpg"),
	 *                     @OA\Property(
     *                         property="category",
     *                         type="array",
     *                         @OA\Items(
     *                         type="object",
     *                              @OA\Property(property="id",type="number", example="1"),
     *                              @OA\Property(property="name", type="string", example="Category name"),
     *                          )
     *                     ),
     *                     @OA\Property(property="user", type="array",
     *                         @OA\Items(
     *                         type="object",
     *                              @OA\Property(property="id", type="number", example="1"),
     *                              @OA\Property(property="name", type="string", example="User name"),
     *                              @OA\Property(property="email", type="string", example="user@email.com"),)
     *                         ),
     *                 )
     *             ),
	 *                    @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/products?page=1"),
	 *                    @OA\Property(property="from", type="number", example="1"),
	 *                    @OA\Property(property="last_page", type="number", example="2"),
	 *                    @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/api/products?page=2"),
     *                    @OA\Property(type="array", property="links", example={{"url": "null", "label": "&laquo; Previous", "active": false,},{"url": "http://localhost:8000/api/products?page=1", "label": "1", "active": true,},{"url": "http://localhost:8000/api/products?page=2", "label": "2", "active": false,},{"url": "http://localhost:8000/api/products?page=2","label": "Next &raquo;", "active": false}},
     *                     @OA\Items(
     *                        type="object",
        *                     @OA\Property(property="url", type="string", example="null"),
        *                     @OA\Property(property="label", type="string", example="&laquo; Previous"),
        *                     @OA\Property(property="active", type="boolean", example="false"),
     *                     ),
     *             ),
	 *                    @OA\Property(property="next_page_url", type="string", example="http://127.0.0.1:8000/api/products?page=2"),
	 *                    @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/products"),
	 *                    @OA\Property(property="per_page", type="number", example="15"),
	 *                    @OA\Property(property="prev_page_url",  type="string", example="null"),
	 *                    @OA\Property(property="to", type="number", example="15"),
	 *                    @OA\Property(property="total", type="number", example="26"),
     *         )
     *     ),
     *   @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated")))
     *   )
     * )
     */
    public function index()
    {
        $products = Products::with('category')->with('user')->orderBy('id', 'DESC')->paginate(15);
        return response()->json($products, 200);
    }

    /**
     * Register the information of a product
     * @OA\Post(
     *     security={{"Bearer":{}}},
     *     path="/api/products",
     *     tags={"Product"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"image", "name", "description", "price", "stock", "category_id"},
     *                 @OA\Property(
     *                     property="image",
     *                     description="The image to upload",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *                @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                 ),
     *                @OA\Property(
     *                     property="price",
     *                     type="number",
     *                 ),
     *                  @OA\Property(
     *                     property="stock",
     *                     type="number",
     *                 ),
     *              @OA\Property(
     *                     property="category_id",
     *                     type="number",
     *                 ),
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "description", "price", "stock", "image", "category_id"},
     *                 @OA\Property(property="name", type="string", example="Product name"),
     *                 @OA\Property(property="description", type="string", example="Product description"),
     *                 @OA\Property(property="price", type="number", format="float", example=10.99),
     *                 @OA\Property(property="stock", type="integer", example=10),
     *                 @OA\Property(property="image", type="string", example="http://example.com/products/product.jpg"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *             )
     *     ),
     *     ),
     *        @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *                    @OA\Property(property="succes", type="number", example="200"),
     *             @OA\Property(
     *                 type="array",
     *                 property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="number", example="1"),
     *                     @OA\Property(property="name", type="string", example="Product name"),
     *                     @OA\Property(property="description", type="string", example="Product description"),
     *                     @OA\Property(property="price", type="number", format="float", example=10.99),
     *                     @OA\Property(property="stock", type="integer", example=10),
     *                     @OA\Property(property="image", type="string", example="http://example.com/products/product.jpg"),
     *                     @OA\Property(property="category", type="array",
     *                                  @OA\Items(
     *                                     type="object",
     *                                     @OA\Property(property="id", type="number", example="1"),
     *                                     @OA\Property(property="name", type="string", example="Category name"),
     *                                   ),
     *                                  ),
     *                     @OA\Property(
     *                         property="user",
     *                         type="array",
     *                         @OA\Items(
     *                         type="object",
     *                              @OA\Property(property="id", type="number", example="1"),
     *                              @OA\Property(property="name", type="string", example="User name"),
     *                              @OA\Property(property="email", type="string", example="user@email.com"),
     *                          )
     *                     ),
     *                 )
     *             )
     *         )
     *     ),
     *   @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *			  @OA\Property(property="status", type="number", example="400"),
     *                    @OA\Property(type="array", property="errors",
     *                    @OA\Property(property="message", type="string", example="Oops we have detected errors"),
     *                           @OA\Items(type="object",
     *                                  @OA\Property(property="name", type="array",
     *                                      @OA\Items(example="The name field is required.")
     *                                  ),
     *                                 @OA\Property(property="price", type="array",
     *                                      @OA\Items(example="The price field is required.")
     *                                  ),
     * 	                               @OA\Property(property="category_id", type="array",
     *                                     @OA\Items(example="The category_id field is required.")
     *                                 ),
     * 	                               @OA\Property(property="stock", type="array",
     *                                     @OA\Items(example="The stock field is required.")
     *                               ),
     *                               @OA\Property(property="description", type="array",
     *                                     @OA\Items(example="The description field is required.")
     *                               ),
     * 	                            @OA\Property(property="image", type="array",
     *                                    @OA\Items(example="The image field is required.")
     *                              ),
     *                        )
     *                 )
     *           )
     *         ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *                    @OA\Property(property="message", type="string", example="Unauthenticated"),
     *                 )
     *         )
     * )
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
        $product->name = $nombre;
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->description = e($request->input('description'));
        $product->category_id = $request->input('category_id');
        $product->user_id = auth()->user()->id;

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
     * Displays the information of a product
     * @OA\Get (
     *     security={{"Bearer":{}}},
     *     path="/api/products/{id}",
     *     tags={"Product"},
     *     @OA\Parameter(in="path", name="id", required=true, @OA\Schema(type="number")),
     *     @OA\Response(response=200, description="Ok",
     *         @OA\JsonContent(
     *                    @OA\Property(property="succes", type="number", example="200"),
     *                    @OA\Property(type="array", property="data",
     *                    @OA\Items(
     *                        type="object",
     *                        @OA\Property(property="id", type="number", example="1"),
     *                        @OA\Property(property="name", type="string", example="Product name"),
	 *                        @OA\Property(property="description", type="string", example="Product description"),
	 *                        @OA\Property(property="stock", type="number", example="10"),
	 *                        @OA\Property(property="price", type="number", example="10.99"),
	 *                        @OA\Property(property="image", type="number", example="http://example.com/products/product-image.jpg"),
	 *                        @OA\Property(property="category", type="array",
     *                            @OA\Items(
     *                            type="object",
     *                                @OA\Property(property="id",type="number",example="1"),
     *                                @OA\Property(property="name", type="string", example="Category name"),
     *                             )
     *                        ),
     *                        @OA\Property(property="user", type="array",
     *                            @OA\Items(
     *                            type="object",
     *                                 @OA\Property(property="id", type="number", example="1"),
     *                                 @OA\Property(property="name", type="string", example="User name"),
     *                                 @OA\Property(property="email", type="string", example="user@email.com"),
     *                            )
     *                     ),
     *                 )
     *             )
     *         )
     *     ),
     *  @OA\Response(response=404, description="Not Found",
     *         @OA\JsonContent(
	 *			 @OA\Property(property="status", type="number", example="404"),
     *           @OA\Property(property="message", type="string", example="There is no product with the id entered, please enter another one"),
     *         )
     *  ),
	 * @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *  )
     *     )
     */
    public function show(string $id)
    {
        $product = Products::with('category')->with('user')->orderBy('id', 'DESC')->find($id);
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
     * Update the information of a  product
     * @OA\Post(
     *     security={{"Bearer":{}}},
     *     path="/api/products/{id}",
     *     tags={"Product"},
     *       @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"image", "name", "description", "price", "stock", "category_id"},
     *                 @OA\Property(
     *                     property="image",
     *                     description="The image to upload",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *                @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                 ),
     *                @OA\Property(
     *                     property="price",
     *                     type="number",
     *                 ),
     *                  @OA\Property(
     *                     property="stock",
     *                     type="number",
     *                 ),
     *              @OA\Property(
     *                     property="category_id",
     *                     type="number",
     *                 ),
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "description", "price", "stock", "image", "category_id"},
     *                 @OA\Property(property="name", type="string", example="Product name"),
     *                 @OA\Property(property="description", type="string", example="Product description"),
     *                 @OA\Property(property="price", type="number", format="float", example=10.99),
     *                 @OA\Property(property="stock", type="integer", example=10),
     *                 @OA\Property(property="image", type="string", example="http://example.com/products/product.jpg"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *             )
     *     ),
     *     ),
     *        @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *                    @OA\Property(property="succes", type="number", example="200"),
     *             @OA\Property(
     *                 type="array",
     *                 property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="number", example="1"),
     *                     @OA\Property(property="name", type="string", example="Product name"),
     *                     @OA\Property(property="description", type="string", example="Product description"),
     *                     @OA\Property(property="price", type="number", format="float", example=10.99),
     *                     @OA\Property(property="stock", type="integer", example=10),
     *                     @OA\Property(property="image", type="string", example="http://example.com/products/product.jpg"),
     *                     @OA\Property(property="category", type="array",
     *                                  @OA\Items(
     *                                     type="object",
     *                                     @OA\Property(property="id", type="number", example="1"),
     *                                     @OA\Property(property="name", type="string", example="Category name"),
     *                                   ),
     *                                  ),
     *                     @OA\Property(
     *                         property="user",
     *                         type="array",
     *                         @OA\Items(
     *                         type="object",
     *                              @OA\Property(property="id", type="number", example="1"),
     *                              @OA\Property(property="name", type="string", example="User name"),
     *                              @OA\Property(property="email", type="string", example="user@email.com"),
     *                          )
     *                     ),
     *                 )
     *             )
     *         )
     *     ),
     *   @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *			  @OA\Property(property="status", type="number", example="400"),
     *                    @OA\Property(type="array", property="errors",
     *                    @OA\Property(property="message", type="string", example="Oops we have detected errors"),
     *                           @OA\Items(type="object",
     *                                  @OA\Property(property="name", type="array",
     *                                      @OA\Items(example="The name field is required.")
     *                                  ),
     *                                 @OA\Property(property="price", type="array",
     *                                      @OA\Items(example="The price field is required.")
     *                                  ),
     * 	                               @OA\Property(property="category_id", type="array",
     *                                     @OA\Items(example="The category_id field is required.")
     *                                 ),
     * 	                               @OA\Property(property="stock", type="array",
     *                                     @OA\Items(example="The stock field is required.")
     *                               ),
     *                               @OA\Property(property="description", type="array",
     *                                     @OA\Items(example="The description field is required.")
     *                               ),
     * 	                            @OA\Property(property="image", type="array",
     *                                    @OA\Items(example="The image field is required.")
     *                              ),
     *                        )
     *                 )
     *           )
     *         ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *                    @OA\Property(property="message", type="string", example="Unauthenticated"),
     *                 )
     *         )
     * )
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
            $fileName = rand(1, 9999) . '-' . $slug . '.' . $fileExt;
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
        $product->name = $nombre;
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->description = e($request->input('description'));
        $product->category_id = $request->input('category_id');
        $product->user_id = auth()->user()->id;

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
     * Delete the information of a product
     * @OA\Delete (
     *     security={{"Bearer":{}}},
     *     path="/api/products/{id}",
     *     tags={"Product"},
     *     @OA\Parameter(in="path", name="id", required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(response=200, description="Ok",
     *            @OA\JsonContent(
	 *						@OA\Property(property="status", type="number", example="200"),
     *                      @OA\Property(property="message", type="string", example="Product deleted successfully"),
     *            )
     *     ),
     *   @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
	 *						@OA\Property(property="status", type="number", example="404"),
     *                      @OA\Property(property="message", type="string", example="There is no product with the id entered, please enter another one"),
     *                 )
     *         ),
     *   @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *                    @OA\Property(property="message", type="string", example="Unauthenticated"),
     *                 )
     *         ),
     * )
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
