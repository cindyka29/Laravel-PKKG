<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0",
 *      title="Swagger UI",
 *      description="Swagger UI"
 * )
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST_API
 * )
 * @OA\SecurityScheme(
 *   securityScheme="Bearer",
 *   type="apiKey",
 *   name="Authorization",
 *   in="header"
 * )
 */
abstract class Controller
{
    public function response($data,$message,$code): \Illuminate\Http\JsonResponse
    {
        $response['response'] = array('status' => $code, 'message' => $message);
        $response['data'] = $data;
        return response()->json($response, $code);
    }
}
