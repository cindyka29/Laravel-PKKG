<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     title="AuthRequest"
 * )
 */
class AuthRequest extends FormRequest
{
    /**
     * @OA\Property (
     *     title="Phone",
     *     required={"true"}
     * )
     * @var string
     */
    private string $phone;

    /**
     * @OA\Property (
     *     title="Password",
     *     required={"true"}
     * )
     * @var string
     */
    private string $password;
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
            'phone' => 'required',
            'password' => 'required'
        ];
    }
}
