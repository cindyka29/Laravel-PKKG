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
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\IuranExportXls;

class IuranController extends Controller
{
    /**
     * @param $user_id
     * @return JsonResponse
     * @OA\Get  (
     *     path="/iuran/user/{user_id}",
     *     tags={"Iuran"},
     *     operationId="show-iuran-user",
     *     summary="Get Iuran by User ID",
     *     description="Get Iuran by User ID",
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
        $users = Iuran::with("activity")->where("user_id",'=',$user_id)->get();
        $data['records'] = IuranResource::collection($users);
        return $this->response($data,"Data Retrieved",200);
    }

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
        $iuran->nominal = $request->nominal;
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
        $iuran->is_paid = $request->is_paid;
        $iuran->nominal = $request->nominal;
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
        $user_id = Iuran::with(["user"])->where('activity_id','=',$activity_id)->pluck("user_id")->toArray();

        $allUser = User::where('role','!=','admin')->whereNotIn("id",$user_id)->get();
        $additional_user = [];
        foreach($allUser as $value){
            $additional_user[] = [
                "id_iuran" => null,
                "is_paid" => 0,
                "nominal" => 0,
                "user_id" => $value->id,
                "user" => $value,
                "activity_id" => $activity_id,
                "updated_at" => null
            ];
        }

        $data['users'] = IuranResource::collection($absences);
        $data['activity'] = new ActivityResources($activity);
        $data['users'] = $data['users']->merge($additional_user);
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

    public function exportIuran(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    // public function exportIuran(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            "month" => "required|date_format:Y-m"
        ]);
        if ($validator->fails()){
            return $this->response($validator->getMessageBag(),"invalid input",400);
        }
        $records = Iuran::with('activity')
        ->whereHas('activity', function ($query) use ($request) {
            $query->whereYear('date', '=', Carbon::parse($request->month)->year)
                ->whereMonth('date', '=', Carbon::parse($request->month)->month);
        })
        ->get();
        $date = Carbon::createFromFormat("Y-m",$request->month)->format("F Y");
        return Excel::download(new IuranExportXls($date,$records),'Laporan-Iuran-'.$date.'.xlsx');
        // return  $this->response($records,"Data Retrieved",200);
    }

    public function getMonthIuran(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            "year" => "date_format:Y|nullable"
        ]);
        if($validator->fails()){
            return $this->response($validator->getMessageBag(),"Invalid Input",400);
        }
        $year = $request->year;
        $months = Iuran::selectRaw("DISTINCT(DATE_FORMAT(activities.date,'%M %Y')) as text, DATE_FORMAT(activities.date,'%Y-%m') as value")
        ->leftJoin('activities', 'activities.id', '=', 'iurans.activity_id')
        ->whereHas('activity', function ($query) use ($year) {
            $query->where(function($q) use ($year){
                if ($year && $year != ""){
                    $q->whereRaw("DATE_FORMAT(date,'%Y') = '$year'");
                }
            });
        })
        ->get();
        $data["records"] = $months;
        return $this->response($data,"Data Retrieved",200);
    }

    public function reportIuran(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            "start_date" => "nullable|date_format:Y-m-d",
        ]);
        if($validator->fails()){
            return $this->response($validator->getMessageBag(),"Invalid Input Data",400);
        }
        $start_date = $request->get("start_date") ?? null;
        if ($start_date !== null) {
            $validator = Validator::make($request->all(),[
                "end_date" => "required|date_format:Y-m-d"
            ]);
            if($validator->fails()){
                return $this->response($validator->getMessageBag(),"Invalid Input Data",400);
            }
        }
        $end_date = $request->get("end_date") ?? null;
        $query = Iuran::selectRaw("sum(nominal) as nominal, activities.date")
        ->leftJoin('activities', 'activities.id', '=', 'iurans.activity_id')
        ->leftJoin('kas', 'kas.activity_id', '=', 'activities.id')
        ->where(function($q) use ($start_date,$end_date){
            if ($start_date !== null && $end_date !== null){
                $q->whereBetween('activities.date',[$start_date,$end_date]);
            }
        })
        ->groupBy("activities.date");
        $inQuery = clone $query;
        $outQuery = clone $query;
        $in = $inQuery->where("kas.type",'=','in')->get();
        $out = $outQuery->where("kas.type",'=','out')->get();
        $data['in'] = $in;
        $data["out"] = $out;
        return $this->response($data,"Data Retrieved",200);
    }
}
