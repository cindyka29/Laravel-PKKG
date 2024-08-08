<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IuranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "is_paid" => $this->is_paid,
            "user_id" => $this->user_id,
            $this->mergeWhen(isset($this->user),[
                'user' => $this->whenLoaded("user",new UserResource($this->user))
            ]),
            "activity_id" => $this->activity_id,
            $this->mergeWhen(isset($this->activity),[
                "activity" => $this->whenLoaded("activity",new ActivityResources($this->activity))
            ]),
            "updated_at" => $this->updated_at
        ];
    }
}
