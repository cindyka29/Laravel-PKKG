<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema
 */
class IuranRequest extends FormRequest
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
            'user_id' => 'required',
            'activity_id' => 'required',
            'is_paid' => 'required|boolean'
        ];
    }

    /**
     * @OA\Property  (
     *     required={"true"}
     * )
     * @var string
     */
    private string $user_id;

    /**
     * @OA\Property  (
     *     required={"true"}
     * )
     * @var string
     */
    private string $activity_id;

    /**
     * @OA\Property  (
     *     required={"true"}
     * )
     * @var bool
     */
    private bool $is_paid;
}
