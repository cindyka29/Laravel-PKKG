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
            "program" => $this->whenLoaded("program",new ProgramResource($this->program),null),
            "documentations" => $this->whenLoaded("documentations",ImageResources::collection($this->documentations),[]),
            "updated_at" => $this->updated_at
        ];
    }
}
