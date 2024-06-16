<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema
 */
class ActivityRequest extends FormRequest
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
            "name" => 'required',
            "date" => 'required|date_format:Y-m-d'
        ];
    }

    /**
     * @OA\Property (
     *     required={"true"}
     * )
     * @var string
     */
    private string $name;

    /**
     * @OA\Property (
     *     required={"true"},
     *     description="Y-m-d"
     * )
     * @var string
     */
    private string $date;

    /**
     * @OA\Property
     * @var string
     */
    private string $note;
}
