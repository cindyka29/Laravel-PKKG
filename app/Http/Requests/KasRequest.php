<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema
 */
class KasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "activity_id" => 'required',
            "date" => 'required|date_format:Y-m-d',
            "type" => "required|in:in,out",
            'image' => 'required|mimes:jpg,jpeg,png,bmp|max:4096',
            'nominal' => 'required|numeric'
        ];
    }

    /**
     * @OA\Property (
     *     required={"true"}
     * )
     * @var string
     */
    private string $activity_id;

    /**
     * @OA\Property
     * @var string
     */
    private string $keterangan;

    /**
     * @OA\Property
     * @var string
     */
    private string $tujuan;

    /**
     * @OA\Property (
     *     required={"true"},
     *     description="Format: Y-m-d"
     * )
     * @var string
     */
    private string $date;

    /**
     * @OA\Property (
     *     required={"true"}
     * )
     * @var float
     */
    private float $nominal;

    /**
     * @OA\Property (
     *     required={"true"},
     *     description="in || out"
     * )
     * @var string
     */
    private string $type;

    /**
     * @OA\Property (
     *     required={"true"},
     *     format="binary"
     * )
     * @var string
     */
    private string $image;
}
