<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
     *    @OA\Post(
     *       path="/user",
     *       tags={"User"},
     *       operationId="store-user",
     *       summary="Add User",
     *       description="Add new user",
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  ref="#/components/schemas/UserCreateRequest"
     *              )
     *          )
     *     ),
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
    public function store(UserCreateRequest $request) : JsonResponse
    {
        $file_url = null;
        if($request->hasFile('image')){
            $request->validate([
                'image' => 'required|mimes:jpg,jpeg,png,bmp |max:4096',
            ]);
            $hash = Str::random(30);
            $extension = '.'.$request->file('image')->guessExtension();
            $filenameClient = $hash.$extension;
            $request->file('image')->storeAs('images/users', $filenameClient, $disk = 'app-public');
            $file_path = Storage::disk('app-public')->path('images/users', true). '/'.$filenameClient;
            if (file_exists($file_path)) {
                $file_url = '/images/users'.'/'.$filenameClient;
            }
        }
        $user = new User;
        $user->id = Str::uuid();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->image = $file_url;
        $user->jabatan = $request->jabatan ?? null;
        $user->role = $request->role;
        $user->save();
        return $this->response(['record' => new UserResource($user)],"Record Saved",200);
    }

    /**
     *    @OA\Get(
     *       path="/user/{id}",
     *       tags={"User"},
     *       operationId="show-user",
     *       summary="Show User",
     *       description="Show user",
     *       @OA\Parameter(
     *          name="id",
     *          required=true,
     *          in="path",
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
    public function show(string $id) : JsonResponse
    {
        $user = User::whereId($id)->firstOrFail();
        $data['record'] = new UserResource($user);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     *    @OA\Post(
     *       path="/user/{id}",
     *       tags={"User"},
     *       operationId="update-user",
     *       summary="Update User",
     *       description="Update user by ID",
     *     @OA\Parameter(
     *          name="id",
     *          required=true,
     *          in="path",
     *          @OA\Property(
     *              type="string"
     *          ),
     *      ),
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  ref="#/components/schemas/UserUpdateRequest"
     *              )
     *          )
     *     ),
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
    public function update(UserUpdateRequest $request, string $id) : JsonResponse
    {
        $user = User::whereId($id)->firstOrFail();
        if (!Hash::check($request->old_password,$user->password)){
            return $this->response(null,"Invalid Password",403);
        }
        if ($request->password && $request->password != ""){
            $validator = Validator::make($request->all(),[
                "password" => "required|min:6|max:16|confirmed"
            ]);
            if ($validator->fails()){
                return $this->response($validator->getMessageBag(),"Invalid Entity",400);
            }
            $user->password = Hash::make($request->password);
        }
        $file_url = $user->image;
        if($request->hasFile('image')){
            $request->validate([
                'image' => 'required|mimes:jpg,jpeg,png,bmp |max:4096',
            ]);
            $hash = Str::random(30);
            $extension = '.'.$request->file('image')->guessExtension();
            $filenameClient = $hash.$extension;
            $request->file('image')->storeAs('images/users', $filenameClient, $disk = 'app-public');
            $file_path = Storage::disk('app-public')->path('images/users', true). '/'.$filenameClient;
            if (file_exists($file_path)) {
                $file_url = '/images/users'.'/'.$filenameClient;
            }
        }
        $user->name = $request->name;
        $user->username = $request->username;
        $user->phone = $request->phone;
        $user->image = $file_url;
        $user->jabatan = $request->jabatan ?? null;
        $user->role = $request->role;
        $user->save();
        $data['record'] = new UserResource($user);
        return $this->response($data,"Data Updated",200);
    }

    /**
     *    @OA\Delete(
     *       path="/user/{id}",
     *       tags={"User"},
     *       operationId="delete-user",
     *       summary="Delete User",
     *       description="Delete user by ID",
     *       @OA\Parameter(
     *          name="id",
     *          required=true,
     *          in="path",
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
    public function destroy(string $id) : JsonResponse
    {
        $user = User::whereId($id)->firstOrFail();
        $user->delete();
        return $this->response(null,"Record Deleted",200);
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
