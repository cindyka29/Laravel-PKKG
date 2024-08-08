<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'note' => $this->note,
            'date' => $this->date,
            'program_id' => $this->program_id,
            $this->mergeWhen($this->relationLoaded('program'),[
                "program" => new ProgramResource($this->whenLoaded("program"))
            ]),
            $this->mergeWhen($this->relationLoaded('documentations'),[
                "documentations" => ImageResources::collection($this->whenLoaded("documentations"))
            ]),
            "updated_at" => $this->updated_at
        ];
    }
}
