<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Application;

/**
 * @OA\Info(
 *             title="Api Inventario",
 *             version="1.0",
 *             description="API Inventario del proyecto 3 de TryCatch"
 * )
 *
 * @OA\Server(url="http://127.0.0.1:8000")
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function version()
    {
        $data = [
            'laravel_version' => app()->version(),
            'php_version' => phpversion()
        ];

        return response()->json($data);
    }
}
