<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResources;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    /**
     *    @OA\Get(
     *       path="/activity",
     *       tags={"Acitvity"},
     *       operationId="index-activity",
     *       summary="Activity",
     *       description="Get All Activity",
     *     @OA\Parameter(
     *          name="limit",
     *          required=false,
     *          description="limit",
     *          in="query",
     *          @OA\Property(
     *              type="integer"
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
        $limit = $request->get("limit") ?? 10;
        $now = Carbon::now();
        $activities = Activity::where("date",">=",$now->format("Y-m-d"))->limit($limit)->get();
        $data['records'] = ActivityResources::collection($activities);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     *    @OA\Post(
     *       path="/activity",
     *       tags={"Acitvity"},
     *       operationId="store-activity",
     *       summary="Activity",
     *       description="Add New Activity",
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  ref="#/components/schemas/ActivityRequest"
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
    public function store(ActivityRequest $request) : JsonResponse
    {
        $activity = new Activity;
        $activity->id = Str::uuid();
        $activity->name = $request->name;
        $activity->date = $request->date;
        $activity->note = $request->note;
        $activity->save();

        $data['record'] = new ActivityResources($activity);
        return $this->response($data,"Data Saved",200);
    }

    /**
     *    @OA\Get(
     *       path="/activity/{id}",
     *       tags={"Acitvity"},
     *       operationId="show-activity",
     *       summary="Activity",
     *       description="Get Activity By ID",
     *     @OA\Parameter(
     *          name="id",
     *          required=true,
     *          description="ID",
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
    public function show($id) : JsonResponse
    {
        $activity = Activity::with("documentations")->findOrFail($id);
        $data['record'] = new ActivityResources($activity);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     *    @OA\Put(
     *       path="/activity/{id}",
     *       tags={"Acitvity"},
     *       operationId="update-activity",
     *       summary="Activity",
     *       description="Update Activity By ID",
     *     @OA\Parameter(
     *          name="id",
     *          required=true,
     *          description="ID",
     *          in="path",
     *          @OA\Property(
     *              type="string"
     *          ),
     *      ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  ref="#/components/schemas/ActivityRequest"
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
    public function update(ActivityRequest $request, $id) : JsonResponse
    {
        $activity = Activity::whereId($id)->firstOrFail();
        $activity->name = $request->name;
        $activity->date = $request->date;
        $activity->note = $request->note;
        $activity->save();

        $data['record'] = new ActivityResources($activity);
        return $this->response($data,"Data Updated",200);
    }

    /**
     *    @OA\Delete (
     *       path="/activity/{id}",
     *       tags={"Acitvity"},
     *       operationId="delete-activity",
     *       summary="Activity",
     *       description="Delete Activity By ID",
     *     @OA\Parameter(
     *          name="id",
     *          required=true,
     *          description="ID",
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
    public function destroy($id) : JsonResponse
    {
        $activity = Activity::with("documentations")->findOrFail($id);
        if($activity->documentations){
            foreach ($activity->documentations as $doc){
                $doc->delete();
            }
        }
        $activity->delete();
        return $this->response(null,"Activity Deleted",200);
    }

    /**
     *    @OA\Get(
     *       path="/activity-month",
     *       tags={"Acitvity"},
     *       operationId="index-activity-month",
     *       summary="Activity Per Month",
     *       description="Get All Activity Per Month",
     *     @OA\Parameter(
     *          name="month",
     *          required=true,
     *          description="Y-m",
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
    public function getByMonth(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            "month" => 'required|date_format:Y-m'
        ]);
        if ($validator->fails()){
            return $this->response($validator->getMessageBag(),"Invalid Input",400);
        }

        $activities = Activity::whereRaw("DATE_FORMAT(date,'%Y-%m') = '$request->month'")->get();
        $data['records'] = ActivityResources::collection($activities);

        return $this->response($data,"Data Retrieved",200);
    }
}
