<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "is_attended" => $this->is_attended,
            "user_id" => $this->user_id,
            $this->mergeWhen($this->relationLoaded('user'),[
                'user' => new UserResource($this->whenLoaded("user"))
            ]),
            "activity_id" => $this->activity_id,
            $this->mergeWhen($this->relationLoaded('activity'),[
                "activity" => new ActivityResources($this->whenLoaded("activity"))
            ]),
            "updated_at" => $this->updated_at
        ];
    }
}
