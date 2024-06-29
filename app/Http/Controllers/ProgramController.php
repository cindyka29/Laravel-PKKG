<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramRequest;
use App\Http\Resources\ProgramResource;
use App\Models\Images;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    /**
     * @return JsonResponse
     * @OA\Get   (
     *     path="/program",
     *     tags={"Program"},
     *     operationId="index-program",
     *     summary="Get List Program",
     *     description="Get List Program",
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
    public function index() : JsonResponse
    {
        $program = Program::with("image")->get();
        return $this->response(['records' => ProgramResource::collection($program)],"Data Retrieved",200);
    }

    /**
     * @param ProgramRequest $request
     * @return JsonResponse
     * @OA\Post (
     *     path="/program",
     *     tags={"Program"},
     *     operationId="store-program",
     *     summary="Add new Program",
     *     description="Add new Program",
     *     @OA\RequestBody (
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema (
     *                  type="object",
     *                  ref="#/components/schemas/ProgramRequest"
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
    public function store(ProgramRequest $request) : JsonResponse
    {
        $file_url = null;
        if($request->hasFile('image')){
            $request->validate([
                'image' => 'required|mimes:jpg,jpeg,png,bmp |max:4096',
            ]);
            $hash = Str::random(30);
            $extension = '.'.$request->file('image')->guessExtension();
            $filenameClient = $hash.$extension;
            $request->file('image')->storeAs('images/programs', $filenameClient, $disk = 'app-public');
            $file_path = Storage::disk('app-public')->path('images/programs', true). '/'.$filenameClient;
            if (file_exists($file_path)) {
                $file_url = '/images/programs'.'/'.$filenameClient;
            }
        }
        $program = new Program;
        $program->id = Str::uuid();
        $program->name = $request->name;
        $program->note = $request->note;
        $program->save();

        if ($file_url !== null){
            $image = new Images;
            $image->name = $request->name;
            $image->url = $file_url;
            $program->image()->save($image);
        }
        $program = Program::with("image")->findOrFail($program->id);
        return $this->response(['record' => new ProgramResource($program)],"Record Saved",200);
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @OA\Get  (
     *     path="/program/{id}",
     *     tags={"Program"},
     *     operationId="show-program",
     *     summary="Get Program by ID",
     *     description="Get Program by ID",
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
        $program = Program::with(["image","activities"])->findOrFail($id);
        return $this->response(['record' => new ProgramResource($program)],"Data Retrieved",200);
    }

    /**
     * @param ProgramRequest $request
     * @param string $id
     * @return JsonResponse
     * @OA\Post  (
     *     path="/program-update/{id}",
     *     tags={"Program"},
     *     operationId="update-program",
     *     summary="Update Program by ID",
     *     description="Update Program by ID",
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
     *                  ref="#/components/schemas/ProgramRequest"
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
    public function update(ProgramRequest $request, string $id) : JsonResponse
    {
        $program = Program::with("image")->where("id",'=',$id)->firstOrFail();
        $image = $program->image;
        $file_url = $image->url;
        if($request->hasFile('image')){
            $request->validate([
                'image' => 'required|mimes:jpg,jpeg,png,bmp |max:4096',
            ]);
            $hash = Str::random(30);
            $extension = '.'.$request->file('image')->guessExtension();
            $filenameClient = $hash.$extension;
            $request->file('image')->storeAs('images/programs', $filenameClient, $disk = 'app-public');
            $file_path = Storage::disk('app-public')->path('images/programs', true). '/'.$filenameClient;
            if (file_exists($file_path)) {
                $file_url = '/images/programs'.'/'.$filenameClient;
            }
        }
        $program->name = $request->name;
        $program->note = $request->note;
        $program->save();
        $image->name = $request->name;
        $image->url = $file_url;
        $image->save();
        $program = Program::with("image")->findOrFail($program->id);
        return $this->response(['record' => new ProgramResource($program)],"Record Updated",200);
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @OA\Delete   (
     *     path="/program/{id}",
     *     tags={"Program"},
     *     operationId="delete-program",
     *     summary="Delete Program by ID",
     *     description="Delete Program by ID",
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
        $program = Program::whereId($id)->firstOrFail();
        $program->delete();
        return $this->response(null,"Record Deleted",200);
    }
}
