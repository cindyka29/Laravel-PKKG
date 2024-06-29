<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema
 */
class ProgramRequest extends FormRequest
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
        ];
    }

    /**
     * @OA\Property  (
     *     required={"true"}
     * )
     * @var string
     */
    private string $name;

    /**
     * @OA\Property
     * @var string
     */
    private string $note;

    /**
     * @OA\Property (
     *     format="binary"
     * )
     * @var string
     */
    private string $image;
}
