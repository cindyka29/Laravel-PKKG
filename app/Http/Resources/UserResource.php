<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'phone' => $this->phone,
            'role' => $this->role,
            "jatabatan" => $this->jabatan,
            'is_active' => $this->is_active,
            'image' => $this->image == null ? url("/assets/images/default_user.jpg") : url($this->image),
            "updated_at" => $this->updated_at
        ];
    }
}
