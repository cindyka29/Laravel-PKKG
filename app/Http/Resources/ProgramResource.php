<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "note" => $this->note,
            $this->mergeWhen(isset($this->image),[
                "image" => $this->whenLoaded("image",new ImageResources($this->image))
            ]),
            $this->mergeWhen(isset($this->activities),[
                "activities" => $this->whenLoaded("activities",ActivityResources::collection($this->activities))
            ]),
            "updated_at" => $this->updated_at
        ];
    }
}
