<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsenceRequest;
use App\Http\Resources\AbsenceResource;
use App\Http\Resources\ActivityResources;
use App\Http\Resources\UserResource;
use App\Models\Absence;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AbsenceController extends Controller
{
    /**
     * @param $user_id
     * @return JsonResponse
     * @OA\Get  (
     *     path="/absence/user/{user_id}",
     *     tags={"Absence"},
     *     operationId="show-absence-user",
     *     summary="Get Absence by User ID",
     *     description="Get Absence by User ID",
     *     @OA\Parameter (
     *          name="user_id",
     *          required=true,
     *          description="User ID",
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
    public function index($user_id) : JsonResponse
    {
        $users = Absence::with("activity")->where("user_id",'=',$user_id)->get();
        $data['records'] = AbsenceResource::collection($users);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     * @param AbsenceRequest $request
     * @return JsonResponse
     * @OA\Post (
     *     path="/absence",
     *     tags={"Absence"},
     *     operationId="store-absence",
     *     summary="Add new Absence",
     *     description="Add new Absence",
     *     @OA\RequestBody (
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema (
     *                  type="object",
     *                  ref="#/components/schemas/AbsenceRequest"
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
    public function store(AbsenceRequest $request) : JsonResponse
    {
        $absence = new Absence;
        $absence->id = Str::uuid();
        $absence->user_id = $request->user_id;
        $absence->activity_id = $request->activity_id;
        $absence->is_attended = $request->is_attended;
        $absence->save();

        return $this->response(["record" => new AbsenceResource($absence)],"Record Saved",200);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @OA\Get  (
     *     path="/absence/{id}",
     *     tags={"Absence"},
     *     operationId="show-absence",
     *     summary="Get Absence by ID",
     *     description="Get Absence by ID",
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
    public function show($id) : JsonResponse
    {
        $absence = Absence::with(["user","activity"])->findOrFail($id);
        return $this->response(["record"=>new AbsenceResource($absence)],"Data Retrieved",200);
    }

    /**
     * @param AbsenceRequest $request
     * @param $id
     * @return JsonResponse
     * @OA\Put  (
     *     path="/absence/{id}",
     *     tags={"Absence"},
     *     operationId="update-absence",
     *     summary="Update Absence by ID",
     *     description="Update Absence by ID",
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
     *                  ref="#/components/schemas/AbsenceRequest"
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
    public function update(AbsenceRequest $request, $id) : JsonResponse
    {
        $absence = Absence::whereId($id)->firstOrFail();
        $absence->user_id = $request->user_id;
        $absence->activity_id = $request->activity_id;
        $absence->is_attended = $request->is_attended;
        $absence->save();

        return $this->response(["record"=>new AbsenceResource($absence)],"Record Updated",200);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @OA\Delete   (
     *     path="/absence/{id}",
     *     tags={"Absence"},
     *     operationId="delete-absence",
     *     summary="Delete Absence by ID",
     *     description="Delete Absence by ID",
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
    public function destroy($id) : JsonResponse
    {
        $absence = Absence::whereId($id)->firstOrFail();
        $absence->delete();

        return $this->response(null,"Record Deleted",200);
    }

    /**
     * @param $activity_id
     * @return JsonResponse
     * @OA\Get   (
     *     path="/absence/{activity_id}/activity",
     *     tags={"Absence"},
     *     operationId="show-user-absence",
     *     summary="Get User Absence by Activity ID",
     *     description="Get User Absence by Activity ID",
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
    public function getUserAbsentByActivity($activity_id) : JsonResponse
    {
        $activity = Activity::whereId($activity_id)->firstOrFail();
        $absences = Absence::with(["user"])->where('activity_id','=',$activity_id)->get();
        $user_id = Absence::whereActivityId($activity_id)->get()->pluck("user_id")->toArray();
        $allUser = User::where('role','!=','admin')->whereNotIn("id",$user_id)->get();
        $additional_user = [];
        foreach($allUser as $value){
            $additional_user[] = [
                "is_attended" => 0,
                "user_id" => $value->id,
                "user" => $value,
                "activity_id" => $activity_id,
                "updated_at" => null
            ];
        }

        $data['users'] = AbsenceResource::collection($absences);
        $data['activity'] = new ActivityResources($activity);
        $data['users'] = $data['users']->merge($additional_user);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     * @param $activity_id
     * @return JsonResponse
     * @OA\Get   (
     *     path="/absence/not/{activity_id}/activity",
     *     tags={"Absence"},
     *     operationId="show-user-not-absence",
     *     summary="Get User Not Absence by Activity ID",
     *     description="Get User Not Absence by Activity ID",
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
    public function getUserNotAbsentByActivity($activity_id) : JsonResponse
    {
        $activity = Activity::whereId($activity_id)->firstOrFail();
        $user_id = Absence::whereActivityId($activity_id)->get()->pluck("user_id")->toArray();
        $user = User::where('role','!=','admin')->whereNotIn("id",$user_id)->get();
        $data['users'] = UserResource::collection($user);
        $data['activity'] = new ActivityResources($activity);
        return  $this->response($data,"Data Retrieved",200);
    }
}
