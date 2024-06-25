<?php

namespace App\Http\Controllers;

use App\Http\Requests\IuranRequest;
use App\Http\Resources\ActivityResources;
use App\Http\Resources\IuranResource;
use App\Http\Resources\UserResource;
use App\Models\Activity;
use App\Models\Iuran;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IuranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
//    public function index()
//    {
//        //
//    }

    /**
     * @param IuranRequest $request
     * @return JsonResponse
     * @OA\Post (
     *     path="/iuran",
     *     tags={"Iuran"},
     *     operationId="store-iuran",
     *     summary="Add new Iuran",
     *     description="Add new Iuran",
     *     @OA\RequestBody (
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema (
     *                  type="object",
     *                  ref="#/components/schemas/IuranRequest"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Failure",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     security={
     *          {"Bearer": {}}
     *     }
     * )
     */
    public function store(IuranRequest $request) : JsonResponse
    {
        $iuran = new Iuran;
        $iuran->id = Str::uuid();
        $iuran->user_id = $request->user_id;
        $iuran->activity_id = $request->activity_id;
        $iuran->is_paid = $request->is_paid;
        $iuran->save();

        return $this->response(['record'=>new IuranResource($iuran)],"Record Saved",200);
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @OA\Get  (
     *     path="/iuran/{id}",
     *     tags={"Iuran"},
     *     operationId="show-iuran",
     *     summary="Get Iuran by ID",
     *     description="Get Iuran by ID",
     *     @OA\Parameter (
     *          name="id",
     *          required=true,
     *          description="ID",
     *          in="path",
     *          @OA\Schema (
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Failure",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     security={
     *          {"Bearer": {}}
     *     }
     * )
     */
    public function show(string $id) : JsonResponse
    {
        $iuran = Iuran::with(["user","activity"])->firstOrFail($id);
        return $this->response(["record" => new IuranResource($iuran)],"Data Retrieved",200);
    }

    /**
     * @param IuranRequest $request
     * @param string $id
     * @return JsonResponse
     * @OA\Put  (
     *     path="/iuran/{id}",
     *     tags={"Iuran"},
     *     operationId="update-iuran",
     *     summary="Update Iuran by ID",
     *     description="Update Iuran by ID",
     *     @OA\Parameter (
     *          name="id",
     *          required=true,
     *          description="ID",
     *          in="path",
     *          @OA\Schema (
     *              type="string"
     *          ),
     *     ),
     *     @OA\RequestBody (
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema (
     *                  type="object",
     *                  ref="#/components/schemas/IuranRequest"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Failure",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     security={
     *          {"Bearer": {}}
     *     }
     * )
     */
    public function update(IuranRequest $request, string $id) : JsonResponse
    {
        $iuran = Iuran::whereId($id)->firstOrFail();
        $iuran->user_id = $request->user_id;
        $iuran->activity_id = $request->activity_id;
        $iuran->is_present = $request->is_present;
        $iuran->save();

        return $this->response(['record' => new IuranResource($iuran)], "Data Updated", 200);
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @OA\Delete   (
     *     path="/iuran/{id}",
     *     tags={"Iuran"},
     *     operationId="delete-iuran",
     *     summary="Delete Iuran by ID",
     *     description="Delete Iuran by ID",
     *     @OA\Parameter (
     *          name="id",
     *          required=true,
     *          description="ID",
     *          in="path",
     *          @OA\Schema (
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Failure",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     security={
     *          {"Bearer": {}}
     *     }
     * )
     */
    public function destroy(string $id) : JsonResponse
    {
        $iuran = Iuran::whereId($id)->firstOrFail();
        $iuran->delete();

        return $this->response(null,"Data Deleted",200);
    }

    /**
     * @param $activity_id
     * @return JsonResponse
     * @OA\Get   (
     *     path="/iuran/{activity_id}/activity",
     *     tags={"Iuran"},
     *     operationId="show-user-iuran",
     *     summary="Get User Iuran by Activity ID",
     *     description="Get User Iuran by Activity ID",
     *     @OA\Parameter (
     *          name="activity_id",
     *          required=true,
     *          description="Activity ID",
     *          in="path",
     *          @OA\Schema (
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Failure",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     security={
     *          {"Bearer": {}}
     *     }
     * )
     */
    public function getUserIuranByActivity($activity_id) : JsonResponse
    {
        $activity = Activity::whereId($activity_id)->firstOrFail();
        $absences = Iuran::with(["user"])->where('activity_id','=',$activity_id)->get();
        $data['users'] = IuranResource::collection($absences);
        $data['activity'] = new ActivityResources($activity);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     * @param $activity_id
     * @return JsonResponse
     * @OA\Get   (
     *     path="/iuran/not/{activity_id}/activity",
     *     tags={"Iuran"},
     *     operationId="show-user-not-iuran",
     *     summary="Get User Not Iuran by Activity ID",
     *     description="Get User Not Iuran by Activity ID",
     *     @OA\Parameter (
     *          name="activity_id",
     *          required=true,
     *          description="Activity ID",
     *          in="path",
     *          @OA\Schema (
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Failure",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/ResponseSchema"),
     *     ),
     *     security={
     *          {"Bearer": {}}
     *     }
     * )
     */
    public function getUserNotIuranByActivity($activity_id) : JsonResponse
    {
        $activity = Activity::whereId($activity_id)->firstOrFail();
        $user_id = Iuran::whereActivityId($activity_id)->get()->pluck("user_id")->toArray();
        $user = User::where('role','!=','admin')->whereNotIn("id",$user_id)->get();
        $data['users'] = UserResource::collection($user);
        $data['activity'] = new ActivityResources($activity);
        return  $this->response($data,"Data Retrieved",200);
    }
}
