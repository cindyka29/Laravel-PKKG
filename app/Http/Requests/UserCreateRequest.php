<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema
 */
class UserCreateRequest extends FormRequest
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
            "name" => "required",
            "username" => "required|unique:users,username",
            "phone" => "required|unique:users,phone",
            "password" => "required|min:6|max:16|confirmed",
            "role" => 'required|in:admin,user',
            "jabatan" => 'required_if:role,user'
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
     *     required={"true"}
     * )
     * @var string
     */
    private string $username;

    /**
     * @OA\Property (
     *     required={"true"}
     * )
     * @var string
     */
    private string $phone;

    /**
     * @OA\Property (
     *     required={"true"}
     * )
     * @var string
     */
    private string $password;

    /**
     * @OA\Property (
     *     required={"true"}
     * )
     * @var string
     */
    private string $password_confirmation;

    /**
     * @OA\Property (
     *     format="binary"
     * )
     * @var string
     */
    private string $image;
    /**
     * @OA\Property(
     *     description="admin || user",
     *     required={"true"}
     * )
     * @var string
     */
    private string $role;

    /**
     * @OA\Property
     * @var string
     */
    private string $jabatan;
}
