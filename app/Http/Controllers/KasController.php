<?php

namespace App\Http\Controllers;

use App\Exports\KasExportXls;
use App\Http\Requests\KasRequest;
use App\Http\Resources\ActivityResources;
use App\Http\Resources\KasResource;
use App\Models\Activity;
use App\Models\Images;
use App\Models\Kas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class KasController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Get   (
     *     path="/kas",
     *     tags={"Kas"},
     *     operationId="index-kas",
     *     summary="Get List Kas",
     *     description="Get List Kas",
     *     @OA\Parameter (
     *          name="month",
     *          required=false,
     *          description="Format: Y-m",
     *          in="query",
     *          @OA\Schema (
     *              type="string",
     *          ),
     *      ),
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
    public function index(Request $request)  : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            "month" => 'date_format:Y-m'
        ]);
        if ($validator->fails()){
            return $this->response($validator->getMessageBag(),"Invalid Input",400);
        }
        $month = $request->month ?? null;
        $kas = Kas::with(["image","activity"])->where(function ($q) use ($month){
            if($month && $month != ""){
                $q->whereRaw("DATE_FORMAT(date,'%Y-%m') = '$month'");
            }
        })->get();
        $data["records"] = KasResource::collection($kas);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     * @param KasRequest $request
     * @return JsonResponse
     * @OA\Post (
     *     path="/kas",
     *     tags={"Kas"},
     *     operationId="store-kas",
     *     summary="Add new Kas",
     *     description="Add new Kas",
     *     @OA\RequestBody (
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema (
     *                  type="object",
     *                  ref="#/components/schemas/KasRequest"
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
    public function store(KasRequest $request) : JsonResponse
    {
        $file_url = null;
        if($request->hasFile('image')){
            $hash = Str::random(30);
            $extension = '.'.$request->file('image')->guessExtension();
            $filenameClient = $hash.$extension;
            $request->file('image')->storeAs('images/kas', $filenameClient, $disk = 'app-public');
            $file_path = Storage::disk('app-public')->path('images/kas', true). '/'.$filenameClient;
            if (file_exists($file_path)) {
                $file_url = '/images/kas'.'/'.$filenameClient;
            }
        }
        $kas = new Kas;
        $kas->id = Str::uuid();
        $kas->activity_id = $request->activity_id;
        $kas->keterangan = $request->keterangan;
        $kas->tujuan = $request->tujuan;
        $kas->date = $request->date;
        $kas->nominal = $request->nominal;
        $kas->type = $request->type;
        $kas->save();

        if ($file_url !== null){
            $image = new Images;
            $image->name = "Bukti Kas Tanggal: ".$request->date;
            $image->url = $file_url;
            $kas->image()->save($image);
        }
        $kas = Kas::with(["image","activity"])->findOrFail($kas->id);
        $data['record'] = new KasResource($kas);
        return $this->response($data,"Record Saved",200);
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @OA\Get  (
     *     path="/kas/{id}",
     *     tags={"Kas"},
     *     operationId="show-kas",
     *     summary="Get Kas by ID",
     *     description="Get Kas by ID",
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
        $kas = Kas::with(["image","activity"])->findOrFail($id);
        return $this->response(["record" => new KasResource($kas)],"Data Retrieved",200);
    }

    /**
     * @param KasRequest $request
     * @param string $id
     * @return JsonResponse
     * @OA\Post  (
     *     path="/kas-update/{id}",
     *     tags={"Kas"},
     *     operationId="update-kas",
     *     summary="Update Kas by ID",
     *     description="Update Kas by ID",
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
     *              mediaType="multipart/form-data",
     *              @OA\Schema (
     *                  type="object",
     *                  ref="#/components/schemas/KasRequest"
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
    public function update(KasRequest $request, string $id) : JsonResponse
    {
        $kas = Kas::with("image")->findOrFail($id);
        $image = $kas->image;
        $file_url = $image->url;
        if($request->hasFile('image')){
            $hash = Str::random(30);
            $extension = '.'.$request->file('image')->guessExtension();
            $filenameClient = $hash.$extension;
            $request->file('image')->storeAs('images/kas', $filenameClient, $disk = 'app-public');
            $file_path = Storage::disk('app-public')->path('images/kas', true). '/'.$filenameClient;
            if (file_exists($file_path)) {
                $file_url = '/images/kas'.'/'.$filenameClient;
            }
        }
        $kas->activity_id = $request->activity_id;
        $kas->keterangan = $request->keterangan;
        $kas->tujuan = $request->tujuan;
        $kas->date = $request->date;
        $kas->nominal = $request->nominal;
        $kas->type = $request->type;
        $kas->save();

        $image->name = "Bukti Kas Tanggal: ".$request->date;
        $image->url = $file_url;
        $image->save();

        $kas = Kas::with(["image","activity"])->findOrFail($kas->id);
        $data['record'] = new KasResource($kas);
        return $this->response($data,"Record Updated",200);
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @OA\Delete   (
     *     path="/kas/{id}",
     *     tags={"Kas"},
     *     operationId="delete-kas",
     *     summary="Delete Kas by ID",
     *     description="Delete Kas by ID",
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
    public function destroy(string $id)
    {
        $kas = Kas::whereId($id)->firstOrFail();
        $kas->delete();
        return $this->response(null,"Data Deleted",200);
    }

    /**
     * @param $activity_id
     * @return JsonResponse
     * @OA\Get   (
     *     path="/kas/activity/{activity_id}",
     *     tags={"Kas"},
     *     operationId="get-kas-activity",
     *     summary="Get Kas by Activity ID",
     *     description="Get Kas by Activity ID",
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
    public function getByActivityId($activity_id) : JsonResponse
    {
        $activity = Activity::whereId($activity_id)->firstOrFail();
        $kas = Kas::with(["image"])->where("activity_id","=",$activity_id)->get();
        $data["activity"] = new ActivityResources($activity);
        $data["kas"] = KasResource::collection($kas);
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Get   (
     *     path="/kas/month",
     *     tags={"Kas"},
     *     operationId="get-kas-month",
     *     summary="Get Kas Months",
     *     description="Get Kas Months",
     *     @OA\Parameter (
     *          name="year",
     *          required=false,
     *          description="YYYY(2005)",
     *          in="query",
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
    public function getMonthKas(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            "year" => "date_format:Y|nullable"
        ]);
        if($validator->fails()){
            return $this->response($validator->getMessageBag(),"Invalid Input",400);
        }
        $year = $request->year;
        $months = Kas::selectRaw("DISTINCT(DATE_FORMAT(date,'%M %Y')) as text, DATE_FORMAT(date,'%Y-%m') as value")
        ->where(function($q) use ($year){
            if ($year && $year != ""){
                $q->whereRaw("DATE_FORMAT(date,'%Y') = '$year'");
            }
        })->get();
        $data["records"] = $months;
        return $this->response($data,"Data Retrieved",200);
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @OA\Get   (
     *     path="/kas/month/xls",
     *     tags={"Kas"},
     *     operationId="get-kas-month-export",
     *     summary="Get Report Kas To Excel",
     *     description="Get Report Kas To Excel",
     *     @OA\Parameter (
     *          name="month",
     *          required=true,
     *          description="YYYY-mm(2005-05)",
     *          in="query",
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
    public function exportKas(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        $validator = Validator::make($request->all(),[
            "month" => "required|date_format:Y-m"
        ]);
        if ($validator->fails()){
            return $this->response($validator->getMessageBag(),"invalid input",400);
        }
        $records = Kas::with("activity")->whereRaw("DATE_FORMAT(date,'%Y-%m') = '$request->month'")->get();
        $date = Carbon::createFromFormat("Y-m",$request->month)->format("F Y");
        return Excel::download(new KasExportXls($date,$records),'Laporan-Kas-'.$date.'.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Get   (
     *     path="/kas/report",
     *     tags={"Kas"},
     *     operationId="get-kas-report",
     *     summary="Get Report Kas",
     *     description="Get Report",
     *     @OA\Parameter (
     *          name="start_date",
     *          required=false,
     *          description="YYYY-mm-dd(2005-05-23)",
     *          in="query",
     *          @OA\Schema (
     *              type="string"
     *          ),
     *     ),
     *     @OA\Parameter (
     *          name="end_date",
     *          required=false,
     *          description="YYYY-mm-dd(2005-05-23)",
     *          in="query",
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
    public function reportKas(Request $request) : JsonResponse
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
        $query = Kas::selectRaw("sum(nominal) as nominal, date")
            ->where(function($q) use ($start_date,$end_date){
                if ($start_date !== null && $end_date !== null){
                    $q->whereBetween('date',[$start_date,$end_date]);
                }
            })
            ->groupBy("date");
        $in = $query->where("type",'=','in')->get();
        $out = $query->where("type",'=','out')->get();
        $data['in'] = $in;
        $data["out"] = $out;
        return $this->response($data,"Data Retrieved",200);
    }
}
