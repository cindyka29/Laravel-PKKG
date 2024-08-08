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
                "image" => new ImageResources($this->whenLoaded("image"))
            ]),
            $this->mergeWhen(isset($this->activities),[
                "activities" => ActivityResources::collection($this->whenLoaded("activities"))
            ]),
            "updated_at" => $this->updated_at
        ];
    }
}
