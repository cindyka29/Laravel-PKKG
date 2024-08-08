<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResources;
use App\Models\Activity;
use App\Models\Images;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $activities = Activity::with(["program","documentations"])->get();
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
        $activity->program_id = $request->program_id;
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
        $activity = Activity::with(["program","documentations"])->findOrFail($id);
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
        $activity->program_id = $request->program_id;
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

    /**
     *    @OA\Get(
     *       path="/activity-date",
     *       tags={"Acitvity"},
     *       operationId="index-activity-date",
     *       summary="Activity Per Date",
     *       description="Get All Activity By Date",
     *     @OA\Parameter(
     *          name="activity_date",
     *          required=true,
     *          description="Y-m-d",
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
    public function getByDate(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'activity_date' => 'required|date_format:Y-m-d'
        ]);
        if($validator->fails()) {
            return $this->response($validator->getMessageBag(),"Invalid input data",400);
        }
        $activity = Activity::whereDate($request->activity_date)->get();
        $data['records'] = ActivityResources::collection($activity);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     *    @OA\Post(
     *       path="/activity/documentation",
     *       tags={"Acitvity"},
     *       operationId="store-activity-documentation",
     *       summary="Activity Documentation",
     *       description="Add New Activity Documentation",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"activity_id","name","image"},
     *               @OA\Property(property="activity_id", type="string"),
     *               @OA\Property(property="name", type="string"),
     *               @OA\Property(property="image", format="binary", type="string"),
     *            ),
     *        ),
     *    ),
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
    public function addDocumentation(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'activity_id' => 'required',
            "name" => "required",
            'image' => 'required|mimes:jpg,jpeg,png,bmp|max:4096'
        ]);
        if ($validator->fails()){
            return $this->response($validator->getMessageBag(),"Invalid entity",422);
        }

        $activity = Activity::whereId($request->activity_id)->firstOrFail();

        $file_url = null;
        if($request->hasFile('image')){
            $request->validate([
                'image' => 'required|mimes:jpg,jpeg,png,bmp |max:4096',
            ]);
            $hash = Str::random(30);
            $extension = '.'.$request->file('image')->guessExtension();
            $filenameClient = $hash.$extension;
            $request->file('image')->storeAs('images/documentations', $filenameClient, $disk = 'app-public');
            $file_path = Storage::disk('app-public')->path('images/documentations', true). '/'.$filenameClient;
            if (file_exists($file_path)) {
                $file_url = '/images/documentations'.'/'.$filenameClient;
            }
        }
        if ($file_url !== null){
            $image = new Images;
            $image->name = $request->name;
            $image->url = $file_url;
            $activity->documentations()->save($image);
        }
        return $this->response(null,"Image Saved",200);
    }

    /**
     * @param $image_id
     * @return JsonResponse
     * @OA\Delete (
     *       path="/activity/documentation/{image_id}",
     *       tags={"Acitvity"},
     *       operationId="store-activity-documentation-delete",
     *       summary="Delete Activity Documentation",
     *       description="Delete Activity Documentation",
     *     @OA\Parameter(
     *          name="image_id",
     *          required=true,
     *          description="Image ID",
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
    public function deleteDocumentation($image_id) : JsonResponse
    {
        $image = Images::whereId($image_id)->firstOrFail();
        $image->delete();
        return $this->response(null,"Image Deleted",200);
    }
}
