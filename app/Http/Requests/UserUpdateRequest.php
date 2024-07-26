<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema
 */
class UserUpdateRequest extends FormRequest
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
            "username" => "required|unique:users,username,".$this->id,
            "phone" => "required|unique:users,phone,".$this->id,
            "name" => "required",
            "role" => "required|in:admin,user",
            "jabatan" => "required_if:role,user"
        ];

    }

    /**
     * @OA\Property (
     *     example="PUT"
     * )
     * @var string
     */
    private string $_method;

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

    /**
     * @OA\Property
     * @var string
     */
    private string $password;

    /**
     * @OA\Property
     * @var string
     */
    private string $confirm_password;
}
