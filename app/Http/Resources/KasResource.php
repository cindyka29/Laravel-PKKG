<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KasResource extends JsonResource
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
            'activity_id' => $this->activity_id,
            $this->mergeWhen($this->relationLoaded('activity'),[
                "activity" => $this->whenLoaded("activity",new ActivityResources($this->activity))
            ]),
            'keterangan' => $this->keterangan,
            'tujuan' => $this->tujuan,
            'date' => $this->date,
            "nominal" => $this->nominal,
            "type" => $this->type,
            $this->mergeWhen($this->relationLoaded('image'),[
                "image" => $this->whenLoaded("image",new ImageResources($this->image))
            ]),
        ];
    }
}
