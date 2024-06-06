<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     *    @OA\Get(
     *       path="/user",
     *       tags={"User"},
     *       operationId="index-user",
     *       summary="User",
     *       description="Get All User",
     *     @OA\Parameter(
     *          name="search",
     *          required=false,
     *          description="name || phone",
     *          in="query",
     *          @OA\Property(
     *              type="string"
     *          ),
     *      ),
     *     @OA\Parameter(
     *          name="value",
     *          required=false,
     *          description="value for search",
     *          in="query",
     *          @OA\Property(
     *              type="string"
     *          ),
     *      ),
     *     @OA\Parameter(
     *          name="sort",
     *          required=false,
     *          description="name || phone || username",
     *          in="query",
     *          @OA\Property(
     *              type="string"
     *          ),
     *      ),
     *     @OA\Parameter(
     *          name="order",
     *          required=false,
     *          description="asc || desc",
     *          in="query",
     *          @OA\Property(
     *              type="string"
     *          ),
     *      ),
     *     @OA\Response(
     *           response="200",
     *           description="Success",
     *           @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     @OA\Response(
     *           response="500",
     *           description="Failure",
     *           @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     security={
     *          {"Bearer": {}}
     *      }
     * )
     */
    public function index(Request $request) : JsonResponse
    {
        $search = $request->get("search");
        $value = $request->get("value");
        $sort = $request->get("sort") ?? "created_at";
        $order = $request->get("order") ?? "desc";
        $records = User::where(function ($q) use ($search,$value){
            if (($search && $search != "") && ($value && $value != "")){
                switch ($search){
                    case 'phone':
                        $q->where('phone','like','%'.$value.'%');
                        break;
                    case 'name':
                        $q->where('name','like','%'.$value.'%');
                        break;
                }
            }
        })->orderBy($sort,$order)->get();
        $data['records'] = UserResource::collection($records);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     *    @OA\Post(
     *       path="/login",
     *       tags={"Auth"},
     *       operationId="auth",
     *       summary="Login",
     *       description="Login",
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  ref="#/components/schemas/AuthRequest"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *           response="200",
     *           description="Success",
     *           @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     )
     * )
     */
    public function auth(AuthRequest $request) : JsonResponse
    {
        $user = User::wherePhone($request->phone)->first();
        if (!$user){
            return $this->response(null,"User Not Found",404);
        }

        if (!Hash::check($request->password,$user->password)){
            return $this->response(null,"Invalid Credential",401);
        }

        $userTokens = $user->tokens;
        if ($userTokens != null) {
            foreach ($userTokens as $token) {
                $token->revoke();
                $token->delete();
            }
        }

        $scope = [];

        $tokenResult = $user->createToken('Personal Access Token '.$user->name,$scope);
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(7);
        $token->save();

        $access['access_token'] = $tokenResult->accessToken;
        $access['token_type'] = 'Bearer';
        $access['expires_in'] = $token->expires_at->getTimestamp();
        $data['token'] = $access;
        return $this->response($data,"Authenticated",200);
    }

    /**
     *    @OA\Post(
     *       path="/validate-token",
     *       tags={"Auth"},
     *       operationId="validateToken",
     *       summary="Validate Token",
     *       description="Validate Token",
     *     @OA\Response(
     *           response="200",
     *           description="Success",
     *           @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     @OA\Response(
     *           response="500",
     *           description="Failure",
     *           @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     security={
     *          {"Bearer": {}}
     *      }
     *  )
     */
    public function validateToken(Request $request) : JsonResponse
    {
        $user = $request->user();
        $data['user'] = new UserResource(User::whereId($user->id)->firstOrFail());
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     *    @OA\Delete(
     *       path="/logout",
     *       tags={"Auth"},
     *       operationId="logout",
     *       summary="Logout",
     *       description="Logout",
     *     @OA\Response(
     *           response="200",
     *           description="Success",
     *           @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     @OA\Response(
     *           response="500",
     *           description="Failure",
     *           @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     security={
     *          {"Bearer": {}}
     *      }
     *  )
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->token()->revoke();
        $user->token()->delete();

        $userTokens = $user->tokens;
        foreach ($userTokens as $token) {//revoke and delete all user token
            $token->revoke();
            $token->delete();
        }
        return $this->response(null, "Logout success",200);
    }
}
